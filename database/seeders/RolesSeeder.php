<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator']);
        Role::firstOrCreate(['name' => 'manager'], ['display_name' => 'Manager']);
        Role::firstOrCreate(['name' => 'member'], ['display_name' => 'Member']);

        $user = User::where('email', 'admin@example.com')->first();
        if ($user && !$user->roles()->where('name','admin')->exists()) {
            $user->roles()->attach($admin->id);
        }
    }
}
