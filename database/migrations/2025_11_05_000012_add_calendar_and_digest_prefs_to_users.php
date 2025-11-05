<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'calendar_token')) {
                $table->string('calendar_token', 64)->nullable()->unique()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'digest_frequency')) {
                $table->enum('digest_frequency', ['daily','weekly'])->default('daily')->after('calendar_token');
            }
            if (!Schema::hasColumn('users', 'quiet_hours_start')) {
                $table->time('quiet_hours_start')->nullable()->after('digest_frequency');
            }
            if (!Schema::hasColumn('users', 'quiet_hours_end')) {
                $table->time('quiet_hours_end')->nullable()->after('quiet_hours_start');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'calendar_token')) $table->dropColumn('calendar_token');
            if (Schema::hasColumn('users', 'digest_frequency')) $table->dropColumn('digest_frequency');
            if (Schema::hasColumn('users', 'quiet_hours_start')) $table->dropColumn('quiet_hours_start');
            if (Schema::hasColumn('users', 'quiet_hours_end')) $table->dropColumn('quiet_hours_end');
        });
    }
};


