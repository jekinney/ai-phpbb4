<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'forum_id',
        'user_id',
        'title',
        'slug',
        'is_sticky',
        'is_locked',
        'views_count',
        'posts_count',
        'last_post_id',
        'last_post_user_id',
        'last_post_at',
    ];

    protected $casts = [
        'is_sticky' => 'boolean',
        'is_locked' => 'boolean',
        'last_post_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($topic) {
            if (empty($topic->slug)) {
                $topic->slug = Str::slug($topic->title);
            }
        });
    }

    /**
     * Get the forum that owns the topic.
     */
    public function forum(): BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }

    /**
     * Get the user that created the topic.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the posts for the topic.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the first post of the topic.
     */
    public function firstPost(): HasMany
    {
        return $this->hasMany(Post::class)->where('is_first_post', true);
    }

    /**
     * Get the last post for the topic.
     */
    public function lastPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'last_post_id');
    }

    /**
     * Get the user who made the last post.
     */
    public function lastPostUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_post_user_id');
    }

    /**
     * Get posts for the topic with pagination.
     */
    public function getPostsWithPagination($perPage = 15)
    {
        return $this->posts()
            ->with(['user'])
            ->orderBy('created_at')
            ->paginate($perPage);
    }

    /**
     * Update topic statistics.
     */
    public function updateStats()
    {
        $this->posts_count = $this->posts()->count();
        
        $lastPost = $this->posts()->latest()->first();
        if ($lastPost) {
            $this->last_post_id = $lastPost->id;
            $this->last_post_user_id = $lastPost->user_id;
            $this->last_post_at = $lastPost->created_at;
        }
        
        $this->save();
        
        // Update forum stats
        $this->forum->updateStats();
    }

    /**
     * Increment views count.
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Get topics for a forum with filters.
     */
    public static function getForumTopics($forumId, $perPage = 20)
    {
        return static::where('forum_id', $forumId)
            ->with(['user', 'lastPost.user'])
            ->orderBy('is_sticky', 'desc')
            ->orderBy('last_post_at', 'desc')
            ->paginate($perPage);
    }
}
