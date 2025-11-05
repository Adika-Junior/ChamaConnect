<?php

namespace App\Services\Transcription;

use App\Models\MeetingTranscript;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TranscriptionService
{
    private string $provider;
    private ?string $apiKey;

    public function __construct()
    {
        $this->provider = config('services.transcription.provider', 'whisper');
        $this->apiKey = config('services.transcription.api_key');
    }

    public function transcribe(string $audioPath, string $language = 'en'): ?string
    {
        if (!$this->apiKey) {
            Log::warning('Transcription service not configured');
            return null;
        }

        try {
            return match ($this->provider) {
                'whisper' => $this->transcribeWhisper($audioPath, $language),
                'google' => $this->transcribeGoogle($audioPath, $language),
                default => null,
            };
        } catch (\Throwable $e) {
            Log::error('Transcription failed', ['error' => $e->getMessage(), 'path' => $audioPath]);
            return null;
        }
    }

    private function transcribeWhisper(string $audioPath, string $language): ?string
    {
        // Integration with OpenAI Whisper API or self-hosted Whisper
        $apiUrl = config('services.transcription.whisper_url', 'https://api.openai.com/v1/audio/transcriptions');
        
        $response = Http::withToken($this->apiKey)
            ->attach('file', file_get_contents(storage_path('app/public/' . $audioPath)), basename($audioPath))
            ->post($apiUrl, [
                'model' => 'whisper-1',
                'language' => $language,
                'response_format' => 'text',
            ]);

        if ($response->successful()) {
            return $response->body();
        }

        return null;
    }

    private function transcribeGoogle(string $audioPath, string $language): ?string
    {
        // Google Speech-to-Text API integration
        // Implementation placeholder
        return null;
    }

    public function getSupportedLanguages(): array
    {
        return [
            'en' => 'English',
            'sw' => 'Swahili',
            'fr' => 'French',
            'ar' => 'Arabic',
        ];
    }
}

