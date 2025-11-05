<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RouteCache extends Command
{
    protected $signature = 'route:cache-warm';
    protected $description = 'Warm route cache for better performance';

    public function handle(): int
    {
        try {
            Artisan::call('route:cache');
            $this->info('Route cache created successfully.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Route cache failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}

