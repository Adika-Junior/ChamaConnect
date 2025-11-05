<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyDigest extends Command
{
    protected $signature = 'notifications:digest {--frequency=daily}';
    protected $description = 'Send a simple daily email digest to active users';

    public function handle(): int
    {
        $freq = $this->option('frequency') === 'weekly' ? 'weekly' : 'daily';
        $users = User::query()
            ->where('status', 'active')
            ->where(function ($q) use ($freq) {
                $q->whereNull('digest_frequency')->orWhere('digest_frequency', $freq);
            })
            ->limit(1000)
            ->get();
        foreach ($users as $user) {
            if (!$user->email) continue;
            $subject = $freq === 'weekly' ? 'Weekly Digest' : 'Daily Digest';
            Mail::raw("Your {$freq} digest: payments, contributions, and meetings updates.", function ($m) use ($user, $subject) {
                $m->to($user->email)->subject($subject);
            });
        }
        $this->info(ucfirst($freq).' digest queued for '.count($users).' users');
        return self::SUCCESS;
    }
}


