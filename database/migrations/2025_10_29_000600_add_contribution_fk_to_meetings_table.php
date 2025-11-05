<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('meetings') || !Schema::hasTable('contributions')) {
            return;
        }

        Schema::table('meetings', function (Blueprint $table) {
            if (!Schema::hasColumn('meetings', 'contribution_id')) {
                $table->unsignedBigInteger('contribution_id')->nullable()->index();
            }
            // Add FK if not already present
            $table->foreign('contribution_id')->references('id')->on('contributions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('meetings')) {
            return;
        }
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropForeign(['contribution_id']);
        });
    }
};


