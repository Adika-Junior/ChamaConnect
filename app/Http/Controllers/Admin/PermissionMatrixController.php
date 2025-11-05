<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\GroupRoleTemplate;
use App\Models\Role;
use Illuminate\Http\Request;

class PermissionMatrixController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('category')->orderBy('name')->get();
        $roles = Role::with('permissions')->get();
        $templates = GroupRoleTemplate::all();

        return view('admin.permissions.matrix', compact('permissions', 'roles', 'templates'));
    }

    public function updateRole(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return back()->with('status', 'Role permissions updated.');
    }
}

