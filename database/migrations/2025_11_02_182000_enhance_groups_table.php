<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->boolean('is_public')->default(false)->after('type');
            $table->boolean('accepting_applications')->default(true)->after('is_public');
            $table->text('application_requirements')->nullable()->after('accepting_applications');
            $table->string('registration_number')->nullable()->after('application_requirements');
            $table->date('registered_at')->nullable()->after('registration_number');
            $table->text('by_laws')->nullable()->after('registered_at');
            $table->string('location')->nullable()->after('by_laws');
            $table->string('contact_email')->nullable()->after('location');
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->integer('min_members')->default(10)->after('contact_phone');
            $table->integer('current_members')->default(0)->after('min_members');
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn([
                'is_public', 'accepting_applications', 'application_requirements',
                'registration_number', 'registered_at', 'by_laws', 'location',
                'contact_email', 'contact_phone', 'min_members', 'current_members'
            ]);
        });
    }
};

