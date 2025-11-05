<?php

namespace App\Http\Controllers;

use App\Events\MeetingSignal;
use App\Models\Meeting;
use Illuminate\Http\Request;

class MeetingSignalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function send(Request $request, Meeting $meeting)
    {
        $data = $request->validate([
            'type' => 'required|string',
            'from' => 'required|integer',
            'to' => 'nullable|integer',
            'signal' => 'array|nullable',
            'candidate' => 'array|nullable',
        ]);

        broadcast(new MeetingSignal($meeting, $data))->toOthers();
        return response()->json(['ok' => true]);
    }
}


