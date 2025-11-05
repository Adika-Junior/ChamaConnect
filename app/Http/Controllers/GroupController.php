<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $groups = Group::with(['creator', 'treasurer', 'secretary', 'members'])
            ->withCount('members')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:sacco,committee,project,other',
            'description' => 'nullable|string',
            'treasurer_id' => 'nullable|exists:users,id',
            'secretary_id' => 'nullable|exists:users,id',
            'is_public' => 'boolean',
            'accepting_applications' => 'boolean',
            'application_requirements' => 'nullable|string',
            'registration_number' => 'nullable|string|max:100',
            'registered_at' => 'nullable|date',
            'by_laws' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'min_members' => 'nullable|integer|min:1',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_public'] = $request->has('is_public');
        $validated['accepting_applications'] = $request->has('accepting_applications');
        $validated['current_members'] = 1; // Creator is first member

        $group = Group::create($validated);

        // Add creator as admin member
        $group->members()->attach(Auth::id(), [
            'role' => 'admin',
            'total_contributed' => 0,
            'joined_at' => now(),
        ]);

        return redirect()->route('groups.show', $group)
            ->with('status', 'Group created successfully.');
    }

    public function show(Group $group)
    {
        $group->load(['members', 'expenses' => function($query) {
            $query->latest()->limit(10);
        }, 'contributions' => function($query) {
            $query->latest()->limit(10);
        }, 'treasurer', 'secretary', 'creator']);

        $isMember = $group->isMember(Auth::user());
        $canManage = $isMember && ($group->hasRole(Auth::user(), 'admin') || $group->hasRole(Auth::user(), 'treasurer'));

        return view('groups.show', compact('group', 'isMember', 'canManage'));
    }

    public function edit(Group $group)
    {
        if (!$group->hasRole(Auth::user(), 'admin') && !Auth::user()->isAdmin()) {
            abort(403);
        }
        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        if (!$group->hasRole(Auth::user(), 'admin') && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:sacco,committee,project,other',
            'description' => 'nullable|string',
            'treasurer_id' => 'nullable|exists:users,id',
            'secretary_id' => 'nullable|exists:users,id',
            'member_quota' => Auth::user()->isAdmin() ? 'nullable|integer|min:1' : 'prohibited',
        ]);

        $group->update($validated);

        return redirect()->route('groups.show', $group)
            ->with('status', 'Group updated successfully.');
    }

    public function destroy(Group $group)
    {
        if (!$group->hasRole(Auth::user(), 'admin') && !Auth::user()->isAdmin()) {
            abort(403);
        }
        $group->delete();
        return redirect()->route('groups.index')
            ->with('status', 'Group deleted successfully.');
    }

    public function addMember(Request $request, Group $group)
    {
        if (!$group->hasRole(Auth::user(), 'admin') && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|in:member,treasurer,secretary,admin',
        ]);

        if ($group->isMember(User::find($validated['user_id']))) {
            return back()->withErrors(['user_id' => 'User is already a member']);
        }

        // Enforce member quota unless system admin
        $memberCount = $group->members()->count();
        if (!$request->user()->isAdmin() && $group->member_quota && $memberCount >= $group->member_quota) {
            return back()->withErrors(['user_id' => 'Member quota reached for this group. Contact system admin to increase limit.']);
        }

        $group->members()->attach($validated['user_id'], [
            'role' => $validated['role'],
            'total_contributed' => 0,
            'joined_at' => now(),
        ]);

        // Update treasurer/secretary if role matches
        if ($validated['role'] === 'treasurer') {
            $group->update(['treasurer_id' => $validated['user_id']]);
        } elseif ($validated['role'] === 'secretary') {
            $group->update(['secretary_id' => $validated['user_id']]);
        }

        return back()->with('status', 'Member added successfully.');
    }

    public function removeMember(Group $group, User $user)
    {
        if (!$group->hasRole(Auth::user(), 'admin') && !Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($group->treasurer_id === $user->id) {
            $group->update(['treasurer_id' => null]);
        }
        if ($group->secretary_id === $user->id) {
            $group->update(['secretary_id' => null]);
        }

        $group->members()->detach($user->id);

        return back()->with('status', 'Member removed successfully.');
    }

    public function report(Group $group)
    {
        if (!$group->isMember(Auth::user()) && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $group->load(['members', 'expenses', 'contributions.payments']);

        $memberContributions = $group->members()
            ->withPivot('total_contributed')
            ->orderByDesc('group_members.total_contributed')
            ->get();

        $expensesByCategory = $group->expenses()
            ->select('category', DB::raw('SUM(amount) as total'))
            ->where('status', 'approved')
            ->groupBy('category')
            ->get();

        return view('groups.report', compact('group', 'memberContributions', 'expensesByCategory'));
    }
}

