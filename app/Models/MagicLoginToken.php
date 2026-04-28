<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MagicLoginToken extends Model
{
    protected $fillable = [
        'user_id',
        'token_hash',
        'expires_at',
        'is_used',
        'used_at',
        'created_ip',
        'used_ip',
        'created_user_agent',
        'used_user_agent',
        'device_fingerprint',
        'otp_required',
        'otp_hash',
        'otp_expires_at',
        'otp_verified',
        'invalidated_reason',
        'invalidated_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at'      => 'datetime',
            'used_at'         => 'datetime',
            'otp_expires_at'  => 'datetime',
            'invalidated_at'  => 'datetime',
            'is_used'         => 'boolean',
            'otp_required'    => 'boolean',
            'otp_verified'    => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return ! $this->is_used && ! $this->isExpired() && ! $this->invalidated_at;
    }

    public function markAsUsed(string $ip, string $userAgent): void
    {
        $this->update([
            'is_used'           => true,
            'used_at'           => now(),
            'used_ip'           => $ip,
            'used_user_agent'   => $userAgent,
            'invalidated_reason'=> 'used',
            'invalidated_at'    => now(),
        ]);
    }

    public function revoke(string $reason = 'manually_revoked'): void
    {
        $this->update([
            'invalidated_reason' => $reason,
            'invalidated_at'     => now(),
        ]);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeValid($query)
    {
        return $query->where('is_used', false)
            ->where('expires_at', '>', now())
            ->whereNull('invalidated_at');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
