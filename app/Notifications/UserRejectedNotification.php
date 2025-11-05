<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ?string $reason = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Account Registration Status')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We regret to inform you that your account registration was not approved at this time.');

        if ($this->reason) {
            $message->line('Reason: ' . $this->reason);
        }

        $message->line('If you have any questions, please contact the administrator.');

        return $message;
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'user_rejected',
            'reason' => $this->reason,
            'message' => 'Your account registration was not approved.',
        ];
    }
}

