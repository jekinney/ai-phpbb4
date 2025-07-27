<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'sort_order',
        'is_active',
    ];

    /**
     * Set db column types for casting.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the forums for the category.
     */
    public function forums(): HasMany
    {
        return $this->hasMany(Forum::class)->orderBy('sort_order');
    }

    /**
     * Get active forums for the category.
     */
    public function activeForums(): HasMany
    {
        return $this->forums()->where('is_active', true);
    }

    /**
     * Scope to get active categories.
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
     * Get all active categories with their active forums.
     */
    public static function getForumsIndex()
    {
        return static::active()
            ->ordered()
            ->with(['activeForums' => function ($query) {
                $query->with(['lastPost.user', 'lastPost.topic']);
            }])
            ->get();
    }
}
