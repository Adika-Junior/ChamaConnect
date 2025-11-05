<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Contribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $meetings = Meeting::with(['organizer', 'contribution', 'chat'])
            ->orderByDesc('scheduled_at')
            ->paginate(15);

        return view('meetings.index', compact('meetings'));
    }

    public function create()
    {
        $contributions = Contribution::orderByDesc('created_at')->get(['id','title']);
        return view('meetings.create', compact('contributions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:online,physical,hybrid',
            'scheduled_at' => 'required|date',
            'duration' => 'nullable|integer|min:0',
            'meeting_link' => 'nullable|url',
            'contribution_id' => 'nullable|exists:contributions,id',
            'has_waiting_room' => 'boolean',
            'password' => 'nullable|string|max:255',
            'is_locked' => 'boolean',
        ]);

        $validated['organizer_id'] = Auth::id();
        $validated['has_waiting_room'] = $request->has('has_waiting_room');
        $validated['is_locked'] = $request->has('is_locked');

        $meeting = Meeting::create($validated);

        return redirect()->route('meetings.show', $meeting)->with('status', 'Meeting created.');
    }

    public function show(Meeting $meeting)
    {
        $meeting->load(['organizer', 'participants', 'contribution', 'chat']);
        
        // Check if meeting is locked
        if ($meeting->is_locked && $meeting->organizer_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return back()->withErrors(['error' => 'This meeting is locked. No new participants can join.']);
        }
        
        // Check password if required
        $passwordVerified = session("meeting_{$meeting->id}_password_verified", false);
        if ($meeting->password && !$passwordVerified && $meeting->organizer_id !== Auth::id()) {
            return view('meetings.password', compact('meeting'));
        }
        
        return view('meetings.show', compact('meeting'));
    }

    public function edit(Meeting $meeting)
    {
        $contributions = Contribution::orderByDesc('created_at')->get(['id','title']);
        return view('meetings.edit', compact('meeting', 'contributions'));
    }

    public function calendar()
    {
        return view('meetings.calendar');
    }

    public function feed(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $query = Meeting::query();
        if ($start && $end) {
            $query->whereBetween('scheduled_at', [$start, $end]);
        }
        $meetings = $query->get();

        $events = $meetings->map(function (Meeting $m) {
            $endTime = $m->scheduled_at && $m->duration
                ? $m->scheduled_at->copy()->addMinutes($m->duration)
                : $m->scheduled_at;
            return [
                'id' => (string) $m->id,
                'title' => $m->title,
                'start' => optional($m->scheduled_at)->toIso8601String(),
                'end' => optional($endTime)->toIso8601String(),
                'url' => route('meetings.show', $m),
            ];
        });

        return response()->json($events);
    }

    public function ics(Meeting $meeting)
    {
        $dtStart = optional($meeting->scheduled_at)->format('Ymd\THis\Z');
        $dtEnd = $meeting->scheduled_at && $meeting->duration
            ? $meeting->scheduled_at->copy()->addMinutes($meeting->duration)->format('Ymd\THis\Z')
            : $dtStart;
        $summary = addcslashes($meeting->title ?? 'Meeting', ",;\\");
        $desc = addcslashes((string) ($meeting->description ?? ''), ",;\\\n");
        $url = route('meetings.show', $meeting);

        $ics = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//TTMS//Meetings//EN\r\n".
            "BEGIN:VEVENT\r\nUID:meeting-{$meeting->id}@ttms\r\nDTSTAMP:".gmdate('Ymd\THis\Z')."\r\n".
            "DTSTART:{$dtStart}\r\nDTEND:{$dtEnd}\r\nSUMMARY:{$summary}\r\nDESCRIPTION:{$desc}\\n{$url}\r\n".
            (isset($meeting->meeting_link) ? "URL:{$meeting->meeting_link}\r\n" : '').
            "END:VEVENT\r\nEND:VCALENDAR\r\n";

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=meeting-'.$meeting->id.'.ics',
        ]);
    }

    public function update(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:online,physical,hybrid',
            'scheduled_at' => 'required|date',
            'duration' => 'nullable|integer|min:0',
            'meeting_link' => 'nullable|url',
            'status' => 'nullable|string|in:scheduled,ongoing,completed,cancelled',
            'contribution_id' => 'nullable|exists:contributions,id',
            'has_waiting_room' => 'boolean',
            'password' => 'nullable|string|max:255',
            'is_locked' => 'boolean',
        ]);

        $validated['has_waiting_room'] = $request->has('has_waiting_room');
        $validated['is_locked'] = $request->has('is_locked');
        
        $meeting->update($validated);

        return redirect()->route('meetings.show', $meeting)->with('status', 'Meeting updated.');
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();
        return redirect()->route('meetings.index')->with('status', 'Meeting deleted.');
    }
}


