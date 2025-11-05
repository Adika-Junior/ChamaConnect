<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User if not exists
        User::firstOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin User',
            'password' => Hash::make('password123'),
            'phone' => '254712345678',
            'status' => 'active',
            'employee_id' => 'EMP001',
            'email_verified_at' => now(),
            'approved_at' => now(),
        ]);

        // Create Regular User (Pending)
        User::firstOrCreate(['email' => 'john@example.com'], [
            'name' => 'John Doe',
            'password' => Hash::make('password123'),
            'phone' => '254712345679',
            'status' => 'pending',
            'employee_id' => 'EMP002',
        ]);

        // Create Active User
        User::firstOrCreate(['email' => 'jane@example.com'], [
            'name' => 'Jane Smith',
            'password' => Hash::make('password123'),
            'phone' => '254712345680',
            'status' => 'active',
            'employee_id' => 'EMP003',
            'email_verified_at' => now(),
            'approved_at' => now(),
        ]);

        // Create Suspended User
        User::firstOrCreate(['email' => 'suspended@example.com'], [
            'name' => 'Bad User',
            'password' => Hash::make('password123'),
            'phone' => '254712345681',
            'status' => 'suspended',
            'employee_id' => 'EMP004',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Test users created:');
        $this->command->info('Admin: admin@example.com / password123');
        $this->command->info('Pending: john@example.com / password123');
        $this->command->info('Active: jane@example.com / password123');
        $this->command->info('Suspended: suspended@example.com / password123');
    }
}

