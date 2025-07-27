<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'level',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean'
    ];

    /**
     * Users that have this role
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    /**
     * Permissions that belong to this role
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    /**
     * Assign a permission to this role
     */
    public function givePermissionTo(Permission|string $permission): self
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }

        $this->permissions()->syncWithoutDetaching([$permission->id]);
        
        return $this;
    }

    /**
     * Remove a permission from this role
     */
    public function revokePermissionTo(Permission|string $permission): self
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }

        $this->permissions()->detach($permission->id);
        
        return $this;
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermissionTo(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists() ||
               $this->permissions()->where('name', '*')->exists();
    }

    /**
     * Get the default role for new users
     */
    public static function getDefault(): ?Role
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Scope to get roles by level (higher level = more permissions)
     */
    public function scopeByLevel($query, int $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope to get roles above a certain level
     */
    public function scopeAboveLevel($query, int $level)
    {
        return $query->where('level', '>', $level);
    }
}
