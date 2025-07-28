<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopicFollow extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'topic_id',
        'is_following',
        'email_notifications',
        'web_notifications',
    ];

    protected $casts = [
        'is_following' => 'boolean',
        'email_notifications' => 'boolean', 
        'web_notifications' => 'boolean',
    ];

    /**
     * Get the user that follows the topic.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the topic being followed.
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Follow a topic for a user.
     */
    public static function followTopic(int $userId, int $topicId, bool $isFollowing = true): self
    {
        return self::updateOrCreate(
            ['user_id' => $userId, 'topic_id' => $topicId],
            [
                'is_following' => $isFollowing,
                'email_notifications' => $isFollowing,
                'web_notifications' => $isFollowing,
            ]
        );
    }

    /**
     * Unfollow a topic for a user.
     */
    public static function unfollowTopic(int $userId, int $topicId): bool
    {
        return self::where('user_id', $userId)
            ->where('topic_id', $topicId)
            ->update(['is_following' => false]) > 0;
    }

    /**
     * Check if a user is following a topic.
     */
    public static function isFollowing(int $userId, int $topicId): bool
    {
        return self::where('user_id', $userId)
            ->where('topic_id', $topicId)
            ->where('is_following', true)
            ->exists();
    }

    /**
     * Get followers of a topic.
     */
    public static function getTopicFollowers(int $topicId)
    {
        return self::with('user')
            ->where('topic_id', $topicId)
            ->where('is_following', true)
            ->get();
    }
}