<?php

namespace Database\Seeders;

use App\Models\GroupRoleTemplate;
use Illuminate\Database\Seeder;

class GroupRoleTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'permissions' => ['groups.*', 'contributions.*', 'payments.*', 'members.*', 'invitations.*'],
                'description' => 'Full access to all group functions and settings',
            ],
            [
                'name' => 'treasurer',
                'display_name' => 'Treasurer',
                'permissions' => ['groups.view', 'contributions.view', 'contributions.create', 'payments.*', 'members.view'],
                'description' => 'Can manage payments, view contributions, and access financial reports',
            ],
            [
                'name' => 'secretary',
                'display_name' => 'Secretary',
                'permissions' => ['groups.view', 'contributions.*', 'members.*', 'invitations.*'],
                'description' => 'Can manage members, invitations, and contributions',
            ],
            [
                'name' => 'member',
                'display_name' => 'Member',
                'permissions' => ['groups.view', 'contributions.view', 'contributions.create'],
                'description' => 'Basic member access to view and create contributions',
            ],
        ];

        foreach ($templates as $t) {
            GroupRoleTemplate::updateOrCreate(['name' => $t['name']], $t);
        }
    }
}

