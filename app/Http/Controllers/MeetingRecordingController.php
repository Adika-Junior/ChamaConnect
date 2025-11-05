<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingRecording;
use App\Models\Contribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessRecordingJob;

class MeetingRecordingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Meeting $meeting)
    {
        // Simple gate: must be organizer or a participant
        $isParticipant = $meeting->participants()->where('users.id', Auth::id())->exists();
        if (!($isParticipant || $meeting->organizer_id === Auth::id())) {
            abort(403);
        }

        $validated = $request->validate([
            'recording' => ['required', 'file', 'mimetypes:audio/*,video/*', 'max:512000'], // up to 500MB
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'contribution_id' => ['nullable', 'exists:contributions,id'],
        ]);

        $file = $validated['recording'];
        $path = $file->store('recordings', 'public');

        $rec = new MeetingRecording([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'duration_seconds' => $validated['duration_seconds'] ?? null,
            'processing_status' => 'pending',
        ]);
        $rec->meeting_id = $meeting->id;
        $rec->contribution_id = $validated['contribution_id'] ?? null;
        $rec->created_by = Auth::id();
        $rec->save();

        // Kick off background processing
        ProcessRecordingJob::dispatch($rec->id)->onQueue('recordings');

        // Post a message with link in associated chat if present
        $meeting->loadMissing('chat');
        if ($meeting->chat) {
            $url = Storage::disk('public')->url($rec->file_path);
            $message = new \App\Models\Message();
            $message->chat_id = $meeting->chat->id;
            $message->user_id = Auth::id();
            $message->content = 'Meeting recording uploaded: ' . $rec->file_name . "\n" . $url;
            $message->save();
            event(new \App\Events\MessageSent($message));
        }

        return back()->with('status', 'Recording uploaded');
    }
}


