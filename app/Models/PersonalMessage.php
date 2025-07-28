<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PersonalMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'content',
        'content_html',
        'sender_id',
        'is_draft',
        'sent_at',
    ];

    protected $casts = [
        'is_draft' => 'boolean',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the sender of the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get all participants of this message.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(PersonalMessageParticipant::class, 'message_id');
    }

    /**
     * Get recipients (participants with type 'to').
     */
    public function recipients(): HasMany
    {
        return $this->participants()->where('type', 'to');
    }

    /**
     * Get CC recipients.
     */
    public function ccRecipients(): HasMany
    {
        return $this->participants()->where('type', 'cc');
    }

    /**
     * Get BCC recipients.
     */
    public function bccRecipients(): HasMany
    {
        return $this->participants()->where('type', 'bcc');
    }

    /**
     * Get all users who are participants in this message.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'personal_message_participants', 'message_id', 'user_id')
            ->withPivot(['type', 'is_read', 'read_at', 'is_deleted', 'deleted_at', 'is_archived', 'archived_at'])
            ->withTimestamps();
    }

    /**
     * Check if message is read by a specific user.
     */
    public function isReadBy(User $user): bool
    {
        return $this->participants()
            ->where('user_id', $user->id)
            ->where('is_read', true)
            ->exists();
    }

    /**
     * Mark message as read by a specific user.
     */
    public function markAsReadBy(User $user): void
    {
        $this->participants()
            ->where('user_id', $user->id)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Check if message is deleted by a specific user.
     */
    public function isDeletedBy(User $user): bool
    {
        return $this->participants()
            ->where('user_id', $user->id)
            ->where('is_deleted', true)
            ->exists();
    }

    /**
     * Mark message as deleted by a specific user.
     */
    public function markAsDeletedBy(User $user): void
    {
        $this->participants()
            ->where('user_id', $user->id)
            ->update([
                'is_deleted' => true,
                'deleted_at' => now(),
            ]);
    }

    /**
     * Get file attachments for this message.
     */
    public function fileAttachments()
    {
        return $this->morphMany(FileAttachment::class, 'attachable');
    }

    /**
     * Scope to get messages for a specific user.
     */
    public function scopeForUser($query, User $user)
    {
        return $query->whereHas('participants', function ($q) use ($user) {
            $q->where('user_id', $user->id)->where('is_deleted', false);
        });
    }

    /**
     * Scope to get unread messages for a specific user.
     */
    public function scopeUnreadForUser($query, User $user)
    {
        return $query->whereHas('participants', function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('is_read', false)
              ->where('is_deleted', false);
        });
    }

    /**
     * Scope to get sent messages (where user is sender).
     */
    public function scopeSentBy($query, User $user)
    {
        return $query->where('sender_id', $user->id);
    }

    /**
     * Scope to get received messages (where user is participant but not sender).
     */
    public function scopeReceivedBy($query, User $user)
    {
        return $query->whereHas('participants', function ($q) use ($user) {
            $q->where('user_id', $user->id)->where('is_deleted', false);
        })->where('sender_id', '!=', $user->id);
    }

    /**
     * Scope to exclude drafts.
     */
    public function scopeNotDrafts($query)
    {
        return $query->where('is_draft', false);
    }

    /**
     * Scope to get only drafts.
     */
    public function scopeDrafts($query)
    {
        return $query->where('is_draft', true);
    }
}
