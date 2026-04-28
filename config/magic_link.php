<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Magic Link Expiry (CBUAE: 10-15 minutes)
    |--------------------------------------------------------------------------
    */
    'expiry_minutes' => (int) env('MAGIC_LINK_EXPIRY_MINUTES', 10),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limit'       => (int) env('MAGIC_LINK_RATE_LIMIT', 5),
    'rate_limit_decay' => (int) env('MAGIC_LINK_RATE_LIMIT_DECAY', 60),

    /*
    |--------------------------------------------------------------------------
    | Inactivity Auto-Logout (CBUAE: 15-30 minutes)
    |--------------------------------------------------------------------------
    */
    'inactivity_timeout_minutes' => (int) env('INACTIVITY_TIMEOUT_MINUTES', 15),

    /*
    |--------------------------------------------------------------------------
    | Force HTTPS (CBUAE Requirement)
    |--------------------------------------------------------------------------
    */
    'force_https' => (bool) env('FORCE_HTTPS', true),

    /*
    |--------------------------------------------------------------------------
    | Suspicious Activity Detection
    | When IP subnet or device fingerprint changes, require OTP
    |--------------------------------------------------------------------------
    */
    'suspicious_activity_otp' => (bool) env('SUSPICIOUS_ACTIVITY_OTP', true),

    /*
    |--------------------------------------------------------------------------
    | Token byte length (64 bytes = 512-bit security)
    |--------------------------------------------------------------------------
    */
    'token_bytes' => 64,

];
