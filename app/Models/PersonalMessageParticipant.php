<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalMessageParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'user_id',
        'type',
        'is_read',
        'read_at',
        'is_deleted',
        'deleted_at',
        'is_archived',
        'archived_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'is_deleted' => 'boolean',
        'deleted_at' => 'datetime',
        'is_archived' => 'boolean',
        'archived_at' => 'datetime',
    ];

    /**
     * Get the message this participant belongs to.
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(PersonalMessage::class, 'message_id');
    }

    /**
     * Get the user this participant represents.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get unread participants.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get read participants.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope to get non-deleted participants.
     */
    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }

    /**
     * Scope to get recipients (type 'to').
     */
    public function scopeRecipients($query)
    {
        return $query->where('type', 'to');
    }

    /**
     * Scope to get CC recipients.
     */
    public function scopeCc($query)
    {
        return $query->where('type', 'cc');
    }

    /**
     * Scope to get BCC recipients.
     */
    public function scopeBcc($query)
    {
        return $query->where('type', 'bcc');
    }
}
