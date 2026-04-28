<?php

namespace App\Services;

use App\Mail\MagicLinkMail;
use App\Models\AuditLog;
use App\Models\MagicLoginToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class MagicLinkService
{
    public function __construct(
        private readonly AuditService $auditService
    ) {}

    /**
     * Generate a cryptographically secure magic link for a user.
     * Invalidates all existing unused tokens first.
     */
    public function generate(User $user, Request $request): string
    {
        // Revoke any previous unused tokens for this user
        MagicLoginToken::forUser($user->id)
            ->valid()
            ->get()
            ->each(fn ($t) => $t->revoke('superseded'));

        // Generate a 64-byte (512-bit) cryptographically secure random token
        $rawToken = bin2hex(random_bytes(64)); // 128 hex chars

        // Store only the SHA-256 hash - never the raw token
        $tokenHash = hash('sha256', $rawToken);

        $expiryMinutes = (int) config('magic_link.expiry_minutes', 10);

        $token = MagicLoginToken::create([
            'user_id'            => $user->id,
            'token_hash'         => $tokenHash,
            'expires_at'         => now()->addMinutes($expiryMinutes),
            'is_used'            => false,
            'created_ip'         => $request->ip(),
            'created_user_agent' => $request->userAgent(),
            'device_fingerprint' => $this->generateDeviceFingerprint($request),
        ]);

        // Build the signed URL with the raw token
        $url = URL::temporarySignedRoute(
            'auth.magic.verify',
            now()->addMinutes($expiryMinutes),
            ['token' => $rawToken, 'uid' => $user->id]
        );

        $this->auditService->log(
            action:      AuditLog::ACTION_LINK_GENERATED,
            request:     $request,
            user:        $user,
            description: "Magic link generated for {$user->email}",
            metadata:    ['token_id' => $token->id, 'expires_at' => $token->expires_at]
        );

        return $url;
    }

    /**
     * Send the magic link email.
     */
    public function send(User $user, string $magicUrl, Request $request): void
    {
        Mail::to($user->email)->send(new MagicLinkMail($user, $magicUrl));

        $this->auditService->log(
            action:      AuditLog::ACTION_EMAIL_SENT,
            request:     $request,
            user:        $user,
            description: "Magic link email dispatched to {$user->email}"
        );
    }

    /**
     * Validate and consume a magic token.
     * Returns the User if valid, null otherwise.
     */
    public function validate(string $rawToken, int $userId, Request $request): ?User
    {
        $tokenHash = hash('sha256', $rawToken);

        $tokenRecord = MagicLoginToken::where('token_hash', $tokenHash)
            ->where('user_id', $userId)
            ->first();

        // Token not found
        if (! $tokenRecord) {
            $this->auditService->log(
                action:      AuditLog::ACTION_LOGIN_FAILED,
                request:     $request,
                status:      AuditLog::STATUS_FAILURE,
                description: 'Magic link token not found',
                metadata:    ['user_id' => $userId]
            );
            return null;
        }

        $user = $tokenRecord->user;

        // Token already used
        if ($tokenRecord->is_used) {
            $this->auditService->log(
                action:      AuditLog::ACTION_LOGIN_FAILED,
                request:     $request,
                user:        $user,
                status:      AuditLog::STATUS_FAILURE,
                description: 'Attempted reuse of already-used magic link',
                metadata:    ['token_id' => $tokenRecord->id]
            );
            return null;
        }

        // Token expired
        if ($tokenRecord->isExpired()) {
            $tokenRecord->revoke('expired');
            $this->auditService->log(
                action:      AuditLog::ACTION_TOKEN_EXPIRED,
                request:     $request,
                user:        $user,
                status:      AuditLog::STATUS_FAILURE,
                description: 'Magic link token has expired',
                metadata:    ['token_id' => $tokenRecord->id, 'expired_at' => $tokenRecord->expires_at]
            );
            return null;
        }

        // Token revoked
        if ($tokenRecord->invalidated_at) {
            $this->auditService->log(
                action:      AuditLog::ACTION_LOGIN_FAILED,
                request:     $request,
                user:        $user,
                status:      AuditLog::STATUS_FAILURE,
                description: "Magic link revoked: {$tokenRecord->invalidated_reason}"
            );
            return null;
        }

        // Account checks
        if (! $user->is_active) {
            $this->auditService->log(
                action:      AuditLog::ACTION_LOGIN_FAILED,
                request:     $request,
                user:        $user,
                status:      AuditLog::STATUS_FAILURE,
                description: 'Login attempt on inactive account'
            );
            return null;
        }

        if ($user->isAccountLocked()) {
            $this->auditService->log(
                action:      AuditLog::ACTION_LOGIN_FAILED,
                request:     $request,
                user:        $user,
                status:      AuditLog::STATUS_FAILURE,
                description: 'Login attempt on locked account',
                metadata:    ['locked_until' => $user->locked_until]
            );
            return null;
        }

        // Check for suspicious activity (IP/device change)
        if ($this->isSuspiciousRequest($tokenRecord, $request)) {
            $tokenRecord->update(['otp_required' => true]);
            $this->auditService->log(
                action:      AuditLog::ACTION_SUSPICIOUS_ACTIVITY,
                request:     $request,
                user:        $user,
                status:      AuditLog::STATUS_WARNING,
                description: 'Suspicious login attempt: IP/device mismatch detected',
                metadata:    [
                    'original_ip'  => $tokenRecord->created_ip,
                    'current_ip'   => $request->ip(),
                    'token_id'     => $tokenRecord->id,
                ]
            );
            // OTP required — return special marker via session
            session(['otp_required_token_id' => $tokenRecord->id]);
            return null;
        }

        // All valid — consume the token
        $tokenRecord->markAsUsed($request->ip(), $request->userAgent() ?? '');

        $user->recordLogin($request->ip());

        $this->auditService->log(
            action:      AuditLog::ACTION_LOGIN_SUCCESS,
            request:     $request,
            user:        $user,
            description: "Successful magic link login for {$user->email}",
            metadata:    ['token_id' => $tokenRecord->id]
        );

        return $user;
    }

    /**
     * Detect suspicious activity based on IP or device fingerprint change.
     */
    private function isSuspiciousRequest(MagicLoginToken $token, Request $request): bool
    {
        // Check for IP change (cross-network access)
        if ($token->created_ip !== $request->ip()) {
            // Allow same /24 subnet (same office/home network)
            $originalSubnet = substr($token->created_ip, 0, strrpos($token->created_ip, '.'));
            $currentSubnet  = substr($request->ip(), 0, strrpos($request->ip(), '.'));

            if ($originalSubnet !== $currentSubnet) {
                return true;
            }
        }

        // Check device fingerprint
        $currentFingerprint = $this->generateDeviceFingerprint($request);
        if ($token->device_fingerprint && $token->device_fingerprint !== $currentFingerprint) {
            return true;
        }

        return false;
    }

    /**
     * Generate a device fingerprint from request attributes.
     */
    private function generateDeviceFingerprint(Request $request): string
    {
        $data = implode('|', [
            $request->userAgent() ?? '',
            $request->header('Accept-Language') ?? '',
            $request->header('Accept-Encoding') ?? '',
        ]);

        return hash('sha256', $data);
    }
}
