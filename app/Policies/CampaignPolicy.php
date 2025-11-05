<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Campaign;

class CampaignPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Public campaigns can be viewed by anyone
    }

    public function view(User $user, Campaign $campaign): bool
    {
        // Public active campaigns can be viewed by anyone
        if ($campaign->is_public && $campaign->status === 'active') {
            return true;
        }
        
        // Otherwise, only organizer or admin
        return $campaign->organizer_id === $user->id || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return true; // All authenticated users can create campaigns
    }

    public function update(User $user, Campaign $campaign): bool
    {
        // Only organizer or admin can update
        return $campaign->organizer_id === $user->id || $user->isAdmin();
    }

    public function delete(User $user, Campaign $campaign): bool
    {
        // Only organizer or admin can delete
        return $campaign->organizer_id === $user->id || $user->isAdmin();
    }
}

