<?php

namespace App\Http\Controllers\Donations;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;

class DonationReceiptController extends Controller
{
    // Public receipt view by reference
    public function show(string $reference)
    {
        $donation = Donation::where('reference', $reference)->firstOrFail();
        return view('donations.receipt', compact('donation'));
    }

    // Download receipt as printable HTML (or future PDF)
    public function download(string $reference)
    {
        $donation = Donation::where('reference', $reference)->firstOrFail();
        return response()->view('donations.receipt_print', compact('donation'));
    }
}


