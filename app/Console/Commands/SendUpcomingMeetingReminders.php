<?php

namespace App\Console\Commands;

use App\Models\Meeting;
use App\Notifications\UpcomingMeetingNotification;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class SendUpcomingMeetingReminders extends Command
{
    protected $signature = 'meetings:send-reminders {--window=30 : Minutes ahead to remind}';
    protected $description = 'Send reminders for meetings starting soon to participants and organizer';

    public function handle(): int
    {
        $window = (int) $this->option('window');
        $now = CarbonImmutable::now();
        $until = $now->addMinutes($window);

        $meetings = Meeting::with(['participants', 'organizer'])
            ->whereBetween('scheduled_at', [$now, $until])
            ->whereIn('status', ['scheduled', null])
            ->get();

        $count = 0;
        foreach ($meetings as $meeting) {
            $recipients = $meeting->participants; // assumes many-to-many users
            if ($meeting->organizer) {
                $recipients = $recipients->push($meeting->organizer)->unique('id');
            }
            foreach ($recipients as $user) {
                $user->notify(new UpcomingMeetingNotification($meeting));
                $count++;
            }
        }

        $this->info("Sent {$count} reminders for {$meetings->count()} meeting(s).");
        return self::SUCCESS;
    }
}


