<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupLedgerExportController extends Controller
{
    public function form(Group $group)
    {
        return view('admin.groups.ledger', compact('group'));
    }

    public function export(Request $request, Group $group)
    {
        $validated = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        // Collect expenses
        $expenses = $group->expenses()
            ->whereBetween('created_at', [$validated['from'], $validated['to']])
            ->get(['id','category','amount','status','created_at']);

        // Collect contribution payments if relation exists
        $payments = collect();
        if (method_exists($group, 'contributions')) {
            $group->load(['contributions.payments' => function ($q) use ($validated) {
                $q->whereBetween('created_at', [$validated['from'], $validated['to']]);
            }]);
            foreach ($group->contributions as $c) {
                foreach ($c->payments ?? [] as $p) {
                    $payments->push([
                        'id' => $p->id,
                        'contribution_id' => $c->id,
                        'amount' => $p->amount ?? ($p->amount_cents ? $p->amount_cents/100 : null),
                        'method' => $p->method ?? 'mpesa',
                        'created_at' => $p->created_at,
                    ]);
                }
            }
        }

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['Type','ID','Date','Category/Contribution','Amount','Status/Method']);

        foreach ($expenses as $e) {
            fputcsv($handle, ['expense', $e->id, optional($e->created_at)->toDateTimeString(), $e->category, number_format((float)$e->amount, 2), $e->status]);
        }
        foreach ($payments as $p) {
            fputcsv($handle, ['payment', $p['id'], optional($p['created_at'])->toDateTimeString(), 'Contribution #'.$p['contribution_id'], number_format((float)$p['amount'], 2), $p['method']]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        $filename = 'group_'.$group->id.'_ledger_'.date('Ymd_His').'.csv';
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename='.$filename,
        ]);
    }

    public function statement(Request $request, Group $group)
    {
        $month = $request->query('month', now()->format('Y-m'));
        [$y, $m] = explode('-', $month);
        $start = \Carbon\Carbon::createFromDate((int)$y, (int)$m, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

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

        $totalExpenses = $expenses->sum('amount');
        $totalPayments = $payments->sum(function ($p) { return $p->amount ?? ($p->amount_cents ? $p->amount_cents/100 : 0); });

        return view('admin.groups.statement', [
            'group' => $group,
            'month' => $month,
            'start' => $start,
            'end' => $end,
            'expenses' => $expenses,
            'payments' => $payments,
            'totalExpenses' => $totalExpenses,
            'totalPayments' => $totalPayments,
        ]);
    }
}


