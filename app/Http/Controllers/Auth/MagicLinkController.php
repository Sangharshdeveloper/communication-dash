<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\MagicLinkRequest;
use App\Http\Requests\OtpVerifyRequest;
use App\Models\AuditLog;
use App\Models\MagicLoginToken;
use App\Models\User;
use App\Services\AuditService;
use App\Services\MagicLinkService;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MagicLinkController extends Controller
{
    public function __construct(
        private readonly MagicLinkService $magicLinkService,
        private readonly OtpService       $otpService,
        private readonly AuditService     $auditService,
    ) {}

    // ── Show login form ────────────────────────────────────────────────────────

    public function showLoginForm(): View
    {
        return view('auth.staff-login');
    }

    // ── Step 1: Request magic link ─────────────────────────────────────────────

    /**
     * POST /auth/magic-link
     * Rate limited: 5 requests per 60 seconds per IP
     */
    public function requestLink(MagicLinkRequest $request): RedirectResponse
    {
        $email = strtolower(trim($request->validated('email')));

        /** @var User|null $user */
        $user = User::where('email', $email)->where('is_active', true)->first();

        // Security: Do not reveal whether email exists (timing-safe)
        if (! $user) {
            // Log attempt but don't reveal user doesn't exist
            $this->auditService->log(
                action:      AuditLog::ACTION_LINK_GENERATED,
                request:     $request,
                status:      AuditLog::STATUS_WARNING,
                description: 'Magic link requested for unknown email',
                email:       $email
            );
            // Same response as success to prevent user enumeration
            return $this->linkSentResponse($email);
        }

        if ($user->isAccountLocked()) {
            $this->auditService->log(
                action:      AuditLog::ACTION_LOGIN_FAILED,
                request:     $request,
                user:        $user,
                status:      AuditLog::STATUS_FAILURE,
                description: 'Magic link request on locked account'
            );
            return back()->withErrors([
                'email' => 'Your account is temporarily locked. Please try again later or contact support.',
            ]);
        }

        // Generate and send magic link
        $magicUrl = $this->magicLinkService->generate($user, $request);
        $this->magicLinkService->send($user, $magicUrl, $request);

        return $this->linkSentResponse($email);
    }

    // ── Step 2: Verify magic link ──────────────────────────────────────────────

    /**
     * GET /auth/magic-link/verify?token=...&uid=...&signature=...
     */
    public function verifyLink(Request $request): RedirectResponse|View
    {
        // Laravel checks the signature automatically via the signed middleware
        $rawToken = $request->query('token');
        $userId   = (int) $request->query('uid');

        if (! $rawToken || ! $userId) {
            return $this->invalidLinkResponse('Invalid or malformed magic link.');
        }

        $user = $this->magicLinkService->validate($rawToken, $userId, $request);

        // Check if OTP is required (suspicious activity detected)
        if (! $user && session()->has('otp_required_token_id')) {
            $tokenId = session('otp_required_token_id');
            $token   = MagicLoginToken::find($tokenId);

            if ($token && $token->isValid()) {
                $this->otpService->sendOtp($token, $token->user, $request);
                session(['otp_token_id' => $tokenId]);
                session()->forget('otp_required_token_id');

                return redirect()->route('auth.otp.form')
                    ->with('info', 'Suspicious activity detected. Please verify your identity with the OTP sent to your email.');
            }
        }

        if (! $user) {
            return $this->invalidLinkResponse();
        }

        return $this->loginUser($user, $request);
    }

    // ── OTP Verification (suspicious activity) ────────────────────────────────

    public function showOtpForm(): View
    {
        abort_unless(session()->has('otp_token_id'), 403, 'No OTP session found.');
        return view('auth.otp');
    }

    public function verifyOtp(OtpVerifyRequest $request): RedirectResponse
    {
        $tokenId = session('otp_token_id');
        abort_unless($tokenId, 403, 'No OTP session found.');

        $token = MagicLoginToken::find($tokenId);

        if (! $token || ! $token->isValid()) {
            session()->forget('otp_token_id');
            return redirect()->route('login')
                ->withErrors(['otp' => 'Session expired. Please request a new magic link.']);
        }

        $verified = $this->otpService->verifyOtp(
            $token,
            $request->validated('otp'),
            $request
        );

        if (! $verified) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP. Please try again.']);
        }

        // OTP verified — complete the login
        $token->markAsUsed($request->ip(), $request->userAgent() ?? '');
        $user = $token->user;
        $user->recordLogin($request->ip());

        $this->auditService->log(
            action:      AuditLog::ACTION_LOGIN_SUCCESS,
            request:     $request,
            user:        $user,
            description: "Login successful after OTP verification for {$user->email}"
        );

        session()->forget('otp_token_id');

        return $this->loginUser($user, $request);
    }

    // ── Logout ─────────────────────────────────────────────────────────────────

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $this->auditService->log(
            action:      AuditLog::ACTION_LOGOUT,
            request:     $request,
            user:        $user,
            description: "User {$user?->email} logged out"
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been logged out securely.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function loginUser(User $user, Request $request): RedirectResponse
    {
        Auth::login($user);
        $request->session()->regenerate();

        // Set inactivity timeout
        session(['last_activity' => time()]);

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    private function linkSentResponse(string $email): RedirectResponse
    {
        return back()->with('magic_sent',
            "If an account exists for {$email}, a secure login link has been sent. Please check your email. The link expires in " .
            config('magic_link.expiry_minutes', 10) . " minutes."
        );
    }

    private function invalidLinkResponse(string $message = null): RedirectResponse
    {
        return redirect()->route('login')->withErrors([
            'link' => $message ?? 'This magic link is invalid, expired, or has already been used. Please request a new one.',
        ]);
    }
}