<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContributionPledge;
use Illuminate\Http\Request;

class ContributionReportController extends Controller
{
    public function exportForm()
    {
        return view('admin.contributions.export');
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
            'status' => 'nullable|in:pending,paid,overdue,cancelled',
        ]);

        $query = ContributionPledge::query()
            ->whereBetween('due_date', [$validated['from'], $validated['to']]);
        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $headers = ['ID','Rule ID','Group ID','User ID','Campaign ID','Due Date','Amount','Currency','Status','Paid At','Reminder Count'];
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);
        foreach ($query->cursor() as $p) {
            fputcsv($handle, [
                $p->id,
                $p->rule_id,
                $p->group_id,
                $p->user_id,
                $p->campaign_id,
                optional($p->due_date)->toDateString(),
                number_format($p->amount_cents / 100, 2),
                $p->currency,
                $p->status,
                optional($p->paid_at)->toDateTimeString(),
                $p->reminder_count,
            ]);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=contribution_pledges_'.date('Ymd_His').'.csv',
        ]);
    }

    public function summary(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        $base = ContributionPledge::query()->whereBetween('due_date', [$validated['from'], $validated['to']]);

        $totals = [
            'pending' => (clone $base)->where('status','pending')->sum('amount_cents'),
            'paid' => (clone $base)->where('status','paid')->sum('amount_cents'),
            'overdue' => (clone $base)->where('status','overdue')->sum('amount_cents'),
            'cancelled' => (clone $base)->where('status','cancelled')->sum('amount_cents'),
            'count' => (clone $base)->count(),
        ];

        return view('admin.contributions.summary', [
            'from' => $validated['from'],
            'to' => $validated['to'],
            'totals' => array_map(function ($cents) { return is_numeric($cents) ? number_format($cents / 100, 2) : $cents; }, $totals),
        ]);
    }
}
