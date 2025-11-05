<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationPreferenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $preferences = Auth::user()->notificationPreferences;
        $unreadCount = Auth::user()->unreadNotifications()->count();

        return view('notifications.index', compact('preferences', 'unreadCount'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'preferences' => 'required|array',
            'preferences.*.type' => 'required|string',
            'preferences.*.email' => 'boolean',
            'preferences.*.sms' => 'boolean',
            'preferences.*.in_app' => 'boolean',
            'preferences.*.push' => 'boolean',
            'quiet_hours' => 'nullable|array',
            'quiet_hours.start' => 'nullable|date_format:H:i',
            'quiet_hours.end' => 'nullable|date_format:H:i',
        ]);

        foreach ($validated['preferences'] as $pref) {
            NotificationPreference::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'type' => $pref['type'],
                ],
                [
                    'email' => $pref['email'] ?? false,
                    'sms' => $pref['sms'] ?? false,
                    'in_app' => $pref['in_app'] ?? true,
                    'push' => $pref['push'] ?? false,
                    'quiet_hours' => $validated['quiet_hours'] ?? null,
                ]
            );
        }

        return back()->with('status', 'Notification preferences updated successfully.');
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('status', 'All notifications marked as read.');
    }
}

