<?php

namespace App\Policies;

use App\Models\Contribution;
use App\Models\User;

class ContributionPolicy
{
    public function view(User $user, Contribution $contribution): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($contribution->organizer_id === $user->id || $contribution->approver_id === $user->id) {
            return true;
        }
        if ($contribution->meeting && $contribution->meeting->participants()->where('users.id', $user->id)->exists()) {
            return true;
        }
        return false;
    }

    public function contribute(User $user, Contribution $contribution): bool
    {
        return $this->view($user, $contribution) && $contribution->isApproved() || $user->id === $contribution->organizer_id;
    }

    public function update(User $user, Contribution $contribution): bool
    {
        return $user->isAdmin() || $user->id === $contribution->organizer_id;
    }

    public function delete(User $user, Contribution $contribution): bool
    {
        return $user->isAdmin() || $user->id === $contribution->organizer_id;
    }
}


