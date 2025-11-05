<?php

namespace App\Notifications;

use App\Models\Contribution;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContributionClosedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Contribution $contribution) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'contribution_closed',
            'contribution_id' => $this->contribution->id,
            'title' => $this->contribution->title,
            'total' => (float) $this->contribution->collected_amount,
            'target' => (float) $this->contribution->target_amount,
            'closed_at' => optional($this->contribution->closed_at)->toIso8601String(),
        ];
    }
}


