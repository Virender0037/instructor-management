<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Message extends Model
{
    protected $fillable = [
        'sender_id', 'receiver_id', 'subject', 'body', 'sent_at', 'read_at',
        'sender_deleted_at', 'receiver_deleted_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'sender_deleted_at' => 'datetime',
        'receiver_deleted_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function attachments(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'message_attachments', 'message_id', 'document_id')
            ->withTimestamps();
    }
}
