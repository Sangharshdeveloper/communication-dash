<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Session Driver
    | Using Redis for performance and security (CBUAE requirement)
    |--------------------------------------------------------------------------
    */
    'driver' => env('SESSION_DRIVER', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime — 30 minutes max (CBUAE: 15-30 min)
    | Actual inactivity timeout enforced by InactivityTimeout middleware
    |--------------------------------------------------------------------------
    */
    'lifetime' => env('SESSION_LIFETIME', 130),
    'expire_on_close' => false,

    /*
    |--------------------------------------------------------------------------
    | Session Encryption — required for CBUAE compliance
    |--------------------------------------------------------------------------
    */
    'encrypt' => env('SESSION_ENCRYPT', true),

    'files' => storage_path('framework/sessions'),

    'connection' => env('SESSION_CONNECTION'),
    'table'      => 'sessions',
    'store'      => env('SESSION_STORE'),
    'lottery'    => [2, 100],

    /*
    |--------------------------------------------------------------------------
    | Session Cookie — Strict security settings
    |--------------------------------------------------------------------------
    */
    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'cbuae'), '_') . '_session'
    ),

    'path'      => '/',
    'domain'    => env('SESSION_DOMAIN'),
    'secure'    => env('SESSION_SECURE_COOKIE', true),   // HTTPS only
    'http_only' => true,                                  // No JS access
    'same_site' => env('SESSION_SAME_SITE', 'strict'),   // CSRF protection
    'partitioned' => false,
];
