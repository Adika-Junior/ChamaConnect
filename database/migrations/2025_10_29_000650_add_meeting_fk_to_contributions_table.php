<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('contributions') || !Schema::hasTable('meetings')) {
            return;
        }

        Schema::table('contributions', function (Blueprint $table) {
            if (!Schema::hasColumn('contributions', 'meeting_id')) {
                $table->unsignedBigInteger('meeting_id')->nullable()->index();
            }
            $table->foreign('meeting_id')->references('id')->on('meetings')->onDelete('set null');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('contributions')) {
            return;
        }

        Schema::table('contributions', function (Blueprint $table) {
            $table->dropForeign(['meeting_id']);
        });
    }
};


