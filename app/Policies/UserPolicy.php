<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if user can create other users (admin only)
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if user can approve other users (admin only)
     */
    public function approve(User $user, User $targetUser): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if user can update other users
     */
    public function update(User $user, User $targetUser): bool
    {
        return $user->isAdmin() || $user->id === $targetUser->id;
    }

    /**
     * Determine if user can delete other users
     */
    public function delete(User $user, User $targetUser): bool
    {
        return $user->isAdmin() && $user->id !== $targetUser->id;
    }

    /**
     * Determine if user can view other users
     */
    public function view(User $user, User $targetUser): bool
    {
        return $user->isAdmin() || $user->id === $targetUser->id;
    }
}

