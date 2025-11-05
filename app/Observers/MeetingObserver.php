<?php

namespace App\Observers;

use App\Models\Chat;
use App\Models\Meeting;

class MeetingObserver
{
    public function created(Meeting $meeting): void
    {
        // Create a chat for this meeting if not present
        if (!$meeting->chat) {
            $chat = Chat::create([
                'type' => 'group',
                'name' => $meeting->title,
                'created_by' => $meeting->organizer_id,
                'meeting_id' => $meeting->id,
            ]);
            // Sync all participants including organizer
            $meeting->syncParticipantsToChat();
        }
    }

    public function updated(Meeting $meeting): void
    {
        // If participants were updated, sync to chat
        if ($meeting->wasChanged() && $meeting->chat) {
            $meeting->syncParticipantsToChat();
        }
    }
}


