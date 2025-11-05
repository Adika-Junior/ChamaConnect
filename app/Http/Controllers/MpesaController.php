<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\ContributionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use App\Notifications\CampaignDonationReceivedNotification;
use App\Notifications\CampaignGoalReachedNotification;

class MpesaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('initiate');
    }

    public function initiate(Request $request, Contribution $contribution)
    {
        $this->authorize('contribute', $contribution);

        $validated = $request->validate([
            'phone' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        // Build STK push payload for Daraja sandbox
        $shortcode = config('services.mpesa.shortcode');
        $passkey = config('services.mpesa.passkey');
        $timestamp = now()->format('YmdHis');
        $password = base64_encode($shortcode . $passkey . $timestamp);

        $payload = [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int) $validated['amount'],
            'PartyA' => $validated['phone'],
            'PartyB' => $shortcode,
            'PhoneNumber' => $validated['phone'],
            'CallBackURL' => route('payments.mpesa.callback'),
            'AccountReference' => 'CONTR-' . $contribution->id,
            'TransactionDesc' => 'Contribution #' . $contribution->id,
        ];

        $token = $this->generateAccessToken();
        if (!$token) {
            return back()->withErrors(['mpesa' => 'M-Pesa configuration missing.']);
        }

        $response = Http::withToken($token)
            ->acceptJson()
            ->post(config('services.mpesa.stk_url'), $payload);

        if ($response->failed()) {
            return back()->withErrors(['mpesa' => 'M-Pesa request failed: ' . $response->body()]);
        }

        return back()->with('status', 'STK push sent. Complete on your phone to record payment.');
    }

    public function callback(Request $request)
    {
        // Public callback: validate content and record payment on success
        $data = $request->all();

        $resultCode = data_get($data, 'Body.stkCallback.ResultCode');
        $resultDesc = data_get($data, 'Body.stkCallback.ResultDesc');
        $callbackMeta = collect(data_get($data, 'Body.stkCallback.CallbackMetadata.Item', []))
            ->keyBy('Name')
            ->map(fn ($i) => $i['Value'] ?? null);

        $accountRef = $callbackMeta->get('AccountReference') ?: data_get($data, 'Body.stkCallback.MerchantRequestID');
        
        // Handle campaign donations
        if (strpos($accountRef, 'campaign_donation_') === 0) {
            $donationId = (int) str_replace('campaign_donation_', '', (string) $accountRef);
            $donation = \App\Models\Donation::find($donationId);
            
            if ($donation && ($resultCode === 0 || $resultCode === '0')) {
                $amount = (float) ($callbackMeta->get('Amount') ?? 0);
                $receipt = (string) ($callbackMeta->get('MpesaReceiptNumber') ?? '');
                
                // Create payment record
                $payment = new ContributionPayment([
                    'amount' => $amount,
                    'payment_method' => 'mpesa',
                    'reference' => $receipt,
                    'paid_at' => now(),
                    'contribution_id' => null, // Campaign donations don't have contribution
                    'user_id' => $donation->donor_id ?? $donation->campaign->organizer_id,
                ]);
                $payment->save();
                
                // Update donation
                $donation->update([
                    'payment_id' => $payment->id,
                    'payment_status' => 'completed',
                ]);
                
                // Update campaign
                $campaign = $donation->campaign;
                $campaign->increment('current_amount', $amount);
                // Notify organizer about donation
                if ($campaign->organizer) {
                    $campaign->organizer->notify(new CampaignDonationReceivedNotification($campaign, $donation));
                }
                
                if ($campaign->current_amount >= $campaign->goal_amount) {
                    $campaign->update(['status' => 'completed', 'ended_at' => now()]);
                    if ($campaign->organizer) {
                        $campaign->organizer->notify(new CampaignGoalReachedNotification($campaign));
                    }
                }
            }
            if ($donation && !($resultCode === 0 || $resultCode === '0')) {
                $donation->update(['payment_status' => 'failed']);
            }
            
            return response()->json(['status' => 'ok', 'desc' => $resultDesc ?? '']);
        }
        
        // Handle contribution payments (existing logic)
        $contributionId = (int) str_replace('CONTR-', '', (string) $accountRef);
        $contribution = Contribution::find($contributionId);

        if (!$contribution) {
            return response()->json(['status' => 'ignored']);
        }

        if ($resultCode === 0 || $resultCode === '0') {
            $amount = (float) ($callbackMeta->get('Amount') ?? 0);
            $receipt = (string) ($callbackMeta->get('MpesaReceiptNumber') ?? '');
            $phone = (string) ($callbackMeta->get('PhoneNumber') ?? '');

            // Creator unknown on callback; set to organizer
            $userId = $contribution->organizer_id;
            $payment = new ContributionPayment([
                'amount' => $amount,
                'payment_method' => 'mpesa',
                'reference' => $receipt ?: $phone,
                'paid_at' => now(),
            ]);
            $payment->contribution_id = $contribution->id;
            $payment->user_id = $userId;
            $payment->save();

            $contribution->collected_amount = (float) $contribution->collected_amount + (float) $amount;
            $contribution->save();

            event(new \App\Events\ContributionPaymentCreated($contribution->id, $payment->amount, $contribution->collected_amount));
        }

        return response()->json(['status' => 'ok', 'desc' => $resultDesc]);
    }

    /**
     * Public method to initiate STK push for campaigns or other purposes
     */
    public function initiateStkPush(string $phone, float $amount, string $description, string $accountReference): ?array
    {
        $shortcode = config('services.mpesa.shortcode');
        $passkey = config('services.mpesa.passkey');
        $timestamp = now()->format('YmdHis');
        $password = base64_encode($shortcode . $passkey . $timestamp);

        $payload = [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int) $amount,
            'PartyA' => $phone,
            'PartyB' => $shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => route('payments.mpesa.callback'),
            'AccountReference' => $accountReference,
            'TransactionDesc' => $description,
        ];

        $token = $this->generateAccessToken();
        if (!$token) {
            return null;
        }

        $response = Http::withToken($token)
            ->acceptJson()
            ->post(config('services.mpesa.stk_url'), $payload);

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }

    protected function generateAccessToken(): ?string
    {
        $key = config('services.mpesa.consumer_key');
        $secret = config('services.mpesa.consumer_secret');
        $authUrl = config('services.mpesa.auth_url');
        if (!$key || !$secret || !$authUrl) {
            return null;
        }
        $response = Http::withBasicAuth($key, $secret)->get($authUrl);
        if ($response->failed()) {
            return null;
        }
        return $response->json('access_token');
    }
}


