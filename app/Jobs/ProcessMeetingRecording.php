<?php

namespace App\Jobs;

use App\Models\MeetingRecording;
use App\Models\MeetingTranscript;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessMeetingRecording implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $recordingId) {}

    public function handle(): void
    {
        $recording = MeetingRecording::find($this->recordingId);
        if (!$recording) {
            return;
        }

        // Placeholder for transcoding/upload to external storage if needed.
        Log::info('Processing meeting recording', [
            'recording_id' => $recording->id,
            'file_path' => $recording->file_path,
        ]);

        // Immediately dispatch transcription job
        TranscribeMeetingRecording::dispatch($recording->id)->onQueue('transcription');
    }
}


