<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Forum extends Model
{
    /** @use HasFactory<\Database\Factories\ForumFactory> */
    use HasFactory;

    /**
     * Attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'sort_order',
        'is_active',
        'topics_count',
        'posts_count',
        'last_post_id',
        'last_post_at',
    ];

    /**
     * Attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'last_post_at' => 'datetime',
    ];

    /**
     * Get the category that owns the forum.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the topics for the forum.
     */
    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }

    /**
     * Get the last post for the forum.
     */
    public function lastPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'last_post_id');
    }

    /**
     * Scope to get active forums.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get topics for the forum with pagination.
     */
    public function getTopicsWithPagination($perPage = 20)
    {
        return $this->topics()
            ->with(['user', 'lastPost.user'])
            ->orderBy('is_sticky', 'desc')
            ->orderBy('last_post_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Update forum statistics.
     */
    public function updateStats()
    {
        $this->topics_count = $this->topics()->count();
        $this->posts_count = Post::whereIn('topic_id', $this->topics()->pluck('id'))->count();
        
        $lastPost = Post::whereIn('topic_id', $this->topics()->pluck('id'))
            ->latest()
            ->first();
            
        if ($lastPost) {
            $this->last_post_id = $lastPost->id;
            $this->last_post_at = $lastPost->created_at;
        }
        
        $this->save();
    }
}
