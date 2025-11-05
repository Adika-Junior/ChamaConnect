<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Verify meeting password
     */
    public function verify(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'password' => 'required|string',
        ]);

        if ($meeting->password && $meeting->password !== $validated['password']) {
            return back()->withErrors(['password' => 'Incorrect meeting password.']);
        }

        // Store password verification in session
        session(["meeting_{$meeting->id}_password_verified" => true]);

        return redirect()->route('meetings.show', $meeting);
    }
}

