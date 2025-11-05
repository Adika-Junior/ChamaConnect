<?php

namespace App\Http\Controllers;

use App\Models\BreakoutRoom;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BreakoutRoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Meeting $meeting)
    {
        $rooms = BreakoutRoom::where('meeting_id', $meeting->id)->orderBy('created_at')->get();
        return response()->json($rooms);
    }

    public function store(Request $request, Meeting $meeting)
    {
        // Host or admin only
        if ($meeting->organizer_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $room = BreakoutRoom::create([
            'meeting_id' => $meeting->id,
            'name' => $validated['name'],
            'created_by' => Auth::id(),
        ]);

        return response()->json($room, 201);
    }

    public function destroy(Meeting $meeting, BreakoutRoom $room)
    {
        if ($room->meeting_id !== $meeting->id) {
            abort(404);
        }

        if ($meeting->organizer_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $room->delete();
        return response()->json(['status' => 'deleted']);
    }
}


