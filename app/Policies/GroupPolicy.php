<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Group;

class GroupPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view groups
    }

    public function view(User $user, Group $group): bool
    {
        return $group->isMember($user) || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return true; // All authenticated users can create groups
    }

    public function update(User $user, Group $group): bool
    {
        return $group->hasRole($user, 'admin') || $group->hasRole($user, 'treasurer') || $user->isAdmin();
    }

    public function delete(User $user, Group $group): bool
    {
        return $group->hasRole($user, 'admin') || $user->isAdmin();
    }
}

