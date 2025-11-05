<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Role::with('department');
        
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        $roles = $query->orderBy('department_id')->orderBy('level')->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('roles.index', compact('roles', 'departments'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        return view('roles.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'level' => 'required|integer|min:1|max:10',
        ]);

        // Check uniqueness per department
        $exists = Role::where('name', $validated['name'])
            ->where('department_id', $validated['department_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'A role with this name already exists in the selected department.']);
        }

        $role = Role::create($validated);

        return redirect()->route('roles.show', $role)
            ->with('status', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        $role->load(['department', 'users']);
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $departments = Department::orderBy('name')->get();
        return view('roles.edit', compact('role', 'departments'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'level' => 'required|integer|min:1|max:10',
        ]);

        // Check uniqueness per department
        $exists = Role::where('name', $validated['name'])
            ->where('department_id', $validated['department_id'])
            ->where('id', '!=', $role->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'A role with this name already exists in the selected department.']);
        }

        $role->update($validated);

        return redirect()->route('roles.show', $role)
            ->with('status', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete role with assigned users.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('status', 'Role deleted successfully.');
    }
}

