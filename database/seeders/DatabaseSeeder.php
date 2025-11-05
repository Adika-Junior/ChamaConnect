<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([RolesSeeder::class]);

        // Seed test users for development if not already present
        if (!User::where('email', 'admin@example.com')->exists()) {
            $this->call([
                TestUserSeeder::class,
            ]);
        }

        // Roles assignment handled in RolesSeeder

        // Seed a sample meeting for quick testing
        $this->call([MeetingSeeder::class]);
    }
}
