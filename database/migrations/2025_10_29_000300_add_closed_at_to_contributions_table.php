<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('contributions', 'closed_at')) {
            Schema::table('contributions', function (Blueprint $table) {
                $table->timestamp('closed_at')->nullable()->after('approved_at');
                $table->index(['status', 'deadline']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('contributions', 'closed_at')) {
            Schema::table('contributions', function (Blueprint $table) {
                $table->dropColumn('closed_at');
            });
        }
    }
};


