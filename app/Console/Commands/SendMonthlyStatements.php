<?php

namespace App\Console\Commands;

use App\Models\Group;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class SendMonthlyStatements extends Command
{
    protected $signature = 'groups:send-monthly-statements {--month=}';
    protected $description = 'Send monthly statements to group admins via email';

    public function handle(): int
    {
        $month = $this->option('month') ?: now()->subMonth()->format('Y-m');
        [$y, $m] = explode('-', $month);
        $start = \Carbon\Carbon::createFromDate((int)$y, (int)$m, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $groups = Group::where('type', 'sacco')
            ->whereNotNull('contact_email')
            ->get();

        $sent = 0;
        foreach ($groups as $group) {
            $expenses = $group->expenses()
                ->whereBetween('created_at', [$start, $end])
                ->get();

            $payments = collect();
            if (method_exists($group, 'contributions')) {
                $group->load(['contributions.payments' => function ($q) use ($start, $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                }]);
                foreach ($group->contributions as $c) {
                    foreach ($c->payments ?? [] as $p) {
                        $payments->push($p);
                    }
                }
            }

            if ($expenses->isEmpty() && $payments->isEmpty()) {
                continue; // Skip groups with no activity
            }

            $totalExpenses = $expenses->sum('amount');
            $totalPayments = $payments->sum(function ($p) {
                return $p->amount ?? ($p->amount_cents ? $p->amount_cents / 100 : 0);
            });

            try {
                $pdf = Pdf::loadView('admin.groups.statement_pdf', [
                    'group' => $group,
                    'month' => $month,
                    'start' => $start,
                    'end' => $end,
                    'expenses' => $expenses,
                    'payments' => $payments,
                    'totalExpenses' => $totalExpenses,
                    'totalPayments' => $totalPayments,
                ]);

                Mail::raw("Monthly statement for {$group->name} ({$start->format('M Y')})", function ($m) use ($group, $pdf, $month) {
                    $m->to($group->contact_email)
                      ->subject("Monthly Statement - {$group->name} - {$month}")
                      ->attachData($pdf->output(), "statement-{$group->id}-{$month}.pdf", ['mime' => 'application/pdf']);
                });

                $sent++;
            } catch (\Throwable $e) {
                $this->error("Failed to send to {$group->contact_email}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$sent} monthly statements for {$month}");
        return self::SUCCESS;
    }
}

