<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Campaigns
            ['name' => 'campaigns.create', 'display_name' => 'Create Campaigns', 'category' => 'campaigns', 'description' => 'Create new fundraising campaigns'],
            ['name' => 'campaigns.edit', 'display_name' => 'Edit Campaigns', 'category' => 'campaigns', 'description' => 'Edit existing campaigns'],
            ['name' => 'campaigns.delete', 'display_name' => 'Delete Campaigns', 'category' => 'campaigns', 'description' => 'Delete campaigns'],
            ['name' => 'campaigns.approve', 'display_name' => 'Approve Campaigns', 'category' => 'campaigns', 'description' => 'Approve pending campaigns'],
            ['name' => 'campaigns.moderate_wall', 'display_name' => 'Moderate Donor Wall', 'category' => 'campaigns', 'description' => 'Moderate donor wall entries'],
            
            // Contributions
            ['name' => 'contributions.create', 'display_name' => 'Create Contributions', 'category' => 'contributions', 'description' => 'Create new contribution requests'],
            ['name' => 'contributions.approve', 'display_name' => 'Approve Contributions', 'category' => 'contributions', 'description' => 'Approve pending contributions'],
            ['name' => 'contributions.export', 'display_name' => 'Export Contributions', 'category' => 'contributions', 'description' => 'Export contribution data'],
            
            // Meetings
            ['name' => 'meetings.create', 'display_name' => 'Create Meetings', 'category' => 'meetings', 'description' => 'Schedule new meetings'],
            ['name' => 'meetings.control', 'display_name' => 'Control Meetings', 'category' => 'meetings', 'description' => 'Use host controls in meetings'],
            ['name' => 'meetings.record', 'display_name' => 'Record Meetings', 'category' => 'meetings', 'description' => 'Record meeting sessions'],
            
            // Groups
            ['name' => 'groups.manage_members', 'display_name' => 'Manage Group Members', 'category' => 'groups', 'description' => 'Add/remove group members'],
            ['name' => 'groups.view_finances', 'display_name' => 'View Group Finances', 'category' => 'groups', 'description' => 'View financial reports'],
            ['name' => 'groups.manage_expenses', 'display_name' => 'Manage Expenses', 'category' => 'groups', 'description' => 'Create and approve expenses'],
            
            // Admin
            ['name' => 'admin.access', 'display_name' => 'Access Admin Panel', 'category' => 'admin', 'description' => 'Access administrative features'],
            ['name' => 'admin.manage_users', 'display_name' => 'Manage Users', 'category' => 'admin', 'description' => 'Create, edit, and delete users'],
            ['name' => 'admin.manage_roles', 'display_name' => 'Manage Roles', 'category' => 'admin', 'description' => 'Manage role templates and permissions'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']],
                $perm
            );
        }
    }
}

