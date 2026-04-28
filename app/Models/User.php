<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'mobile', 
        'role',
        'is_active',
        'is_verified',
        'last_login_at',
        'last_login_ip',
        'failed_login_attempts',
        'locked_until',
        'password',
        'bitrix_agent_id',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active'            => 'boolean',
            'is_verified'          => 'boolean',
            'last_login_at'        => 'datetime',
            'locked_until'         => 'datetime',
            'email_verified_at'    => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function magicTokens(): HasMany
    {
        return $this->hasMany(MagicLoginToken::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // ── Role helpers ──────────────────────────────────────────────────────────

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isAgent(): bool    { return $this->role === 'agent'; }
    public function isAuditor(): bool  { return $this->role === 'auditor'; }
    public function isCustomer(): bool { return $this->role === 'customer'; }

    // ── Security helpers ──────────────────────────────────────────────────────

    public function isAccountLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function incrementFailedAttempts(): void
    {
        $this->increment('failed_login_attempts');
        if ($this->failed_login_attempts >= 5) {
            $this->update(['locked_until' => now()->addMinutes(30)]);
        }
    }

    public function resetFailedAttempts(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until'          => null,
        ]);
    }

    public function recordLogin(string $ip): void
    {
        $this->update([
            'last_login_at'  => now(),
            'last_login_ip'  => $ip,
        ]);
        $this->resetFailedAttempts();
    }
}
