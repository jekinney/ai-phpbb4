<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_users');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Users can view their own profile, or if they have view_users permission
        return $user->id === $model->id || $user->can('view_users');
    }

    /**
     * Determine whether the user can view detailed user information.
     */
    public function viewDetails(User $user, User $model): bool
    {
        // Users can view their own details, or if they have view_user_details permission
        return $user->id === $model->id || $user->can('view_user_details');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('manage_users');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Users can update their own profile, or if they have manage_users permission
        return $user->id === $model->id || $user->can('manage_users');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Can't delete yourself, but can delete others if you have manage_users permission
        return $user->id !== $model->id && $user->can('manage_users');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->can('manage_users');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->can('manage_users');
    }

    /**
     * Determine whether the user can ban other users.
     */
    public function ban(User $user, User $model): bool
    {
        // Can't ban yourself, but can ban others if you have ban_users permission
        return $user->id !== $model->id && $user->can('ban_users');
    }

    /**
     * Determine whether the user can unban other users.
     */
    public function unban(User $user, User $model): bool
    {
        return $user->can('ban_users');
    }

    /**
     * Determine whether the user can edit roles of other users.
     */
    public function editRoles(User $user, User $model): bool
    {
        // Can't edit your own roles (prevents privilege escalation)
        return $user->id !== $model->id && $user->can('edit_user_roles');
    }
}
