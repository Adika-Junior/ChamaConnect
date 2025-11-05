<?php

namespace App\Console\Commands;

use App\Models\Chat;
use App\Models\Meeting;
use Illuminate\Console\Command;

class EnsureMeetingChats extends Command
{
    protected $signature = 'meetings:ensure-chats';
    protected $description = 'Create and link chats for meetings missing one, and sync participants';

    public function handle(): int
    {
        $created = 0;
        $synced = 0;
        
        // Create missing chats
        Meeting::with('participants')->whereDoesntHave('chat')->chunkById(100, function ($batch) use (&$created) {
            foreach ($batch as $meeting) {
                $chat = Chat::create([
                    'type' => 'group',
                    'name' => $meeting->title,
                    'created_by' => $meeting->organizer_id,
                    'meeting_id' => $meeting->id,
                ]);
                $meeting->syncParticipantsToChat();
                $created++;
            }
        });
        
        // Sync participants for existing chats
        Meeting::with(['participants', 'chat'])->whereHas('chat')->chunkById(100, function ($batch) use (&$synced) {
            foreach ($batch as $meeting) {
                $meeting->syncParticipantsToChat();
                $synced++;
            }
        });
        
        $this->info("Created {$created} chats and synced {$synced} existing chats.");
        return self::SUCCESS;
    }
}


