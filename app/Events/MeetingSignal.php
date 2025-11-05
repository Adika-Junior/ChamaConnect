<?php

namespace App\Events;

use App\Models\Meeting;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeetingSignal implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Meeting $meeting,
        public array $payload
    ) {}

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('meeting.'.$this->meeting->id);
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}


