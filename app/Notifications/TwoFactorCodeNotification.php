<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorCodeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $code
    ) {}

    public function via($notifiable): array
    {
        $channels = ['database'];
        
        // Send SMS if phone number is available
        if ($notifiable->phone) {
            $channels[] = 'sms';
        }
        
        // Also send email as backup
        $channels[] = 'mail';
        
        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Two-Factor Authentication Code')
            ->line('Your two-factor authentication code is:')
            ->line("**{$this->code}**")
            ->line('This code will expire in 10 minutes.')
            ->line('If you did not request this code, please ignore this message.');
    }

    public function toSms($notifiable): string
    {
        return "Your 2FA code is: {$this->code}. Valid for 10 minutes. Do not share this code.";
    }

    public function toArray($notifiable): array
    {
        return [
            'code' => $this->code,
            'expires_at' => now()->addMinutes(10)->toDateTimeString(),
        ];
    }
}

