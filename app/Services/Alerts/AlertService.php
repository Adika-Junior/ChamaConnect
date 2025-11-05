<?php

namespace App\Services\Alerts;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AlertService
{
    public function notify(string $title, string $message): void
    {
        $sent = false;

        $webhook = (string) config('services.alerts.slack_webhook_url');
        if (!empty($webhook)) {
            try {
                Http::post($webhook, [
                    'text' => "{$title}\n{$message}",
                ]);
                $sent = true;
            } catch (\Throwable $e) {
                Log::warning('Slack alert failed', ['error' => $e->getMessage()]);
            }
        }

        $emailTo = (string) config('services.alerts.email_to');
        if (!$sent && !empty($emailTo)) {
            try {
                Notification::route('mail', $emailTo)->notify(new class($title, $message) extends \Illuminate\Notifications\Notification {
                    public function __construct(private string $title, private string $message) {}
                    public function via($notifiable) { return ['mail']; }
                    public function toMail($notifiable) {
                        return (new MailMessage)
                            ->subject($this->title)
                            ->line($this->message);
                    }
                });
                $sent = true;
            } catch (\Throwable $e) {
                Log::warning('Email alert failed', ['error' => $e->getMessage()]);
            }
        }

        if (!$sent) {
            Log::error('Alert not delivered', ['title' => $title, 'message' => $message]);
        }
    }
}


