<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWebhookEventJob;
use App\Models\WebhookEvent;
use App\Services\Payments\MpesaWebhookProcessor;
use Illuminate\Http\Request;

class PaymentsWebhookAdminController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $query = WebhookEvent::query()->orderByDesc('id');
        if ($status) {
            $query->where('status', $status);
        }
        $events = $query->paginateDefault();
        return view('admin.webhooks.index', compact('events', 'status'));
    }

    public function retry(WebhookEvent $event)
    {
        // Reset status before retry
        $event->status = 'received';
        $event->error = null;
        $event->processed_at = null;
        $event->save();

        ProcessWebhookEventJob::dispatch($event->id);

        return redirect()->back()->with('status', 'Retry dispatched for event #'.$event->id);
    }
}


