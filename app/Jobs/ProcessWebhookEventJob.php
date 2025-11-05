<?php

namespace App\Jobs;

use App\Models\WebhookEvent;
use App\Services\Payments\MpesaWebhookProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessWebhookEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private int $eventId) {}

    public function handle(MpesaWebhookProcessor $processor): void
    {
        $event = WebhookEvent::find($this->eventId);
        if (!$event) {
            return;
        }

        $processor->process($event);
    }
}


