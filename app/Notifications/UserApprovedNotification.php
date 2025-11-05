<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $approvedBy
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Account Approved')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your account has been approved by ' . $this->approvedBy . '.')
            ->line('You can now log in and access all features of the platform.')
            ->action('Log In', route('login'))
            ->line('Welcome to the team!');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'user_approved',
            'approved_by' => $this->approvedBy,
            'message' => 'Your account has been approved. You can now log in.',
        ];
    }
}

