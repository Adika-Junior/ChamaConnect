<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\User;

class MeetingCalendarFeedController extends Controller
{
    public function feed(string $token)
    {
        $user = User::where('calendar_token', $token)->firstOrFail();

        // Upcoming meetings where user is organizer or participant
        $meetings = Meeting::query()
            ->where(function ($q) use ($user) {
                $q->where('organizer_id', $user->id)
                  ->orWhereHas('participants', function ($p) use ($user) {
                      $p->where('users.id', $user->id);
                  });
            })
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>=', now()->subDays(7))
            ->orderBy('scheduled_at', 'asc')
            ->limit(200)
            ->get();

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//TTMS//UserCalendar//EN',
        ];

        foreach ($meetings as $m) {
            $start = optional($m->scheduled_at)->format('Ymd\THis\Z');
            $end = $m->scheduled_at && $m->duration ? $m->scheduled_at->copy()->addMinutes($m->duration)->format('Ymd\THis\Z') : $start;
            $summary = addcslashes($m->title ?? 'Meeting', ",;\\");
            $desc = addcslashes((string) ($m->description ?? ''), ",;\\\n");
            $url = route('meetings.show', $m);
            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID=user-'.$user->id.'-meeting-'.$m->id.'@ttms';
            $lines[] = 'DTSTAMP:'.gmdate('Ymd\THis\Z');
            $lines[] = 'DTSTART:'.$start;
            $lines[] = 'DTEND:'.$end;
            $lines[] = 'SUMMARY:'.$summary;
            $lines[] = 'DESCRIPTION:'.$desc.'\\n'.$url;
            if (!empty($m->meeting_link)) $lines[] = 'URL:'.$m->meeting_link;
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';
        $ics = implode("\r\n", $lines)."\r\n";

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'inline; filename=user-'.$user->id.'-meetings.ics',
        ]);
    }
}


