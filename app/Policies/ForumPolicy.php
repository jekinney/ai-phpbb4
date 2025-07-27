<?php

namespace App\Policies;

use App\Models\Forum;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ForumPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // Allow guests and authenticated users to view forums
        return $user ? $user->can('view_forums') : true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Forum $forum): bool
    {
        // Check if forum is hidden and user has permission to view hidden forums
        if ($forum->is_hidden ?? false) {
            return $user && $user->can('view_hidden_forums');
        }
        
        // Allow guests and authenticated users to view public forums
        return $user ? $user->can('view_forums') : true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('manage_forums');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Forum $forum): bool
    {
        return $user->can('manage_forums');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Forum $forum): bool
    {
        return $user->can('manage_forums');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Forum $forum): bool
    {
        return $user->can('manage_forums');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Forum $forum): bool
    {
        return $user->can('manage_forums');
    }
}
