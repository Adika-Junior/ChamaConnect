<?php

namespace App\Services\Observability;

use Illuminate\Support\Facades\Cache;

class Metrics
{
    private const CACHE_PREFIX = 'metrics:';

    public static function inc(string $name, array $labels = [], int $value = 1): void
    {
        $key = self::key($name, $labels);
        Cache::increment($key, $value);
    }

    public static function observe(string $name, float $value, array $labels = []): void
    {
        // Simple histogram: store min/max/sum/count
        $key = self::key($name, $labels);
        $stats = Cache::get($key, ['min' => $value, 'max' => $value, 'sum' => 0, 'count' => 0]);
        $stats['min'] = min($stats['min'], $value);
        $stats['max'] = max($stats['max'], $value);
        $stats['sum'] += $value;
        $stats['count']++;
        Cache::put($key, $stats, now()->addDays(7));
    }

    public static function getHistogram(string $name, array $labels = []): ?array
    {
        return Cache::get(self::key($name, $labels));
    }

    public static function renderPrometheus(): string
    {
        // Render a subset of known metrics
        $lines = [];
        foreach ([
            'webhook_received_total',
            'webhook_processed_total',
            'webhook_failed_total',
            'meeting_controls_total',
        ] as $metric) {
            // Render without labels (simple counters)
            $val = (int) (Cache::get(self::key($metric)) ?? 0);
            $lines[] = "# TYPE {$metric} counter";
            $lines[] = "{$metric} {$val}";
        }

        // Render histograms
        $histograms = [
            'webhook_processing_duration_seconds',
            'request_duration_seconds',
        ];
        foreach ($histograms as $h) {
            $stats = self::getHistogram($h);
            if ($stats) {
                $lines[] = "# TYPE {$h} histogram";
                $lines[] = "{$h}_count {$stats['count']}";
                $lines[] = "{$h}_sum {$stats['sum']}";
                $lines[] = "{$h}_min {$stats['min']}";
                $lines[] = "{$h}_max {$stats['max']}";
            }
        }

        return implode("\n", $lines) . "\n";
    }

    private static function key(string $name, array $labels = []): string
    {
        if (empty($labels)) return self::CACHE_PREFIX.$name;
        ksort($labels);
        return self::CACHE_PREFIX.$name.':'.md5(json_encode($labels));
    }
}


