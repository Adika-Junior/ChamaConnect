<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\ContributionPayment;
use Illuminate\Http\Request;

class BankWebhookController extends Controller
{
    public function incoming(Request $request)
    {
        $payload = $request->validate([
            'reference' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'contribution_id' => ['nullable', 'integer', 'exists:contributions,id'],
            'account_reference' => ['nullable', 'string'],
        ]);

        $contribution = null;
        if (!empty($payload['contribution_id'])) {
            $contribution = Contribution::find($payload['contribution_id']);
        } elseif (!empty($payload['account_reference'])) {
            // Support strings like CONTR-123
            if (preg_match('/(\d+)/', $payload['account_reference'], $m)) {
                $contribution = Contribution::find((int) $m[1]);
            }
        }

        if (!$contribution) {
            return response()->json(['status' => 'ignored'], 202);
        }

        $payment = new ContributionPayment([
            'amount' => $payload['amount'],
            'payment_method' => 'bank',
            'reference' => $payload['reference'],
            'paid_at' => now(),
        ]);
        $payment->contribution_id = $contribution->id;
        $payment->user_id = $contribution->organizer_id; // fallback attribution
        $payment->save();

        $contribution->collected_amount = (float) $contribution->collected_amount + (float) $payment->amount;
        $contribution->save();

        event(new \App\Events\ContributionPaymentCreated($contribution->id, $payment->amount, $contribution->collected_amount));

        return response()->json(['status' => 'ok']);
    }
}


