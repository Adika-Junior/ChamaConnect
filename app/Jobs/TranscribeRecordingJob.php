<?php

namespace App\Jobs;

use App\Models\MeetingRecording;
use App\Models\MeetingTranscript;
use App\Services\Transcription\TranscriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TranscribeRecordingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $recordingId;
    public $language;

    public function __construct(int $recordingId, string $language = 'en')
    {
        $this->recordingId = $recordingId;
        $this->language = $language;
    }

    public function handle(TranscriptionService $service): void
    {
        $recording = MeetingRecording::find($this->recordingId);
        if (!$recording || $recording->processing_status !== 'completed') {
            return;
        }

        $transcript = $service->transcribe($recording->file_path, $this->language);
        
        if ($transcript) {
            MeetingTranscript::create([
                'meeting_id' => $recording->meeting_id,
                'content' => $transcript,
                'created_by' => $recording->created_by,
                'metadata' => ['language' => $this->language, 'recording_id' => $recording->id],
            ]);
        }
    }
}

