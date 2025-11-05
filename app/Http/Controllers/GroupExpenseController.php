<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Group $group)
    {
        if (!$group->isMember(Auth::user())) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'nullable|string|max:255',
        ]);

        $validated['group_id'] = $group->id;
        $validated['requested_by'] = Auth::id();
        $validated['status'] = 'pending';

        $expense = GroupExpense::create($validated);

        return back()->with('status', 'Expense request submitted successfully.');
    }

    public function approve(Request $request, Group $group, GroupExpense $expense)
    {
        // Only treasurer or admin can approve
        if (!$group->hasRole(Auth::user(), 'treasurer') && !$group->hasRole(Auth::user(), 'admin')) {
            abort(403, 'Only treasurer or admin can approve expenses.');
        }

        $expense->approve(Auth::user());

        return back()->with('status', 'Expense approved successfully.');
    }

    public function reject(Request $request, Group $group, GroupExpense $expense)
    {
        if (!$group->hasRole(Auth::user(), 'treasurer') && !$group->hasRole(Auth::user(), 'admin')) {
            abort(403, 'Only treasurer or admin can reject expenses.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $expense->reject(Auth::user(), $validated['rejection_reason']);

        return back()->with('status', 'Expense rejected.');
    }

    public function destroy(Group $group, GroupExpense $expense)
    {
        if (!$group->hasRole(Auth::user(), 'admin') && !Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($expense->status === 'approved' || $expense->status === 'paid') {
            return back()->withErrors(['error' => 'Cannot delete approved or paid expenses']);
        }

        $expense->delete();

        return back()->with('status', 'Expense deleted successfully.');
    }
}

