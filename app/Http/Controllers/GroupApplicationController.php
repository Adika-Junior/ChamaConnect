<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Show all public SACCOs and groups
        $publicGroups = Group::where('is_public', true)
            ->where('type', 'sacco')
            ->with(['creator', 'members'])
            ->paginate(12);

        $userApplications = GroupApplication::where('user_id', Auth::id())
            ->with('group')
            ->get();

        return view('groups.discover', compact('publicGroups', 'userApplications'));
    }

    public function create(Group $group)
    {
        if (!$group->is_public || !$group->accepting_applications) {
            abort(404, 'This group is not accepting applications.');
        }

        // Check if already applied
        $existingApplication = GroupApplication::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingApplication) {
            return redirect()->route('groups.show', $group)
                ->with('info', 'You have already applied to this group.');
        }

        // Check if already a member
        if ($group->isMember(Auth::user())) {
            return redirect()->route('groups.show', $group)
                ->with('info', 'You are already a member of this group.');
        }

        return view('groups.apply', compact('group'));
    }

    public function store(Request $request, Group $group)
    {
        if (!$group->is_public || !$group->accepting_applications) {
            abort(404, 'This group is not accepting applications.');
        }

        // Validate application based on group requirements
        $validated = $request->validate([
            'id_number' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'occupation' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'reason' => 'required|string|min:20|max:1000',
            'terms_accepted' => 'required|accepted',
        ]);

        // Create application data JSON
        $applicationData = [
            'id_number' => $validated['id_number'],
            'phone' => $validated['phone'],
            'occupation' => $validated['occupation'],
            'address' => $validated['address'] ?? null,
        ];

        $application = GroupApplication::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
            'application_data' => $applicationData,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        // Notify group admins
        // TODO: Send notification to group admins

        return redirect()->route('groups.show', $group)
            ->with('status', 'Application submitted successfully! The group administrators will review your application.');
    }

    public function approve(Request $request, Group $group, GroupApplication $application)
    {
        $this->authorize('update', $group);

        $application->approve(Auth::user());

        return back()->with('status', 'Application approved. User has been added to the group.');
    }

    public function reject(Request $request, Group $group, GroupApplication $application)
    {
        $this->authorize('update', $group);

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $application->reject(Auth::user(), $validated['rejection_reason'] ?? null);

        return back()->with('status', 'Application rejected.');
    }
}

