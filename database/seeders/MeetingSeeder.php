<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Meeting;
use App\Models\User;
use App\Models\Contribution;

class MeetingSeeder extends Seeder
{
    public function run(): void
    {
        $organizer = User::first();
        if (!$organizer) {
            return;
        }

        $contribution = Contribution::first();

        Meeting::firstOrCreate(
            ['title' => 'Kickoff Meeting'],
            [
                'description' => 'Initial planning session',
                'type' => 'video_conference',
                'scheduled_at' => now()->addDay(),
                'duration' => 60,
                'meeting_link' => 'https://meet.example.com/kickoff',
                'status' => 'scheduled',
                'organizer_id' => $organizer->id,
                'contribution_id' => $contribution?->id,
            ]
        );
    }
}


