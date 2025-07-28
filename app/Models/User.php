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
        'is_banned',
        'banned_at',
        'ban_reason',
        'is_pm_banned',
        'pm_banned_at',
        'pm_ban_reason',
        'pm_banned_by',
        'pm_ban_expires_at'
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
            'is_banned' => 'boolean',
            'banned_at' => 'datetime',
            'is_pm_banned' => 'boolean',
            'pm_banned_at' => 'datetime',
            'pm_ban_expires_at' => 'datetime'
        ];
    }

    protected static function booted()
    {
        // Automatically assign default role to new users (but not during testing)
        static::created(function ($user) {
            if (app()->environment('testing')) {
                return; // Skip in tests
            }
            
            if ($user->roles()->count() === 0) {
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
     * Check if user can perform a specific action via our ACL system
     */
    public function hasPermission($ability): bool
    {
        // Banned users can't do anything
        if ($this->is_banned) {
            return false;
        }

        // Check direct permissions first
        if ($this->permissions()->where('name', $ability)->exists()) {
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
     * Override Laravel's can method to use our ACL system for string abilities
     */
    public function can($ability, $arguments = []): bool
    {
        // If it's a string permission without arguments and it's a known permission, use our ACL system
        if (is_string($ability) && empty($arguments) && !str_contains($ability, '\\')) {
            // Check if this is actually a known permission in our system
            $knownPermission = \App\Models\Permission::where('name', $ability)->exists();
            if ($knownPermission) {
                return $this->hasPermission($ability);
            }
        }
        
        // Otherwise, use Laravel's default Gate system (for policies)
        return parent::can($ability, $arguments);
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
     * Get the file attachments uploaded by the user.
     */
    public function fileAttachments()
    {
        return $this->hasMany(FileAttachment::class);
    }

    /**
     * Get personal messages sent by the user.
     */
    public function sentMessages()
    {
        return $this->hasMany(PersonalMessage::class, 'sender_id');
    }

    /**
     * Get personal messages the user is a participant in.
     */
    public function personalMessages()
    {
        return $this->belongsToMany(PersonalMessage::class, 'personal_message_participants', 'user_id', 'message_id')
            ->withPivot(['type', 'is_read', 'read_at', 'is_deleted', 'deleted_at', 'is_archived', 'archived_at'])
            ->withTimestamps();
    }

    /**
     * Get personal message participants for this user.
     */
    public function messageParticipants()
    {
        return $this->hasMany(PersonalMessageParticipant::class);
    }

    /**
     * Get unread personal messages count.
     */
    public function getUnreadMessagesCountAttribute()
    {
        return $this->messageParticipants()
            ->where('is_read', false)
            ->where('is_deleted', false)
            ->count();
    }

    /**
     * Get the user's post count.
     */
    public function getPostCountAttribute()
    {
        return $this->posts()->count();
    }

    /**
     * Get topics the user is following.
     */
    public function followedTopics()
    {
        return $this->belongsToMany(Topic::class, 'topic_follows')
            ->withPivot(['notify_replies', 'is_active', 'last_notified_at'])
            ->withTimestamps()
            ->wherePivot('is_active', true);
    }

    /**
     * Get topic follows for this user.
     */
    public function topicFollows()
    {
        return $this->hasMany(TopicFollow::class);
    }

    /**
     * Check if user is following a topic.
     */
    public function isFollowingTopic($topicId): bool
    {
        return TopicFollow::isFollowing($this->id, $topicId);
    }

    /**
     * Follow a topic.
     */
    public function followTopic($topicId, $notifyReplies = true): TopicFollow
    {
        return TopicFollow::followTopic($this->id, $topicId, $notifyReplies);
    }

    /**
     * Unfollow a topic.
     */
    public function unfollowTopic($topicId): bool
    {
        return TopicFollow::unfollowTopic($this->id, $topicId);
    }

    /**
     * Get the user who banned this user from PM.
     */
    public function pmBannedBy()
    {
        return $this->belongsTo(User::class, 'pm_banned_by');
    }

    /**
     * Check if user is currently PM banned.
     */
    public function isPmBanned(): bool
    {
        if (!$this->is_pm_banned) {
            return false;
        }

        // Check if ban has expired
        if ($this->pm_ban_expires_at && $this->pm_ban_expires_at->isPast()) {
            $this->removePmBan();
            return false;
        }

        return true;
    }

    /**
     * Check if user can send PMs.
     */
    public function canSendMessages(): bool
    {
        return !$this->isPmBanned() && $this->can('send_messages');
    }

    /**
     * Check if user can receive PMs.
     */
    public function canReceiveMessages(): bool
    {
        return !$this->isPmBanned() && $this->can('receive_messages');
    }

    /**
     * Ban user from PM system.
     */
    public function pmBan(User $bannedBy, string $reason, $expiresAt = null): void
    {
        $this->update([
            'is_pm_banned' => true,
            'pm_banned_at' => now(),
            'pm_ban_reason' => $reason,
            'pm_banned_by' => $bannedBy->id,
            'pm_ban_expires_at' => $expiresAt ? \Carbon\Carbon::parse($expiresAt) : null,
        ]);
    }

    /**
     * Remove PM ban from user.
     */
    public function removePmBan(): void
    {
        $this->update([
            'is_pm_banned' => false,
            'pm_banned_at' => null,
            'pm_ban_reason' => null,
            'pm_banned_by' => null,
            'pm_ban_expires_at' => null,
        ]);
    }

    /**
     * Get PM ban status info.
     */
    public function getPmBanInfo(): ?array
    {
        if (!$this->isPmBanned()) {
            return null;
        }

        return [
            'banned_at' => $this->pm_banned_at,
            'banned_by' => $this->pmBannedBy,
            'reason' => $this->pm_ban_reason,
            'expires_at' => $this->pm_ban_expires_at,
            'is_permanent' => is_null($this->pm_ban_expires_at),
        ];
    }

    /**
     * Get all game scores for this user.
     */
    public function gameScores()
    {
        return $this->hasMany(GameScore::class);
    }

    /**
     * Get leaderboard entries for this user.
     */
    public function gameLeaderboards()
    {
        return $this->hasMany(GameLeaderboard::class);
    }

    /**
     * Get the user's best score for a specific game.
     */
    public function getBestScore(Game $game)
    {
        return $this->gameLeaderboards()
                   ->where('game_id', $game->id)
                   ->first();
    }

    /**
     * Get the user's rank for a specific game.
     */
    public function getGameRank(Game $game): ?int
    {
        $leaderboard = $this->getBestScore($game);
        return $leaderboard ? $leaderboard->rank : null;
    }
}
