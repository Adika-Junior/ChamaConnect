<?php

namespace App\Notifications;

use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MeetingReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Meeting $meeting,
        public int $minutesBefore
    ) {}

    public function via($notifiable): array
    {
        $channels = ['database'];
        
        $preference = $notifiable->getNotificationPreference('meeting_reminder');
        
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
        $meetingTime = $this->meeting->scheduled_at?->format('l, F j, Y \a\t g:i A');
        
        return (new MailMessage)
            ->subject("Meeting Reminder: {$this->meeting->title}")
            ->line("This is a reminder that you have a meeting in {$this->minutesBefore} minutes.")
            ->line("**Meeting:** {$this->meeting->title}")
            ->line("**Time:** {$meetingTime}")
            ->line("**Type:** " . ucfirst($this->meeting->type))
            ->action('View Meeting', route('meetings.show', $this->meeting))
            ->line('Thank you for using our platform!');
    }

    public function toSms($notifiable): string
    {
        $meetingTime = $this->meeting->scheduled_at?->format('M j, g:i A');
        return "Reminder: Meeting '{$this->meeting->title}' in {$this->minutesBefore} minutes ({$meetingTime}).";
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'meeting_reminder',
            'meeting_id' => $this->meeting->id,
            'meeting_title' => $this->meeting->title,
            'scheduled_at' => $this->meeting->scheduled_at?->toDateTimeString(),
            'minutes_before' => $this->minutesBefore,
            'message' => "Meeting '{$this->meeting->title}' starts in {$this->minutesBefore} minutes.",
        ];
    }
}

