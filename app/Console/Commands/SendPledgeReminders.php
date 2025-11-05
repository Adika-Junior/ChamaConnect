<?php

namespace App\Console\Commands;

use App\Models\ContributionPledge;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SendPledgeReminders extends Command
{
    protected $signature = 'pledges:send-reminders';
    protected $description = 'Send reminders for upcoming and overdue pledges';

    public function handle(): int
    {
        $now = now();

        $pledges = ContributionPledge::query()
            ->whereIn('status', ['pending','overdue'])
            ->where(function ($q) use ($now) {
                $q->whereDate('due_date', '<=', $now->toDateString());
            })
            ->limit(500)
            ->get();

        $count = 0;
        foreach ($pledges as $pledge) {
            $email = data_get($pledge->metadata, 'recipient_email');
            if (!$email) continue;

            Notification::route('mail', $email)->notify(new class($pledge) extends \Illuminate\Notifications\Notification {
                public function __construct(private \App\Models\ContributionPledge $pledge) {}
                public function via($notifiable) { return ['mail']; }
                public function toMail($notifiable) {
                    $due = optional($this->pledge->due_date)->toFormattedDateString();
                    $amount = number_format($this->pledge->amount_cents / 100, 2);
                    return (new MailMessage)
                        ->subject('Contribution Reminder')
                        ->line("Your contribution of KES {$amount} is due on {$due}.")
                        ->line('Thank you for your continued support.');
                }
            });

            $pledge->reminder_count += 1;
            if ($pledge->due_date->isPast() && $pledge->status === 'pending') {
                $pledge->status = 'overdue';
            }
            $pledge->save();
            $count++;
        }

        $this->info("Sent {$count} pledge reminders");
        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Pledge;
use App\Models\User;
use App\Notifications\PledgeReminderNotification;
use Illuminate\Console\Command;

class SendPledgeReminders extends Command
{
    protected $signature = 'pledges:send-reminders';
    protected $description = 'Send reminders for upcoming, due today, and overdue pledges';

    public function handle()
    {
        $today = now()->startOfDay();
        $inThreeDays = $today->copy()->addDays(3);
        $inSevenDays = $today->copy()->addDays(7);

        $sent = 0;

        // Upcoming in 7 days
        $upcomingSeven = Pledge::whereIn('status', ['pending', 'partially_paid'])
            ->whereDate('due_date', $inSevenDays)->with(['user', 'contribution'])->get();
        foreach ($upcomingSeven as $pledge) {
            $user = $pledge->user;
            if ($user && $user->isActive()) {
                $pref = $user->getNotificationPreference('pledge_reminder');
                if (!$pref || !$pref->isQuietHour()) {
                    $user->notify(new PledgeReminderNotification($pledge, 'upcoming'));
                    $sent++;
                }
            }
        }

        // Upcoming in 3 days
        $upcomingThree = Pledge::whereIn('status', ['pending', 'partially_paid'])
            ->whereDate('due_date', $inThreeDays)->with(['user', 'contribution'])->get();
        foreach ($upcomingThree as $pledge) {
            $user = $pledge->user;
            if ($user && $user->isActive()) {
                $pref = $user->getNotificationPreference('pledge_reminder');
                if (!$pref || !$pref->isQuietHour()) {
                    $user->notify(new PledgeReminderNotification($pledge, 'upcoming'));
                    $sent++;
                }
            }
        }

        // Due today
        $dueToday = Pledge::whereIn('status', ['pending', 'partially_paid'])
            ->whereDate('due_date', $today)->with(['user', 'contribution'])->get();
        foreach ($dueToday as $pledge) {
            $user = $pledge->user;
            if ($user && $user->isActive()) {
                $pref = $user->getNotificationPreference('pledge_reminder');
                if (!$pref || !$pref->isQuietHour()) {
                    $user->notify(new PledgeReminderNotification($pledge, 'due_today'));
                    $sent++;
                }
            }
        }

        // Overdue (send once per day)
        $overdue = Pledge::where('status', 'overdue')
            ->whereDate('due_date', '<', $today)->with(['user', 'contribution'])->get();
        foreach ($overdue as $pledge) {
            $user = $pledge->user;
            if ($user && $user->isActive()) {
                $pref = $user->getNotificationPreference('pledge_reminder');
                if (!$pref || !$pref->isQuietHour()) {
                    $user->notify(new PledgeReminderNotification($pledge, 'overdue'));
                    $sent++;
                }
            }
        }

        $this->info("Sent {$sent} pledge reminders.");
        return Command::SUCCESS;
    }
}


