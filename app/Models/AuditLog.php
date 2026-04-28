<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public const UPDATED_AT = null; // immutable - no updated_at

    // CBUAE-required action types
    public const ACTION_LINK_GENERATED       = 'link_generated';
    public const ACTION_EMAIL_SENT           = 'email_sent';
    public const ACTION_LOGIN_SUCCESS        = 'login_success';
    public const ACTION_LOGIN_FAILED         = 'login_failed';
    public const ACTION_LOGOUT               = 'logout';
    public const ACTION_TOKEN_EXPIRED        = 'token_expired';
    public const ACTION_TOKEN_REVOKED        = 'token_revoked';
    public const ACTION_OTP_SENT             = 'otp_sent';
    public const ACTION_OTP_VERIFIED         = 'otp_verified';
    public const ACTION_OTP_FAILED           = 'otp_failed';
    public const ACTION_SUSPICIOUS_ACTIVITY  = 'suspicious_activity';
    public const ACTION_ACCOUNT_LOCKED       = 'account_locked';
    public const ACTION_RATE_LIMITED         = 'rate_limited';

    public const STATUS_SUCCESS  = 'success';
    public const STATUS_FAILURE  = 'failure';
    public const STATUS_WARNING  = 'warning';

    protected $fillable = [
        'user_id',
        'email',
        'action',
        'status',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
        'country_code',
        'request_id',
        'session_id',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
