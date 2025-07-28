<?php

namespace App\Policies;

use App\Models\FileAttachment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FileAttachmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FileAttachment $fileAttachment): bool
    {
        return true; // Files are generally viewable by all authenticated users
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can upload files
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FileAttachment $fileAttachment): bool
    {
        return $user->id === $fileAttachment->user_id || $user->hasPermission('manage_attachments');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FileAttachment $fileAttachment): bool
    {
        return $user->id === $fileAttachment->user_id || $user->hasPermission('manage_attachments');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FileAttachment $fileAttachment): bool
    {
        return $user->hasPermission('manage_attachments');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FileAttachment $fileAttachment): bool
    {
        return $user->hasPermission('manage_attachments');
    }
}
