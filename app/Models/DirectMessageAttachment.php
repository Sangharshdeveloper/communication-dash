<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DirectMessageAttachment extends Model
{
    protected $fillable = [
        'message_id', 'original_name', 'stored_name', 'mime_type', 'size', 'path',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(DirectMessage::class, 'message_id');
    }

    public function getUrl(): string
    {
        return route('direct-chat.attachment.download', $this->id);
    }

    public function getSizeHumanAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes < 1024)       return $bytes . ' B';
        if ($bytes < 1048576)    return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }
}