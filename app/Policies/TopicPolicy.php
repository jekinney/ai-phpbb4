<?php

namespace App\Policies;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TopicPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return $user ? $user->can('view_topics') : true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Topic $topic): bool
    {
        return $user ? $user->can('view_topics') : true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_topics');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Topic $topic): bool
    {
        // User can edit their own topics if they have edit_own_topics permission
        if ($user->id === $topic->user_id && $user->can('edit_own_topics')) {
            return true;
        }
        
        // Or if they have edit_all_topics permission
        return $user->can('edit_all_topics');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Topic $topic): bool
    {
        // User can delete their own topics if they have delete_own_topics permission
        if ($user->id === $topic->user_id && $user->can('delete_own_topics')) {
            return true;
        }
        
        // Or if they have delete_all_topics permission
        return $user->can('delete_all_topics');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Topic $topic): bool
    {
        return $user->can('moderate_topics');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Topic $topic): bool
    {
        return $user->can('delete_all_topics');
    }

    /**
     * Determine whether the user can moderate the topic.
     */
    public function moderate(User $user, Topic $topic): bool
    {
        return $user->can('moderate_topics');
    }

    /**
     * Determine whether the user can lock/unlock the topic.
     */
    public function lock(User $user, Topic $topic): bool
    {
        return $user->can('lock_topics');
    }

    /**
     * Determine whether the user can make topic sticky.
     */
    public function sticky(User $user, Topic $topic): bool
    {
        return $user->can('sticky_topics');
    }

    /**
     * Determine whether the user can move the topic.
     */
    public function move(User $user, Topic $topic): bool
    {
        return $user->can('move_topics');
    }
}
