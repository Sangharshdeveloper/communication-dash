<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class DirectChatSession extends Model
{
    protected $fillable = [
        'session_token', 'customer_id', 'agent_id',
        'customer_ref', 'bitrix_deal_id','bitrix_deal_link','status', 'last_activity_at', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'last_activity_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(DirectMessage::class, 'session_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
  
     public function lastMsg()
    {
        return $this->hasOne(DirectMessage::class, 'session_id')->latestOfMany('id');
    }

    // public function messages()
    // {
    //     return $this->hasMany(DirectMessage::class, 'session_id');
    // }    
}