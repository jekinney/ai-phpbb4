<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'user_id',
        'content',
        'content_html',
        'user_ip',
        'is_first_post',
        'edited_at',
        'edited_by',
    ];

    protected $casts = [
        'is_first_post' => 'boolean',
        'edited_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($post) {
            $post->topic->updateStats();
        });

        static::deleted(function ($post) {
            $post->topic->updateStats();
        });
    }

    /**
     * Get the topic that owns the post.
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get the user that created the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who edited the post.
     */
    public function editedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    /**
     * Check if the post was edited.
     */
    public function wasEdited(): bool
    {
        return !is_null($this->edited_at);
    }

    /**
     * Mark the post as edited.
     */
    public function markAsEdited(User $user)
    {
        $this->edited_at = now();
        $this->edited_by = $user->id;
        $this->save();
    }

    /**
     * Process the content and generate HTML.
     */
    public function processContent()
    {
        // Basic processing - you can expand this with BBCode, Markdown, etc.
        $this->content_html = nl2br(e($this->content));
        $this->save();
    }

    /**
     * Get posts for a topic with user info.
     */
    public static function getTopicPosts($topicId, $perPage = 15)
    {
        return static::where('topic_id', $topicId)
            ->with(['user', 'editedBy'])
            ->orderBy('created_at')
            ->paginate($perPage);
    }

    /**
     * Create a new post and handle related updates.
     */
    public static function createPost(array $data)
    {
        $post = static::create($data);
        $post->processContent();
        
        return $post;
    }
}
