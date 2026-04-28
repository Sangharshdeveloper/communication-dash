<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CBUAE Compliance: Enforce HTTPS for all connections.
 */
class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('magic_link.force_https') && ! $request->secure() && app()->environment('production')) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
