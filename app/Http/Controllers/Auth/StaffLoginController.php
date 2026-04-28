<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StaffLoginController extends Controller
{
    public function __construct(private readonly AuditService $auditService) {}

    public function showForm(): View
    {
        return view('auth.staff-login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower(trim($request->email));

        // Rate limit: 5 attempts per minute per IP
        $key = 'staff-login:' . $request->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Too many attempts. Please wait {$seconds} seconds.",
            ])->withInput(['email' => $email]);
        }

        /** @var User|null $user */
        $user = User::where('email', $email)
            ->whereIn('role', ['admin', 'agent', 'auditor'])
            ->where('is_active', true)
            ->first();

        // Wrong email or not a staff account
        if (! $user || ! $user->password) {
            \Illuminate\Support\Facades\RateLimiter::hit($key, 60);
            $this->auditService->log(
                action:      AuditLog::ACTION_LOGIN_FAILED,
                request:     $request,
                status:      AuditLog::STATUS_FAILURE,
                description: 'Staff login failed: account not found',
                email:       $email
            );
            return back()->withErrors([
                'email' => 'These credentials do not match our records.',
            ])->withInput(['email' => $email]);
        }

        // Account locked
        if ($user->isAccountLocked()) {
            $this->auditService->log(
                action:      AuditLog::ACTION_LOGIN_FAILED,
                request:     $request,
                user:        $user,
                status:      AuditLog::STATUS_FAILURE,
                description: 'Staff login attempt on locked account'
            );
            return back()->withErrors([
                'email' => 'Your account is temporarily locked due to too many failed attempts. Please try again later.',
            ])->withInput(['email' => $email]);
        }

        // Wrong password
        if (! Hash::check($request->password, $user->password)) {
            \Illuminate\Support\Facades\RateLimiter::hit($key, 60);
            $user->incrementFailedAttempts();

            $this->auditService->log(
                action:      AuditLog::ACTION_LOGIN_FAILED,
                request:     $request,
                user:        $user,
                status:      AuditLog::STATUS_FAILURE,
                description: "Staff login failed: wrong password (attempt {$user->failed_login_attempts})"
            );

            return back()->withErrors([
                'password' => 'Incorrect password.',
            ])->withInput(['email' => $email]);
        }

        // ✅ Success
        \Illuminate\Support\Facades\RateLimiter::clear($key);
        $user->recordLogin($request->ip());

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        session(['last_activity' => time()]);

        $this->auditService->log(
            action:      AuditLog::ACTION_LOGIN_SUCCESS,
            request:     $request,
            user:        $user,
            description: "Staff login successful for {$user->email} (role: {$user->role})"
        );

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }
}