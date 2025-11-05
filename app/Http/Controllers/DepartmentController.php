<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $departments = Department::with(['parent', 'children', 'users', 'roles'])
            ->orderBy('name')
            ->get();

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        return view('departments.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:departments,id',
        ]);

        $department = Department::create($validated);

        return redirect()->route('departments.show', $department)
            ->with('status', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $department->load(['parent', 'children', 'users', 'roles', 'usersWithRoles']);
        return view('departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $departments = Department::where('id', '!=', $department->id)->orderBy('name')->get();
        return view('departments.edit', compact('department', 'departments'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:departments,id|not_in:' . $department->id,
        ]);

        $department->update($validated);

        return redirect()->route('departments.show', $department)
            ->with('status', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        // Prevent deletion if has users or is a parent
        if ($department->users()->count() > 0) {
            return redirect()->route('departments.index')
                ->with('error', 'Cannot delete department with assigned users.');
        }

        if ($department->children()->count() > 0) {
            return redirect()->route('departments.index')
                ->with('error', 'Cannot delete department with sub-departments.');
        }

        $department->delete();

        return redirect()->route('departments.index')
            ->with('status', 'Department deleted successfully.');
    }
}

