<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
        'is_banned',
        'banned_at',
        'ban_reason'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'is_banned' => 'boolean',
            'banned_at' => 'datetime'
        ];
    }

    protected static function booted()
    {
        // Automatically assign default role to new users (but not during testing)
        static::created(function ($user) {
            if (app()->environment('testing')) {
                return; // Skip in tests
            }
            
            if (!$user->is_super_admin && $user->roles()->count() === 0) {
                $defaultRole = Role::where('is_default', true)->first();
                if ($defaultRole) {
                    $user->roles()->attach($defaultRole->id);
                }
            }
        });
    }

    /**
     * Roles that belong to this user
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Direct permissions that belong to this user
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    /**
     * Assign a role to the user
     */
    public function assignRole(Role|string $role): self
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $this->roles()->syncWithoutDetaching([$role->id]);
        
        return $this;
    }

    /**
     * Remove a role from the user
     */
    public function removeRole(Role|string $role): self
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $this->roles()->detach($role->id);
        
        return $this;
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    /**
     * Check if user has all of the given roles
     */
    public function hasAllRoles(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->count() === count($roles);
    }

    /**
     * Give permission directly to user
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
     * Revoke permission from user
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
     * Check if user can perform a specific action
     */
    public function can($ability, $arguments = []): bool
    {
        // Super admins can do anything
        if ($this->is_super_admin) {
            return true;
        }

        // Banned users can't do anything
        if ($this->is_banned) {
            return false;
        }

        // Check direct permissions first
        if ($this->permissions()->where('name', $ability)->exists() || 
            $this->permissions()->where('name', '*')->exists()) {
            return true;
        }

        // Check role-based permissions
        foreach ($this->roles as $role) {
            if ($role->hasPermissionTo($ability)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all permissions for this user (direct + role-based)
     */
    public function getAllPermissions()
    {
        $directPermissions = $this->permissions;
        $rolePermissions = $this->roles->flatMap->permissions;
        
        return $directPermissions->merge($rolePermissions)->unique('id');
    }

    /**
     * Ban the user
     */
    public function ban(string $reason = null): self
    {
        $this->update([
            'is_banned' => true,
            'banned_at' => now(),
            'ban_reason' => $reason
        ]);

        // Assign banned role
        $bannedRole = Role::where('name', 'banned')->first();
        if ($bannedRole) {
            $this->roles()->sync([$bannedRole->id]);
        }

        return $this;
    }

    /**
     * Unban the user
     */
    public function unban(): self
    {
        $this->update([
            'is_banned' => false,
            'banned_at' => null,
            'ban_reason' => null
        ]);

        // Remove banned role and assign default role
        $this->roles()->detach();
        $defaultRole = Role::getDefault();
        if ($defaultRole) {
            $this->assignRole($defaultRole);
        }

        return $this;
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the topics created by the user.
     */
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    /**
     * Get the posts created by the user.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the user's post count.
     */
    public function getPostCountAttribute()
    {
        return $this->posts()->count();
    }
}
