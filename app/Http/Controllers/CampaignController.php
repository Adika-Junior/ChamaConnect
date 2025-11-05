<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\CampaignUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::query();

        if (!$request->user() || !$request->user()->isAdmin()) {
            $query->where('is_public', true)->where('status', 'active');
        }

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sort = $request->query('sort', 'newest');
        switch ($sort) {
            case 'goal':
                $query->orderByDesc('goal_amount');
                break;
            case 'progress':
                $query->orderByRaw('CASE WHEN goal_amount > 0 THEN (current_amount / goal_amount) ELSE 0 END DESC');
                break;
            default:
                $query->orderByDesc('created_at');
        }

        $campaigns = $query->paginate(12)->withQueryString();
        return view('campaigns.index', [
            'campaigns' => $campaigns,
            'search' => $search,
            'sort' => $sort,
        ]);
    }

    public function create()
    {
        return view('campaigns.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'goal_amount' => 'required|numeric|min:0.01',
            'is_public' => 'boolean',
            'allow_anonymous' => 'boolean',
        ]);

        $validated['organizer_id'] = Auth::id();
        $validated['status'] = 'pending';
        $validated['is_public'] = $request->has('is_public');
        $validated['allow_anonymous'] = $request->has('allow_anonymous');

        $campaign = Campaign::create($validated);

        return redirect()->route('campaigns.show', $campaign)
            ->with('status', 'Campaign created and pending approval.');
    }

    public function show(Campaign $campaign)
    {
        // SEO meta tags
        $meta = [
            'title' => $campaign->title . ' - ' . config('app.name'),
            'description' => \Illuminate\Support\Str::limit(strip_tags($campaign->description ?? ''), 160),
            'image' => $campaign->image_url ?? asset('brand/chamaconnect-logo.svg'),
            'url' => route('campaigns.show', $campaign),
        ];

        // Check if public campaign is accessible
        if ($campaign->is_public && $campaign->status === 'active') {
            // Allow public access
        } elseif (!Auth::check() || (!Auth::user()->isAdmin() && $campaign->organizer_id !== Auth::id())) {
            abort(403, 'This campaign is not publicly accessible.');
        }

        $campaign->load([
            'organizer',
            'donations' => function($query) {
                $query->latest()->limit(50);
            },
            'updates' => function($query) {
                $query->latest()->limit(10);
            },
            'expenses'
        ]);

        $donations = $campaign->donations()
            ->select('donor_name', 'avatar_url', DB::raw('SUM(amount) as total'))
            ->where('is_anonymous', false)
            ->where('show_on_wall', true)
            ->where('moderation_status', 'approved')
            ->groupBy('donor_name', 'avatar_url')
            ->orderByDesc('total')
            ->limit(20)
            ->get();

        return view('campaigns.show', compact('campaign', 'donations', 'meta'));
    }

    public function donate(Request $request, Campaign $campaign)
    {
        if ($campaign->status !== 'active') {
            return back()->withErrors(['error' => 'Campaign is not active']);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'message' => 'nullable|string|max:500',
            'is_anonymous' => 'boolean',
            'donor_name' => 'nullable|string|max:255',
            'phone' => 'required|string', // For M-Pesa
        ]);

        // Create donation record first (pending payment)
        $donation = Donation::create([
            'campaign_id' => $campaign->id,
            'donor_id' => $validated['is_anonymous'] ? null : Auth::id(),
            'donor_name' => $validated['donor_name'] ?? (Auth::check() ? Auth::user()->name : null),
            'amount' => $validated['amount'],
            'is_anonymous' => $request->has('is_anonymous'),
            'message' => $validated['message'] ?? null,
            'payment_status' => 'pending',
        ]);

        // Initiate M-Pesa STK Push
        try {
            $mpesaController = app(\App\Http\Controllers\MpesaController::class);
            $response = $mpesaController->initiateStkPush(
                $validated['phone'],
                $validated['amount'],
                "Donation to: {$campaign->title}",
                "campaign_donation_{$donation->id}"
            );

            if ($response && isset($response['CheckoutRequestID'])) {
                // Store checkout request ID for callback
                $donation->update([
                    'payment_id' => null, // Will be set on callback
                    'metadata' => json_encode(['checkout_request_id' => $response['CheckoutRequestID']]),
                ]);

                return back()->with('status', 'Payment request sent to your phone. Please complete the payment.');
            } else {
                // If M-Pesa fails, still allow donation record (manual processing)
                $donation->update(['payment_status' => 'failed']);
                return back()->withErrors(['error' => 'Payment initiation failed. Please try again or contact support.']);
            }
        } catch (\Exception $e) {
            \Log::error('Campaign donation M-Pesa error', [
                'donation_id' => $donation->id,
                'error' => $e->getMessage(),
            ]);
            
            // Allow donation to proceed, payment can be processed manually
            $campaign->increment('current_amount', $donation->amount);
            
            if ($campaign->current_amount >= $campaign->goal_amount) {
                $campaign->update(['status' => 'completed', 'ended_at' => now()]);
            }

            return back()->with('status', 'Donation recorded. Payment processing may take a moment.');
        }
    }

    public function resendDonation(Request $request, Campaign $campaign, Donation $donation)
    {
        $request->validate(['phone' => ['required','string']]);
        if ($donation->campaign_id !== $campaign->id) {
            abort(404);
        }
        try {
            $mpesa = app(\App\Http\Controllers\MpesaController::class);
            $resp = $mpesa->initiateStkPush(
                $request->input('phone'),
                (float) $donation->amount,
                "Donation to: {$campaign->title}",
                "campaign_donation_{$donation->id}"
            );
            if ($resp && isset($resp['CheckoutRequestID'])) {
                $donation->update([
                    'payment_status' => 'pending',
                    'metadata' => json_encode(['checkout_request_id' => $resp['CheckoutRequestID']]),
                ]);
                return back()->with('status', 'STK push resent. Complete on your phone.');
            }
            $donation->update(['payment_status' => 'failed']);
            return back()->withErrors(['error' => 'Failed to resend STK push.']);
        } catch (\Throwable $e) {
            \Log::error('Resend STK failed', ['donation_id' => $donation->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Error resending payment request.']);
        }
    }

    public function update(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'goal_amount' => 'required|numeric|min:0.01',
        ]);

        $campaign->update($validated);

        return back()->with('status', 'Campaign updated successfully.');
    }

    public function addUpdate(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        CampaignUpdate::create([
            'campaign_id' => $campaign->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'created_by' => Auth::id(),
        ]);

        return back()->with('status', 'Update added successfully.');
    }

    public function transparency(Campaign $campaign)
    {
        if (!$campaign->is_public && (!Auth::check() || !Auth::user()->isAdmin())) {
            abort(403);
        }

        $campaign->load(['donations', 'expenses.recorder']);

        $donationsTotal = $campaign->donations()->sum('amount');
        $expensesTotal = $campaign->expenses()->sum('amount');
        $netAmount = $donationsTotal - $expensesTotal;

        $topDonors = $campaign->donations()
            ->select('donor_name', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->where('is_anonymous', false)
            ->groupBy('donor_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('campaigns.transparency', compact('campaign', 'donationsTotal', 'expensesTotal', 'netAmount', 'topDonors'));
    }
}

