<?php

namespace App\Notifications;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CampaignApprovedNotification extends Notification
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
            'type' => 'campaign_approved',
            'campaign_id' => $this->campaign->id,
            'title' => $this->campaign->title,
            'message' => 'Your campaign was approved and is now live.',
            'url' => route('campaigns.show', $this->campaign),
        ];
    }
}


