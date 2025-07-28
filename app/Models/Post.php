<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
            
            // Create notifications for topic followers (only for replies, not first posts)
            // TODO: Enable when TopicFollowNotification is ready
            // if (!$post->is_first_post) {
            //     TopicFollowNotification::createForNewReply($post);
            // }
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
     * Get the file attachments for this post.
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(FileAttachment::class, 'attachable');
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
        // Process quotes and basic formatting
        $content = $this->parseQuotes($this->content);
        $this->content_html = nl2br(e($content));
        $this->save();
    }

    /**
     * Parse and format quoted content in BBCode-style format.
     */
    private function parseQuotes($content)
    {
        // Pattern to match [quote=username]content[/quote]
        $pattern = '/\[quote=([^\]]+)\](.*?)\[\/quote\]/s';
        
        return preg_replace_callback($pattern, function ($matches) {
            $author = htmlspecialchars($matches[1]);
            $quotedContent = trim($matches[2]);
            
            return sprintf(
                '<div class="bg-gray-50 dark:bg-gray-800 border-l-4 border-blue-400 p-3 my-3 rounded-r">
                    <div class="text-sm text-blue-600 dark:text-blue-400 font-semibold mb-2">%s wrote:</div>
                    <div class="text-sm text-gray-700 dark:text-gray-300 italic">%s</div>
                </div>',
                $author,
                nl2br(htmlspecialchars($quotedContent))
            );
        }, $content);
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
