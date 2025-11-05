<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;

class DonationExportController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.donations.export');
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
            'anonymize' => 'sometimes|boolean',
        ]);

        $query = Donation::query()
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$validated['from'], $validated['to']]);

        $rows = [];
        $headers = ['Reference', 'Date', 'Amount', 'Currency', 'Donor Name', 'Donor Email', 'Donor Phone', 'Campaign ID', 'Mpesa Receipt'];

        foreach ($query->cursor() as $d) {
            $donorName = $validated['anonymize'] ?? false ? 'Anonymous' : ($d->donor_name ?? '');
            $donorEmail = $validated['anonymize'] ?? false ? '' : ($d->donor_email ?? '');
            $donorPhone = $validated['anonymize'] ?? false ? '' : ($d->donor_phone ?? '');
            $rows[] = [
                $d->reference,
                optional($d->paid_at)->toDateTimeString(),
                number_format($d->amount_cents / 100, 2),
                $d->currency,
                $donorName,
                $donorEmail,
                $donorPhone,
                $d->campaign_id,
                $d->mpesa_receipt,
            ];
        }

        $filename = 'donations_' . date('Ymd_His') . '.csv';
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ]);
    }
}


