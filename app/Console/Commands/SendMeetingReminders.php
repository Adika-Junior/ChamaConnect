<?php

namespace App\Console\Commands;

use App\Models\Meeting;
use App\Models\User;
use App\Notifications\MeetingReminderNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendMeetingReminders extends Command
{
    protected $signature = 'meetings:send-reminders {--minutes=15 : Minutes before meeting to send reminder}';
    protected $description = 'Send meeting reminders to participants';

    public function handle()
    {
        $minutes = (int) $this->option('minutes');
        $reminderTime = now()->addMinutes($minutes);

        $meetings = Meeting::where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [
                $reminderTime->copy()->subMinute(),
                $reminderTime->copy()->addMinute(),
            ])
            ->with('participants')
            ->get();

        $reminderCount = 0;

        foreach ($meetings as $meeting) {
            foreach ($meeting->participants as $participant) {
                $user = User::find($participant->user_id);
                
                if ($user && $user->status === 'active') {
                    // Check if reminder already sent for this meeting and minutes
                    $hasReminder = $user->notifications()
                        ->where('type', 'App\\Notifications\\MeetingReminderNotification')
                        ->where('data->meeting_id', $meeting->id)
                        ->where('data->minutes_before', $minutes)
                        ->exists();
                    
                    if (!$hasReminder) {
                        // Check user notification preferences
                        $preference = $user->getNotificationPreference('meeting_reminder');
                        
                        if (!$preference || $preference->in_app || $preference->email || $preference->sms) {
                            // Skip if in quiet hours
                            if (!$preference || !$preference->isQuietHour()) {
                                $user->notify(new MeetingReminderNotification($meeting, $minutes));
                                $reminderCount++;
                            }
                        }
                    }
                }
            }
        }

        $this->info("Sent {$reminderCount} meeting reminders for {$meetings->count()} meetings.");

        return Command::SUCCESS;
    }
}

