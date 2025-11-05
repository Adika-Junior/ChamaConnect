<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DeployVerify extends Command
{
    protected $signature = 'deploy:verify {--base-url=}';
    protected $description = 'Run a unified verification checklist after deploy';

    public function handle(): int
    {
        $base = rtrim($this->option('base-url') ?: config('app.url'), '/');

        $checks = [];

        // DB
        try {
            DB::connection()->getPdo();
            $checks['db'] = true;
        } catch (\Throwable $e) {
            $checks['db'] = false;
            $this->error('DB error: '.$e->getMessage());
        }

        // Storage writable
        try {
            $ok = Storage::disk('local')->put('__deploy_verify.txt', 'ok');
            if ($ok) Storage::disk('local')->delete('__deploy_verify.txt');
            $checks['storage'] = $ok;
        } catch (\Throwable $e) {
            $checks['storage'] = false;
        }

        // Key routes 200
        foreach (['/', '/docs', '/login'] as $path) {
            try {
                $res = Http::withoutVerifying()->get($base.$path);
                $checks['route:'.$path] = $res->status() < 400;
            } catch (\Throwable $e) {
                $checks['route:'.$path] = false;
            }
        }

        // Metrics endpoint
        try {
            $res = Http::withoutVerifying()->get($base.'/admin/metrics');
            $checks['metrics'] = $res->status() === 200;
        } catch (\Throwable $e) {
            $checks['metrics'] = false;
        }

        // Output summary
        $this->info('Deploy Verification Results:');
        foreach ($checks as $name => $ok) {
            $this->line(sprintf(' - %-18s %s', $name, $ok ? '[OK]' : '[FAIL]'));
        }

        $ok = collect($checks)->every(fn($v) => $v === true);
        return $ok ? self::SUCCESS : self::FAILURE;
    }
}


