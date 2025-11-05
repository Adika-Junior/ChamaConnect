<?php

namespace App\Services\Payments;

class MpesaSignatureVerifier
{
    public function verify(string $payload, ?string $signature): bool
    {
        $secret = (string) config('services.mpesa.webhook_secret', env('MPESA_WEBHOOK_SECRET'));

        if ($secret === '' || $secret === null) {
            // If no secret configured, accept but log should inform ops to set it.
            return true;
        }

        if (!$signature) {
            return false;
        }

        $computed = base64_encode(hash_hmac('sha256', $payload, $secret, true));

        // Timing-safe compare
        if (function_exists('hash_equals')) {
            return hash_equals($computed, (string) $signature);
        }

        return $computed === (string) $signature;
    }
}


