<?php

namespace App\Policies;

use App\Models\System;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SystemPolicy
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
    public function view(User $user, System $system): bool
    {
        return true; // Everyone logged in can see details
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only the Super Admin can register a NEW system
        return $user->email === 'christian.llauderes1296@gmail.com';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, System $system): bool
    {
        // Only the Owner of the system OR the Super Admin can edit it
        return $user->id === $system->user_id || $user->email === 'christian.llauderes1296@gmail.com';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, System $system): bool
    {
        return $user->email === 'christian.llauderes1296@gmail.com'; // Only admin can delete
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, System $system): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, System $system): bool
    {
        return false;
    }
}
