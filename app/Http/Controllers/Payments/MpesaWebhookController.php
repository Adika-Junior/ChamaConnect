<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\WebhookEvent;
use App\Services\Payments\MpesaSignatureVerifier;
use App\Services\Alerts\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\Observability\Metrics;

class MpesaWebhookController extends Controller
{
    public function handle(Request $request, MpesaSignatureVerifier $verifier, AlertService $alerts)
    {
        // Optional IP allowlist check (configure in .env: MPESA_WEBHOOK_IPS)
        $allowedIps = array_filter(explode(',', config('services.mpesa.webhook_ips', '')));
        if (!empty($allowedIps) && !in_array($request->ip(), $allowedIps)) {
            Log::warning('Mpesa webhook from unauthorized IP', ['ip' => $request->ip()]);
            return response()->json(['status' => 'ignored'], 403);
        }

        $rawPayload = $request->getContent();
        $signature = $request->header('X-Signature') ?? $request->header('X-Mpesa-Signature');

        // Verify signature (returns boolean)
        if (!$verifier->verify($rawPayload, $signature)) {
            Metrics::inc('webhook_failed_total');
            Log::warning('Mpesa webhook signature verification failed', [
                'ip' => $request->ip(),
                'ua' => $request->userAgent(),
            ]);
            return response()->json(['status' => 'ignored'], 200);
        }

        $data = json_decode($rawPayload, true) ?? [];

        // Build an idempotency key from stable fields when available
        $idempotencyKey = $this->computeIdempotencyKey($data, $rawPayload);

        // Try to store webhook event; if duplicate, return early for idempotency
        $event = WebhookEvent::firstOrCreate(
            [
                'provider' => 'mpesa',
                'idempotency_key' => $idempotencyKey,
            ],
            [
                'signature' => (string) $signature,
                'payload' => $rawPayload,
                'status' => 'received',
            ]
        );

        if ($event->wasRecentlyCreated === false) {
            return response()->json(['status' => 'duplicate'], 200);
        }

        try {
            Metrics::inc('webhook_received_total');
            // TODO: map payload to domain model (donation/payment update)
            // For now, mark processed to demonstrate idempotency & verification in place
            $event->status = 'processed';
            $event->processed_at = now();
            $event->save();
            Metrics::inc('webhook_processed_total');
        } catch (\Throwable $e) {
            $event->status = 'failed';
            $event->error = Str::limit($e->getMessage(), 500);
            $event->save();
            Log::error('Mpesa webhook processing failed', ['error' => $e->getMessage()]);
            $alerts->notify('M-Pesa Webhook Failure', 'Event ID ' . $event->id . ' failed: ' . $e->getMessage());
            Metrics::inc('webhook_failed_total');
        }

        return response()->json(['status' => 'ok'], 200);
    }

    private function computeIdempotencyKey(array $data, string $rawPayload): string
    {
        // Prefer well-known IDs from M-Pesa STK callback when present
        $c2bCheckoutId = data_get($data, 'Body.stkCallback.CheckoutRequestID');
        $merchantRequestId = data_get($data, 'Body.stkCallback.MerchantRequestID');

        if ($c2bCheckoutId || $merchantRequestId) {
            return hash('sha256', 'mpesa:' . ($c2bCheckoutId ?? '') . ':' . ($merchantRequestId ?? ''));
        }

        // Fallback: hash of whole payload to ensure idempotency across retries
        return hash('sha256', 'mpesa:' . $rawPayload);
    }
}


