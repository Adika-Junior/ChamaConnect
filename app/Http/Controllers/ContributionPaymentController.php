<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\ContributionPayment;
use App\Models\Pledge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContributionPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Contribution $contribution)
    {
        $this->authorize('contribute', $contribution);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:mpesa,bank,cash,other'],
            'reference' => ['nullable', 'string', 'max:255'],
            'paid_at' => ['nullable', 'date'],
        ]);

        $payment = new ContributionPayment($validated);
        $payment->contribution_id = $contribution->id;
        $payment->user_id = Auth::id();
        if (empty($payment->paid_at)) {
            $payment->paid_at = now();
        }
        $payment->save();

        // Update aggregated collected amount
        $contribution->collected_amount = (float) $contribution->collected_amount + (float) $payment->amount;
        $contribution->save();

        // Auto-fulfill or partially update user's pledge(s) for this contribution
        $remaining = (float) $payment->amount;
        $userId = Auth::id();
        if ($userId) {
            $pledges = Pledge::where('contribution_id', $contribution->id)
                ->where('user_id', $userId)
                ->whereIn('status', ['pending', 'partially_paid', 'overdue'])
                ->orderBy('due_date')
                ->get();

            foreach ($pledges as $pledge) {
                if ($remaining <= 0) break;
                $pledgeAmount = (float) $pledge->amount;

                // If already partially paid, assume amount stores total pledge; here we only mark status
                if ($remaining >= $pledgeAmount) {
                    $pledge->status = 'fulfilled';
                    $pledge->save();
                    $remaining -= $pledgeAmount;
                } else {
                    // Partial payment scenario
                    $pledge->status = 'partially_paid';
                    $pledge->save();
                    $remaining = 0;
                }
            }
        }

        event(new \App\Events\ContributionPaymentCreated($contribution->id, $payment->amount, $contribution->collected_amount));

        return redirect()->route('contributions.show', $contribution)
            ->with('status', 'Contribution recorded successfully.');
    }
}


