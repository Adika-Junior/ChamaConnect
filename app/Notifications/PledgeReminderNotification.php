<?php

namespace App\Notifications;

use App\Models\Pledge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PledgeReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Pledge $pledge,
        public string $kind // upcoming|due_today|overdue
    ) {}

    public function via($notifiable): array
    {
        $channels = ['database'];

        $preference = $notifiable->getNotificationPreference('pledge_reminder');

        if (!$preference || $preference->email) {
            $channels[] = 'mail';
        }

        if ($preference && $preference->sms && $notifiable->phone) {
            $channels[] = 'sms';
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $due = $this->pledge->due_date?->format('M j, Y');
        $subject = match ($this->kind) {
            'overdue' => 'Overdue Pledge Reminder',
            'due_today' => 'Pledge Due Today Reminder',
            default => 'Upcoming Pledge Reminder',
        };

        $line = match ($this->kind) {
            'overdue' => "Your pledge of KSh {$this->pledge->amount} is overdue (due {$due}).",
            'due_today' => "Your pledge of KSh {$this->pledge->amount} is due today ({$due}).",
            default => "You have an upcoming pledge of KSh {$this->pledge->amount} due on {$due}.",
        };

        return (new MailMessage)
            ->subject($subject)
            ->line($line)
            ->action('View Contribution', route('contributions.show', $this->pledge->contribution))
            ->line('Thank you for your support!');
    }

    public function toSms($notifiable): string
    {
        $due = $this->pledge->due_date?->format('M j');
        return match ($this->kind) {
            'overdue' => "Overdue pledge: KSh {$this->pledge->amount} (due {$due}).",
            'due_today' => "Pledge due today: KSh {$this->pledge->amount}.",
            default => "Pledge reminder: KSh {$this->pledge->amount} due {$due}.",
        };
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'pledge_reminder',
            'pledge_id' => $this->pledge->id,
            'contribution_id' => $this->pledge->contribution_id,
            'amount' => (float) $this->pledge->amount,
            'due_date' => $this->pledge->due_date?->toDateString(),
            'kind' => $this->kind,
            'message' => $this->toSms($notifiable),
        ];
    }
}


