<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DirectMessage extends Model
{
    protected $fillable = [
        'session_id', 'sender_id', 'body', 'type', 'is_read', 'read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(DirectChatSession::class, 'session_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(DirectMessageAttachment::class, 'message_id');
    }
}