<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\DebugInvite::class,
        \App\Console\Commands\EnsureMeetingChats::class,
        \App\Console\Commands\SendUpcomingMeetingReminders::class,
        \App\Console\Commands\SendMeetingReminders::class,
        \App\Console\Commands\SendPledgeReminders::class,
        \App\Console\Commands\MarkOverduePledges::class,
        \App\Console\Commands\ProcessRecurringContributions::class,
        \App\Console\Commands\SendDailyDigest::class,
        \App\Console\Commands\SendCampaignPledgeReminders::class,
        \App\Console\Commands\BackupDatabase::class,
        \App\Console\Commands\DeployVerify::class,
        \App\Console\Commands\SendMonthlyStatements::class,
        \App\Console\Commands\RouteCache::class,
        \App\Console\Commands\DbIndexAudit::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('contributions:close-due')->everyFiveMinutes();
        $schedule->command('meetings:send-reminders --minutes=15')->everyFiveMinutes();
        $schedule->command('meetings:send-reminders --minutes=30')->everyFiveMinutes();
        // Pledge reminders and overdue marking
        $schedule->command('pledges:send-reminders')->hourly();
        $schedule->command('pledges:mark-overdue')->dailyAt('01:00');
        // Recurring contributions
        $schedule->command('recurring:process')->dailyAt('06:00');
        // Notifications digest
        $schedule->command('notifications:digest --frequency=daily')->dailyAt('07:00');
        $schedule->command('notifications:digest --frequency=weekly')->weeklyOn(1, '08:00'); // Monday 8am
        // Campaign pledge reminders
        $schedule->command('campaigns:pledges:send-reminders')->hourly();
        // Daily DB backup at 02:00 (use S3 if configured via --disk)
        $schedule->command('backup:db --disk='.env('BACKUP_DISK', 'local'))->dailyAt('02:00');
        // Monthly statements (first day of month at 9am)
        $schedule->command('groups:send-monthly-statements')->monthlyOn(1, '09:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
