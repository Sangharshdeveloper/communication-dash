<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\MagicLoginToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OtpNotification;

class OtpService
{
    public function __construct(
        private readonly AuditService $auditService
    ) {}

    /**
     * Generate and send OTP for suspicious login verification.
     * OTP is 6 digits, valid for 5 minutes.
     */
    public function sendOtp(MagicLoginToken $token, User $user, Request $request): void
    {
        $otp     = random_int(100000, 999999); // 6-digit OTP
        $otpHash = hash('sha256', (string) $otp);

        $token->update([
            'otp_hash'       => $otpHash,
            'otp_expires_at' => now()->addMinutes(5),
            'otp_verified'   => false,
        ]);

        // Send OTP via email (can also add SMS)
        $user->notify(new OtpNotification($otp));

        $this->auditService->log(
            action:      AuditLog::ACTION_OTP_SENT,
            request:     $request,
            user:        $user,
            description: 'OTP sent due to suspicious login activity'
        );
    }

    /**
     * Verify the submitted OTP.
     */
    public function verifyOtp(MagicLoginToken $token, string $submittedOtp, Request $request): bool
    {
        $user = $token->user;

        if (! $token->otp_hash || ! $token->otp_expires_at) {
            return false;
        }

        // Check OTP expiry
        if ($token->otp_expires_at->isPast()) {
            $this->auditService->log(
                action:      AuditLog::ACTION_OTP_FAILED,
                request:     $request,
                user:        $user,
                status:      AuditLog::STATUS_FAILURE,
                description: 'OTP verification failed: expired'
            );
            return false;
        }

        $submittedHash = hash('sha256', $submittedOtp);

        if (! hash_equals($token->otp_hash, $submittedHash)) {
            $user->incrementFailedAttempts();
            $this->auditService->log(
                action:      AuditLog::ACTION_OTP_FAILED,
                request:     $request,
                user:        $user,
                status:      AuditLog::STATUS_FAILURE,
                description: 'OTP verification failed: incorrect code'
            );
            return false;
        }

        $token->update(['otp_verified' => true]);

        $this->auditService->log(
            action:      AuditLog::ACTION_OTP_VERIFIED,
            request:     $request,
            user:        $user,
            description: 'OTP verified successfully'
        );

        return true;
    }
}
