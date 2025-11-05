<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebhookEvent;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthDashboardController extends Controller
{
    public function index()
    {
        // DB
        $dbOk = true; $dbMsg = 'ok';
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $dbOk = false; $dbMsg = $e->getMessage();
        }

        // Cache
        $cacheOk = true;
        try {
            cache()->put('__health_check', '1', 5);
            $cacheOk = cache()->get('__health_check') === '1';
        } catch (\Throwable $e) { $cacheOk = false; }

        // Queue (best-effort: show default connection/driver)
        $queueDriver = config('queue.default');
        $queueSize = 0;
        try {
            if ($queueDriver === 'redis') {
                $queueSize = Redis::llen(config('queue.connections.redis.queue', 'default'));
            }
        } catch (\Throwable $e) { }

        // Redis stats
        $redisStats = [];
        try {
            $redisStats = Redis::info();
        } catch (\Throwable $e) { }

        // Webhooks summary
        $webhookCounts = [
            'received' => WebhookEvent::where('status','received')->count(),
            'processed' => WebhookEvent::where('status','processed')->count(),
            'failed' => WebhookEvent::where('status','failed')->count(),
        ];
        $webhookErrorRate = $webhookCounts['processed'] + $webhookCounts['failed'] > 0
            ? round(($webhookCounts['failed'] / ($webhookCounts['processed'] + $webhookCounts['failed'])) * 100, 2)
            : 0;
        $recentWebhookEvents = WebhookEvent::orderByDesc('id')->limit(10)->get();

        return view('admin.health.index', [
            'dbOk' => $dbOk,
            'dbMsg' => $dbMsg,
            'cacheOk' => $cacheOk,
            'queueDriver' => $queueDriver,
            'queueSize' => $queueSize,
            'redisStats' => $redisStats,
            'webhookCounts' => $webhookCounts,
            'webhookErrorRate' => $webhookErrorRate,
            'recentWebhookEvents' => $recentWebhookEvents,
        ]);
    }
}


