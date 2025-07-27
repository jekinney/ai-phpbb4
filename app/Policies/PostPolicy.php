<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return $user ? $user->can('view_posts') : true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Post $post): bool
    {
        return $user ? $user->can('view_posts') : true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_posts');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        // User can edit their own posts if they have edit_own_posts permission
        if ($user->id === $post->user_id && $user->can('edit_own_posts')) {
            return true;
        }
        
        // Or if they have edit_all_posts permission
        return $user->can('edit_all_posts');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        // Can't delete the first post of a topic (must delete topic instead)
        if ($post->is_first_post) {
            return false;
        }

        // User can delete their own posts if they have delete_own_posts permission
        if ($user->id === $post->user_id && $user->can('delete_own_posts')) {
            return true;
        }
        
        // Or if they have delete_all_posts permission
        return $user->can('delete_all_posts');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Post $post): bool
    {
        return $user->can('moderate_posts');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return $user->can('delete_all_posts');
    }

    /**
     * Determine whether the user can moderate the post.
     */
    public function moderate(User $user, Post $post): bool
    {
        return $user->can('moderate_posts');
    }

    /**
     * Check if user can reply to topic (post must be in an unlocked topic)
     */
    public function reply(User $user, Post $post): bool
    {
        // Check if topic is locked
        if ($post->topic->is_locked) {
            return $user->can('moderate_topics'); // Only moderators can post in locked topics
        }
        
        return $user->can('create_posts');
    }
}
