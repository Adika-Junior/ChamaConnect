<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\CampaignApprovedNotification;

class CampaignApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $campaigns = Campaign::where('status', 'pending')
            ->with('organizer')
            ->latest()
            ->paginate(15);

        return view('admin.campaign-approvals', compact('campaigns'));
    }

    public function approve(Campaign $campaign)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $campaign->approve(Auth::user());

        if ($campaign->organizer) {
            $campaign->organizer->notify(new CampaignApprovedNotification($campaign));
        }

        return back()->with('status', 'Campaign approved successfully.');
    }

    public function reject(Request $request, Campaign $campaign)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $campaign->reject(Auth::user(), $validated['rejection_reason']);

        return back()->with('status', 'Campaign rejected.');
    }
}

