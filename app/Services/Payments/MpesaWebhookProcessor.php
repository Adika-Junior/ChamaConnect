<?php

namespace App\Services\Payments;

use App\Models\WebhookEvent;
use Illuminate\Support\Facades\Log;

class MpesaWebhookProcessor
{
    public function process(WebhookEvent $event): void
    {
        // Decode payload and update domain models accordingly.
        // This is a safe placeholder that marks the event processed.
        try {
            $payload = json_decode($event->payload, true) ?? [];

            // TODO: apply business logic for donations/payments

            $event->status = 'processed';
            $event->processed_at = now();
            $event->error = null;
            $event->save();
        } catch (\Throwable $e) {
            Log::error('MpesaWebhookProcessor failed', ['event_id' => $event->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}


