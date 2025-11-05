<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'backup:db {--disk=local}';
    protected $description = 'Create a compressed database dump and store it on the specified disk (default: local)';

    public function handle(): int
    {
        $connection = config('database.connections.mysql');
        $db = $connection['database'] ?? env('DB_DATABASE');
        $user = $connection['username'] ?? env('DB_USERNAME');
        $pass = $connection['password'] ?? env('DB_PASSWORD');
        $host = $connection['host'] ?? env('DB_HOST', '127.0.0.1');
        $port = $connection['port'] ?? env('DB_PORT', 3306);

        $filename = sprintf('backups/db_%s_%s.sql.gz', $db, date('Ymd_His'));
        $tmpFile = sys_get_temp_dir() . '/' . basename($filename);

        $cmd = sprintf(
            'mysqldump -h%s -P%s -u%s -p%s %s | gzip > %s',
            escapeshellarg($host),
            escapeshellarg((string) $port),
            escapeshellarg($user),
            escapeshellarg($pass),
            escapeshellarg($db),
            escapeshellarg($tmpFile)
        );

        $this->info('Running: mysqldump ...');
        $result = null;
        system($cmd, $result);
        if ($result !== 0 || !file_exists($tmpFile)) {
            $this->error('Database dump failed');
            return self::FAILURE;
        }

        $disk = $this->option('disk');
        $this->info("Uploading to disk '{$disk}' as {$filename} ...");
        $ok = Storage::disk($disk)->put($filename, file_get_contents($tmpFile));
        @unlink($tmpFile);

        if (!$ok) {
            $this->error('Upload failed');
            return self::FAILURE;
        }

        $this->info('Backup complete: ' . $filename);
        return self::SUCCESS;
    }
}


