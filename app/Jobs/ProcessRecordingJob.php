<?php

namespace App\Jobs;

use App\Models\MeetingRecording;
use App\Services\Recordings\RecordingProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessRecordingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $recordingId;

    public function __construct(int $recordingId)
    {
        $this->recordingId = $recordingId;
    }

    public function handle(RecordingProcessor $processor): void
    {
        $recording = MeetingRecording::find($this->recordingId);
        if (!$recording) {
            return;
        }

        $processor->process($recording);
    }
}

