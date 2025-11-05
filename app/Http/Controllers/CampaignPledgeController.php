<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignPledge;
use Illuminate\Http\Request;

class CampaignPledgeController extends Controller
{
    // Simple pledge form submit (public or authenticated)
    public function store(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'donor_name' => 'nullable|string|max:120',
            'donor_email' => 'nullable|email',
            'donor_phone' => 'nullable|string|max:50',
            'amount_cents' => 'required|integer|min:100',
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        $validated['campaign_id'] = $campaign->id;
        $validated['currency'] = 'KES';
        $pledge = CampaignPledge::create($validated);

        return back()->with('status', 'Pledge received! We will send a reminder before due date.')->with('pledge_id', $pledge->id);
    }

    public function update(Request $request, CampaignPledge $pledge)
    {
        $this->authorize('update', $pledge);
        $validated = $request->validate([
            'amount_cents' => 'sometimes|integer|min:100',
            'due_date' => 'sometimes|date|after_or_equal:today',
        ]);
        $pledge->update($validated);
        return back()->with('status', 'Pledge updated.');
    }

    public function cancel(Request $request, CampaignPledge $pledge)
    {
        if ($request->user() && $request->user()->id === $pledge->user_id) {
            $pledge->update(['status' => 'cancelled']);
            return back()->with('status', 'Pledge cancelled.');
        }
        abort(403);
    }

    // Organizer can mark pledge fulfilled
    public function fulfill(Request $request, CampaignPledge $pledge)
    {
        $this->authorize('admin');
        $pledge->update(['status' => 'fulfilled', 'fulfilled_at' => now()]);
        return back()->with('status', 'Pledge marked as fulfilled');
    }
}


