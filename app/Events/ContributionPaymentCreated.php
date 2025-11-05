<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContributionPaymentCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $contributionId,
        public float $amount,
        public float $total
    ) {}

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('contribution.' . $this->contributionId);
    }

    public function broadcastAs(): string
    {
        return 'ContributionPaymentCreated';
    }
}


