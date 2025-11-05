<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Pledge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PledgeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Contribution $contribution)
    {
        $this->authorize('view', $contribution);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date|after:today',
            'notes' => 'nullable|string|max:500',
        ]);

        $pledge = Pledge::create([
            'contribution_id' => $contribution->id,
            'user_id' => Auth::id(),
            'amount' => $validated['amount'],
            'pledged_at' => now(),
            'due_date' => $validated['due_date'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('status', 'Pledge recorded successfully. Thank you for your commitment!');
    }

    public function fulfill(Request $request, Pledge $pledge)
    {
        if ($pledge->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Redirect to payment page
        return redirect()->route('contributions.show', $pledge->contribution)
            ->with('fulfill_pledge', $pledge->id)
            ->with('amount', $pledge->amount);
    }
}

