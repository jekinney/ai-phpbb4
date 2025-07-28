<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopicFollowNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'topic_id',
        'post_id',
        'reply_author_id',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user who should receive the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the topic the notification is about.
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get the post that triggered the notification.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the user who wrote the reply.
     */
    public function replyAuthor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reply_author_id');
    }

    /**
     * Scope to get unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Create notifications for topic followers when a new reply is posted.
     */
    public static function createForNewReply(Post $post): void
    {
        $topic = $post->topic;
        $followers = $topic->getNotifiableFollowers();

        foreach ($followers as $follow) {
            // Don't notify the author of their own post
            if ($follow->user_id == $post->user_id) {
                continue;
            }

            self::create([
                'user_id' => $follow->user_id,
                'topic_id' => $topic->id,
                'post_id' => $post->id,
                'reply_author_id' => $post->user_id,
            ]);

            // Update the follow record's last notification time
            $follow->updateLastNotified();
        }
    }
}
