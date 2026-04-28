<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * CBUAE Compliance: Auto logout after inactivity (15–30 minutes).
 */
class InactivityTimeout
{
    public function __construct(private readonly AuditService $auditService) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $timeoutSeconds = config('magic_link.inactivity_timeout_minutes', 15) * 60;
            $lastActivity   = session('last_activity', time());

            if ((time() - $lastActivity) > $timeoutSeconds) {
                $user = Auth::user();

                $this->auditService->log(
                    action:      AuditLog::ACTION_LOGOUT,
                    request:     $request,
                    user:        $user,
                    description: "Session auto-expired due to inactivity for {$user->email}",
                    metadata:    ['inactivity_seconds' => time() - $lastActivity]
                );

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('warning', 'Your session has expired due to inactivity. Please log in again.');
            }

            // Refresh last activity
            session(['last_activity' => time()]);
        }

        return $next($request);
    }
}
