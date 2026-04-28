<?php

// Suppress PHP 8.5+ deprecation warnings for Laravel's internal PDO MySQL constants
error_reporting(E_ALL & ~E_DEPRECATED);

use App\Http\Middleware\ForceHttps;
use App\Http\Middleware\InactivityTimeout;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api:      __DIR__.'/../routes/api.php',
        apiPrefix: 'api',     
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Global middleware
        $middleware->append(ForceHttps::class);
        $middleware->append(SecurityHeaders::class);

        // Web group additions
        $middleware->web(append: [
            InactivityTimeout::class,
        ]);

        // Named middleware aliases
        $middleware->alias([
            'role'               => RoleMiddleware::class,
            'inactivity.timeout' => InactivityTimeout::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (\Illuminate\Routing\Exceptions\InvalidSignatureException $e) {
            return redirect()->route('login')->withErrors([
                'link' => 'This magic link is invalid or has expired. Please request a new one.',
            ]);
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e) {
            return redirect()->route('login')
                ->with('warning', 'Please log in to continue.');
        });
    })
    ->create();