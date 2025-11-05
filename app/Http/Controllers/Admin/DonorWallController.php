<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;

class DonorWallController extends Controller
{
    public function index(Request $request, Campaign $campaign)
    {
        $filter = $request->query('filter', 'approved'); // approved, pending, all

        $query = $campaign->donations()->where('is_anonymous', false);
        
        if ($filter === 'pending') {
            $query->where('moderation_status', 'pending');
        } elseif ($filter === 'approved') {
            $query->where('show_on_wall', true)->where('moderation_status', 'approved');
        }

        $donations = $query->orderByDesc('amount')->paginateDefault();

        $pending = $campaign->donations()->where('moderation_status', 'pending')->count();

        return view('admin.campaigns.donor_wall', compact('campaign', 'donations', 'pending', 'filter'));
    }

    public function moderate(Request $request, Donation $donation)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject,remove',
        ]);

        switch ($validated['action']) {
            case 'approve':
                $donation->update(['moderation_status' => 'approved']);
                break;
            case 'reject':
                $donation->update(['moderation_status' => 'rejected', 'show_on_wall' => false]);
                break;
            case 'remove':
                $donation->update(['show_on_wall' => false]);
                break;
        }

        return back()->with('status', 'Moderation action applied.');
    }
}

