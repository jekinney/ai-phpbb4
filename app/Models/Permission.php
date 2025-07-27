<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'category'
    ];

    /**
     * Override create method to prevent creation outside of seeders
     */
    public static function create(array $attributes = [])
    {
        // Only allow creation during seeding or in console
        if (!app()->runningInConsole() && !defined('SEEDING_PERMISSIONS')) {
            throw new \Exception('Permissions can only be created via seeders');
        }
        
        return parent::create($attributes);
    }

    /**
     * Roles that have this permission
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * Users that have this permission directly
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permissions');
    }

    /**
     * Check if permission applies to all actions (wildcard)
     */
    public function isWildcard(): bool
    {
        return $this->name === '*';
    }

    /**
     * Scope to get permissions by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
