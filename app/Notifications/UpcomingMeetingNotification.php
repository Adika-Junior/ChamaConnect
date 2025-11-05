<?php

namespace App\Notifications;

use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpcomingMeetingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Meeting $meeting)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $when = optional($this->meeting->scheduled_at)->format('Y-m-d H:i');
        $url = route('meetings.show', $this->meeting);

        return (new MailMessage)
            ->subject('Reminder: Upcoming Meeting')
            ->greeting('Hi '.$notifiable->name)
            ->line('You have an upcoming meeting: '.$this->meeting->title)
            ->line('When: '.$when)
            ->action('View Meeting', $url)
            ->line('See you there.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'meeting_id' => $this->meeting->id,
            'title' => $this->meeting->title,
            'scheduled_at' => (string) $this->meeting->scheduled_at,
        ];
    }
}


