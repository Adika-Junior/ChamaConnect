<?php

namespace App\Console\Commands;

use App\Models\Contribution;
use App\Notifications\ContributionClosedNotification;
use Illuminate\Console\Command;

class CloseDueContributions extends Command
{
    protected $signature = 'contributions:close-due';
    protected $description = 'Close contributions whose deadline has passed and send summaries';

    public function handle(): int
    {
        $now = now();
        $affected = 0;
        Contribution::with(['meeting.participants', 'organizer'])
            ->whereIn('status', ['approved', 'active'])
            ->whereNotNull('deadline')
            ->where('deadline', '<=', $now)
            ->chunkById(100, function ($batch) use (&$affected) {
                foreach ($batch as $c) {
                    $c->status = 'closed';
                    $c->closed_at = now();
                    $c->save();

                    // Notify organizer and meeting participants
                    $notifiables = collect([$c->organizer])->filter();
                    if ($c->meeting) {
                        $notifiables = $notifiables->merge($c->meeting->participants);
                    }
                    $notifiables->unique('id')->each(function ($user) use ($c) {
                        $user->notify(new ContributionClosedNotification($c));
                    });
                    $affected++;
                }
            });
        $this->info("Closed {$affected} contributions.");
        return self::SUCCESS;
    }
}


