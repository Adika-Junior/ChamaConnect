<?php

namespace App\Console\Commands;

use App\Models\ContributionPledge;
use Illuminate\Console\Command;

class MarkOverduePledges extends Command
{
    protected $signature = 'pledges:mark-overdue';
    protected $description = 'Mark pledges as overdue if past due date and still pending';

    public function handle(): int
    {
        $count = ContributionPledge::query()
            ->where('status', 'pending')
            ->whereDate('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);

        $this->info("Marked {$count} pledges as overdue");
        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Pledge;
use Illuminate\Console\Command;

class MarkOverduePledges extends Command
{
    protected $signature = 'pledges:mark-overdue';
    protected $description = 'Mark pending pledges as overdue when past due_date';

    public function handle()
    {
        $today = now()->startOfDay();
        $updated = 0;

        Pledge::where('status', 'pending')
            ->whereDate('due_date', '<', $today)
            ->chunkById(500, function ($pledges) use (&$updated) {
                foreach ($pledges as $pledge) {
                    $pledge->markOverdue();
                    $updated++;
                }
            });

        $this->info("Marked {$updated} pledges as overdue.");
        return Command::SUCCESS;
    }
}


