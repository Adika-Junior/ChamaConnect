<?php

namespace App\Notifications;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CampaignGoalReachedNotification extends Notification
{
    use Queueable;

    public function __construct(public Campaign $campaign)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'campaign_goal_reached',
            'campaign_id' => $this->campaign->id,
            'title' => $this->campaign->title,
            'message' => 'Congratulations! Your campaign reached its goal.',
            'url' => route('campaigns.show', $this->campaign),
        ];
    }
}


