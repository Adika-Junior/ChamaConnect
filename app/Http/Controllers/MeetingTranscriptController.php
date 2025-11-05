<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingTranscript;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingTranscriptController extends Controller
{
    public function store(Request $request, Meeting $meeting)
    {
        $isParticipant = $meeting->participants()->where('users.id', Auth::id())->exists();
        if (!($isParticipant || $meeting->organizer_id === Auth::id())) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'mimetypes:text/plain,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'max:20480'],
            'contribution_id' => ['nullable', 'exists:contributions,id'],
        ]);

        $transcript = new MeetingTranscript([
            'content' => $validated['content'] ?? null,
        ]);
        if (!empty($validated['file'])) {
            $path = $validated['file']->store('transcripts', 'public');
            $transcript->file_path = $path;
            $transcript->file_name = $validated['file']->getClientOriginalName();
        }
        $transcript->meeting_id = $meeting->id;
        $transcript->contribution_id = $validated['contribution_id'] ?? null;
        $transcript->created_by = Auth::id();
        $transcript->save();

        return back()->with('status', 'Transcript saved');
    }

    public function downloadTxt(Meeting $meeting, MeetingTranscript $transcript)
    {
        if ($transcript->meeting_id !== $meeting->id) abort(404);
        $this->authorizeAccess($meeting);

        $name = 'transcript-'.$meeting->id.'-'.$transcript->id.'.txt';
        $content = $transcript->content ?? '';
        if (empty($content) && $transcript->file_path) {
            $content = storage_path('app/public/'.$transcript->file_path);
            if (is_file($content)) {
                return response()->download($content, $name, ['Content-Type' => 'text/plain']);
            }
        }
        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Content-Disposition' => 'attachment; filename='.$name,
        ]);
    }

    public function printView(Meeting $meeting, MeetingTranscript $transcript)
    {
        if ($transcript->meeting_id !== $meeting->id) abort(404);
        $this->authorizeAccess($meeting);
        return view('meetings.transcripts.print', compact('meeting', 'transcript'));
    }

    private function authorizeAccess(Meeting $meeting): void
    {
        $isParticipant = $meeting->participants()->where('users.id', Auth::id())->exists();
        if (!($isParticipant || $meeting->organizer_id === Auth::id() || optional(Auth::user())->isAdmin())) {
            abort(403);
        }
    }
}


