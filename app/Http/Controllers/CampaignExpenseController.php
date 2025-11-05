<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Campaign $campaign)
    {
        // Only organizer or admin can add expenses
        if ($campaign->organizer_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0.01',
        ]);

        CampaignExpense::create([
            'campaign_id' => $campaign->id,
            'recorded_by' => Auth::id(),
            'description' => $validated['description'],
            'amount' => $validated['amount'],
        ]);

        return back()->with('status', 'Expense recorded successfully.');
    }

    public function destroy(Campaign $campaign, CampaignExpense $expense)
    {
        if ($campaign->organizer_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $expense->delete();

        return back()->with('status', 'Expense deleted successfully.');
    }
}

