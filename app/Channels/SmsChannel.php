<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toSms')) {
            return;
        }

        $message = $notification->toSms($notifiable);
        $phone = $message['to'] ?? ($notifiable->phone ?? null);
        $text = $message['text'] ?? '';

        if (!$phone || $text === '') {
            return;
        }

        // Quiet hours (22:00 - 06:00) -> skip send and log for later (simple demo)
        $hour = (int) now()->format('H');
        if ($hour >= 22 || $hour < 6) {
            Log::info('SMS suppressed due to quiet hours', ['to' => $phone]);
            return;
        }

        $provider = (string) config('services.sms.provider', 'log');
        try {
            if ($provider === 'africas_talking') {
                // Placeholder implementation; integrate real API here
                Http::post((string) env('AFRICAS_TALKING_SMS_URL', 'https://api.africastalking.com/version1/messaging'), [
                    'username' => config('services.sms.username'),
                    'to' => $phone,
                    'message' => $text,
                    'from' => config('services.sms.shortcode'),
                ]);
            } else {
                Log::info('SMS (log provider)', ['to' => $phone, 'text' => $text]);
            }
        } catch (\Throwable $e) {
            Log::error('SMS send failed', ['error' => $e->getMessage()]);
        }
    }
}

<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    /**
     * Send the given notification via SMS
     */
    public function send($notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toSms')) {
            return;
        }

        $message = $notification->toSms($notifiable);
        $phone = $notifiable->phone;

        if (!$phone) {
            Log::warning('SMS notification skipped: user has no phone number', [
                'user_id' => $notifiable->id,
            ]);
            return;
        }

        // Use Africa's Talking or similar SMS provider
        $this->sendSms($phone, $message);
    }

    /**
     * Send SMS via Africa's Talking or configured provider
     */
    private function sendSms(string $phone, string $message): void
    {
        // Format phone number (ensure it starts with country code)
        $phone = $this->formatPhoneNumber($phone);

        // Check if SMS is configured
        $apiKey = config('services.sms.api_key');
        $username = config('services.sms.username');
        
        if (!$apiKey || !$username) {
            // Log SMS for development (can be sent via mail or logged)
            Log::info('SMS would be sent', [
                'to' => $phone,
                'message' => $message,
            ]);
            
            // In development, you might want to send via email instead
            // Or use a test SMS service
            return;
        }

        // Send via Africa's Talking API
        try {
            $response = Http::withHeaders([
                'ApiKey' => $apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
            ])->asForm()->post("https://api.africastalking.com/version1/messaging", [
                'username' => $username,
                'to' => $phone,
                'message' => $message,
                'from' => config('services.sms.shortcode', 'TTMS'),
            ]);

            if ($response->successful()) {
                Log::info('SMS sent successfully', [
                    'phone' => $phone,
                    'response' => $response->json(),
                ]);
            } else {
                Log::error('SMS sending failed', [
                    'phone' => $phone,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('SMS sending error', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Format phone number for Kenya (254XXXXXXXXX)
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove spaces and dashes
        $phone = preg_replace('/[\s\-]/', '', $phone);
        
        // If starts with 0, replace with 254
        if (strpos($phone, '0') === 0) {
            $phone = '254' . substr($phone, 1);
        }
        
        // If starts with +, remove it
        if (strpos($phone, '+') === 0) {
            $phone = substr($phone, 1);
        }
        
        // Ensure it starts with 254
        if (strpos($phone, '254') !== 0 && strlen($phone) === 9) {
            $phone = '254' . $phone;
        }
        
        return $phone;
    }
}

