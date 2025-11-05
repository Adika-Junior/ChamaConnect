<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Services\MeetingParticipantService;
use Illuminate\Support\Facades\Auth;

class MeetingParticipationController extends Controller
{
    public function __construct(private MeetingParticipantService $participantService)
    {
        $this->middleware('auth');
    }

    public function join(Meeting $meeting)
    {
        // Check if meeting is locked
        if ($meeting->is_locked && $meeting->organizer_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return back()->withErrors(['error' => 'This meeting is locked. No new participants can join.']);
        }
        
        // Check password if required
        $passwordVerified = session("meeting_{$meeting->id}_password_verified", false);
        if ($meeting->password && !$passwordVerified && $meeting->organizer_id !== Auth::id()) {
            return redirect()->route('meetings.show', $meeting)
                ->withErrors(['password' => 'Meeting password required.']);
        }
        
        $max = (int) config('services.webrtc.max_participants', 2);
        $current = $meeting->participants()->count();
        if ($current >= $max) {
            return back()->with('error', 'Meeting is full (max '.$max.' participants).');
        }
        
        $this->participantService->attachParticipant($meeting, Auth::user());
        
        if ($meeting->has_waiting_room && $meeting->organizer_id !== Auth::id()) {
            return back()->with('status', 'You have joined the waiting room. Waiting for host to admit you.');
        }
        
        return back()->with('status', 'You have joined the meeting.');
    }

    public function leave(Meeting $meeting)
    {
        $this->participantService->detachParticipant($meeting, Auth::user());
        return back()->with('status', 'You have left the meeting.');
    }
}


