<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JanusController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get Janus room info for meeting
     * Returns room ID and Janus server URL - frontend creates session
     */
    public function session(Meeting $meeting)
    {
        // Generate a unique room ID based on meeting ID
        // Janus rooms must be > 0, so we add offset
        $roomId = 1000 + $meeting->id;
        
        return response()->json([
            'room_id' => $roomId,
            'ws_url' => config('services.janus.ws_url', 'ws://janus:8088/janus'),
            'janus_url' => config('services.janus.url', 'http://janus:8088/janus'),
            'max_publishers' => config('services.webrtc.max_participants', 50),
            'description' => "Meeting: {$meeting->title}",
        ]);
    }
}

