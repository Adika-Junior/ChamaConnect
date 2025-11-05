<?php

namespace App\Services;

use App\Models\Meeting;
use Illuminate\Support\Facades\DB;

class MeetingParticipantService
{
    public function syncParticipants(Meeting $meeting, array $userIds, bool $detaching = true): void
    {
        DB::transaction(function () use ($meeting, $userIds, $detaching) {
            if ($detaching) {
                $meeting->participants()->sync($userIds);
            } else {
                $meeting->participants()->syncWithoutDetaching($userIds);
            }
            $meeting->syncParticipantsToChat();
        });
    }

    public function attachParticipant(Meeting $meeting, int $userId): void
    {
        $meeting->participants()->syncWithoutDetaching([$userId]);
        $meeting->syncParticipantsToChat();
    }

    public function detachParticipant(Meeting $meeting, int $userId): void
    {
        $meeting->participants()->detach($userId);
        $meeting->syncParticipantsToChat();
    }
}

