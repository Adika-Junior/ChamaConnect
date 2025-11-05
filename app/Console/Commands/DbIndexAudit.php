<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DbIndexAudit extends Command
{
    protected $signature = 'db:index-audit';
    protected $description = 'Audit database indexes for performance';

    public function handle(): int
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();
        $tableKey = 'Tables_in_' . $dbName;

        $issues = [];
        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            $indexes = DB::select("SHOW INDEXES FROM `{$tableName}`");
            
            // Check for tables without indexes (except auto-generated primary keys)
            $hasNonPrimaryIndex = false;
            foreach ($indexes as $idx) {
                if ($idx->Key_name !== 'PRIMARY') {
                    $hasNonPrimaryIndex = true;
                    break;
                }
            }

            // Check for large tables without indexes
            $rowCount = DB::table($tableName)->count();
            if ($rowCount > 1000 && !$hasNonPrimaryIndex) {
                $issues[] = "Table `{$tableName}` has {$rowCount} rows but no indexes (except primary key)";
            }
        }

        if (empty($issues)) {
            $this->info('No index issues found.');
            return self::SUCCESS;
        }

        $this->warn('Found ' . count($issues) . ' potential index issues:');
        foreach ($issues as $issue) {
            $this->line('  - ' . $issue);
        }
        return self::SUCCESS;
    }
}

