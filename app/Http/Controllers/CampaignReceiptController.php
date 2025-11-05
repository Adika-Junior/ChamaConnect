<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;

class CampaignReceiptController extends Controller
{
    // Donor receipt scoped to campaign and donation reference
    public function donor(string $campaignId, string $reference)
    {
        $campaign = Campaign::findOrFail($campaignId);
        $donation = Donation::where('reference', $reference)->firstOrFail();
        return view('campaigns.receipts.donor', compact('campaign', 'donation'));
    }

    // Organizer summary receipt (CSV) for a campaign
    public function organizerCsv(string $campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);
        $donations = Donation::query()->where('campaign_id', $campaign->id)->whereNotNull('paid_at');

        $headers = ['Reference','Date','Amount','Currency','Donor Name','Donor Email','Donor Phone','Mpesa Receipt'];
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);
        foreach ($donations->cursor() as $d) {
            fputcsv($handle, [
                $d->reference,
                optional($d->paid_at)->toDateTimeString(),
                number_format($d->amount_cents / 100, 2),
                $d->currency,
                $d->donor_name,
                $d->donor_email,
                $d->donor_phone,
                $d->mpesa_receipt,
            ]);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        $filename = 'campaign_'.$campaign->id.'_donations_'.date('Ymd_His').'.csv';
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename='.$filename,
        ]);
    }
}


