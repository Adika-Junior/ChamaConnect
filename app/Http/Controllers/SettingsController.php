<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $user->ensureCalendarToken();
        $icalUrl = route('calendar.ical', ['token' => $user->calendar_token]);
        return view('settings.index', compact('user', 'icalUrl'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'digest_frequency' => 'nullable|in:daily,weekly',
            'quiet_hours_start' => 'nullable|date_format:H:i',
            'quiet_hours_end' => 'nullable|date_format:H:i',
        ]);

        $user = Auth::user();
        $user->update($validated);

        if ($request->has('regenerate_calendar_token')) {
            $user->calendar_token = Str::random(64);
            $user->save();
            return redirect()->route('settings.index')->with('status', 'Calendar token regenerated. Update your calendar subscription.');
        }

        return redirect()->route('settings.index')->with('status', 'Settings updated.');
    }
}

