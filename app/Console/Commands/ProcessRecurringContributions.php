<?php

namespace App\Console\Commands;

use App\Models\ContributionPledge;
use App\Models\RecurringContributionRule;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class ProcessRecurringContributions extends Command
{
    protected $signature = 'recurring:process';
    protected $description = 'Generate contribution pledges for due recurring rules';

    public function handle(): int
    {
        $now = now();
        $rules = RecurringContributionRule::query()
            ->where('status', 'active')
            ->whereNotNull('next_run_at')
            ->where('next_run_at', '<=', $now)
            ->get();

        foreach ($rules as $rule) {
            $dueDate = $rule->next_run_at?->toDateString();

            ContributionPledge::create([
                'rule_id' => $rule->id,
                'group_id' => $rule->group_id,
                'user_id' => $rule->user_id,
                'due_date' => $dueDate,
                'amount_cents' => $rule->amount_cents,
                'currency' => $rule->currency,
                'status' => 'pending',
                'metadata' => [
                    'recipient_name' => $rule->recipient_name,
                    'recipient_email' => $rule->recipient_email,
                    'recipient_phone' => $rule->recipient_phone,
                ],
            ]);

            $rule->next_run_at = $this->computeNextRun($rule->interval, $rule->next_run_at, $rule->day_of_month, $rule->weekday);
            $rule->save();
        }

        $this->info('Processed '.count($rules).' rules');
        return self::SUCCESS;
    }

    private function computeNextRun(string $interval, $from, ?int $dayOfMonth, ?int $weekday): \Carbon\Carbon
    {
        $base = CarbonImmutable::instance($from ?? now());
        return match ($interval) {
            'weekly' => $base->addWeek()->startOfWeek()->addDays(max(0, (int) $weekday))->toCarbon(),
            'monthly' => $base->addMonth()->day(min(max(1, (int) $dayOfMonth), 28))->toCarbon(),
            'quarterly' => $base->addMonths(3)->day(min(max(1, (int) $dayOfMonth), 28))->toCarbon(),
            default => $base->addMonth()->toCarbon(),
        };
    }
}

<?php

namespace App\Console\Commands;

use App\Models\RecurringContribution;
use App\Models\Pledge;
use Illuminate\Console\Command;

class ProcessRecurringContributions extends Command
{
    protected $signature = 'recurring:process';
    protected $description = 'Process due recurring contributions by creating pledges and notifying users';

    public function handle()
    {
        $today = now()->toDateString();
        $count = 0;

        RecurringContribution::where('status', 'active')
            ->whereDate('next_run_date', '<=', $today)
            ->with(['user', 'contribution'])
            ->chunkById(200, function ($items) use (&$count) {
                foreach ($items as $rc) {
                    // Create a pledge due in 7 days
                    $pledge = Pledge::create([
                        'contribution_id' => $rc->contribution_id,
                        'user_id' => $rc->user_id,
                        'amount' => $rc->amount,
                        'pledged_at' => now()->toDateString(),
                        'due_date' => now()->copy()->addDays(7)->toDateString(),
                        'status' => 'pending',
                    ]);

                    // Advance schedule
                    $rc->advanceNextRunDate();
                    $rc->save();
                    $count++;
                }
            });

        $this->info("Processed {$count} recurring contributions.");
        return Command::SUCCESS;
    }
}


