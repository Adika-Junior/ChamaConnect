<?php

namespace App\Console\Commands;

use App\Models\CampaignPledge;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SendCampaignPledgeReminders extends Command
{
    protected $signature = 'campaigns:pledges:send-reminders';
    protected $description = 'Send reminders for campaign pledges due or overdue';

    public function handle(): int
    {
        $now = now()->toDateString();
        $pledges = CampaignPledge::query()
            ->whereIn('status', ['pending','overdue'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', $now)
            ->limit(500)
            ->get();

        $count = 0;
        foreach ($pledges as $p) {
            $email = $p->donor_email;
            if (!$email) continue;
            Notification::route('mail', $email)->notify(new class($p) extends \Illuminate\Notifications\Notification {
                public function __construct(private \App\Models\CampaignPledge $p) {}
                public function via($notifiable) { return ['mail']; }
                public function toMail($notifiable) {
                    $due = optional($this->p->due_date)->toFormattedDateString();
                    $amount = number_format($this->p->amount_cents / 100, 2);
                    return (new MailMessage)
                        ->subject('Campaign Pledge Reminder')
                        ->line("Your pledge of KES {$amount} is due on {$due}.")
                        ->line('Thank you for supporting the campaign.');
                }
            });
            $p->reminder_count += 1;
            if ($p->due_date->isPast() && $p->status === 'pending') {
                $p->status = 'overdue';
            }
            $p->save();
            $count++;
        }

        $this->info("Sent {$count} campaign pledge reminders");
        return self::SUCCESS;
    }
}


