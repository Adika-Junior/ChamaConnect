<?php

namespace App\Notifications;

use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CampaignDonationReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(public Campaign $campaign, public Donation $donation)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'campaign_donation_received',
            'campaign_id' => $this->campaign->id,
            'donation_id' => $this->donation->id,
            'amount' => (float) $this->donation->amount,
            'message' => 'New donation received.',
            'url' => route('campaigns.show', $this->campaign),
        ];
    }
}


