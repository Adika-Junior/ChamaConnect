<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Observability\Metrics;

class MeetingControlController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Control meeting participants (host/organizer only)
     * Actions: mute, unmute, remove, lock
     */
    public function control(Request $request, Meeting $meeting)
    {
        // Check if user is organizer
        if ($meeting->organizer_id !== auth()->id()) {
            return response()->json(['error' => 'Only the meeting organizer can control participants'], 403);
        }

        $validated = $request->validate([
            'action' => 'required|string|in:mute,unmute,remove,lock,unlock,admit_all',
            'participant_id' => 'nullable|integer|exists:users,id',
        ]);

        $action = $validated['action'];
        $participantId = $validated['participant_id'] ?? null;

        // Handle lock/unlock actions
        if ($action === 'lock') {
            $meeting->update(['is_locked' => true]);
        } elseif ($action === 'unlock') {
            $meeting->update(['is_locked' => false]);
        }

        // Broadcast control event to meeting participants
        broadcast(new \App\Events\MeetingControl($meeting, [
            'action' => $action,
            'participant_id' => $participantId,
            'controlled_by' => auth()->id(),
        ]))->toOthers();

        Metrics::inc('meeting_controls_total');

        return response()->json([
            'success' => true,
            'action' => $action,
            'message' => match ($action) {
                'lock', 'unlock' => "Meeting {$action}ed successfully",
                'admit_all' => 'All waiting participants admitted',
                default => "Participant {$action}d successfully",
            }
        ]);
    }
}

