<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Meeting;
use App\Models\Group;
use App\Models\Campaign;
use App\Models\Contribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        // Task Statistics
        $pendingTasks = Task::where('assigned_to', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();
        
        $totalTasks = Task::where('assigned_to', $user->id)->count();
        
        // Upcoming Meetings (next 7 days)
        $upcomingMeetings = Meeting::whereHas('participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orWhere('organizer_id', $user->id)
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>=', now())
            ->where('scheduled_at', '<=', now()->addDays(7))
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();
        
        // Recent Activities
        $recentActivities = collect();
        
        // Recent tasks
        $recentTasks = Task::where('assigned_to', $user->id)
            ->orWhere('created_by', $user->id)
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($task) => [
                'type' => 'task',
                'title' => "Task: {$task->title}",
                'description' => $task->status,
                'time' => $task->updated_at,
                'url' => route('tasks.show', $task),
            ]);
        
        $recentActivities = $recentActivities->merge($recentTasks);
        
        // Recent contributions
        $recentContributions = Contribution::whereHas('participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orWhere('organizer_id', $user->id)
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($contribution) => [
                'type' => 'contribution',
                'title' => "Contribution: {$contribution->title}",
                'description' => number_format($contribution->progressPercentage(), 1) . '% complete',
                'time' => $contribution->updated_at,
                'url' => route('contributions.show', $contribution),
            ]);
        
        $recentActivities = $recentActivities->merge($recentContributions);
        
        // Recent meetings
        $recentMeetings = Meeting::whereHas('participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orWhere('organizer_id', $user->id)
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($meeting) => [
                'type' => 'meeting',
                'title' => "Meeting: {$meeting->title}",
                'description' => $meeting->scheduled_at?->format('M d, Y H:i'),
                'time' => $meeting->updated_at,
                'url' => route('meetings.show', $meeting),
            ]);
        
        $recentActivities = $recentActivities->merge($recentMeetings)
            ->sortByDesc('time')
            ->take(10);
        
        // Group Financial Summaries
        $userGroups = $user->groups()->withPivot('total_contributed')->get();
        $groupSummaries = $userGroups->map(function($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
                'balance' => $group->balance,
                'total_contributed' => $group->pivot->total_contributed ?? 0,
                'member_count' => $group->members()->count(),
            ];
        });
        
        // Campaign Progress
        $activeCampaigns = Campaign::where('organizer_id', $user->id)
            ->where('status', 'active')
            ->get()
            ->map(function($campaign) {
                return [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'progress' => $campaign->progress,
                    'current_amount' => $campaign->current_amount,
                    'goal_amount' => $campaign->goal_amount,
                    'remaining' => $campaign->remaining,
                ];
            });
        
        // Unread messages count (if chat exists)
        $unreadMessages = 0; // TODO: Implement chat unread count
        
        return view('dashboard', compact(
            'pendingTasks',
            'totalTasks',
            'upcomingMeetings',
            'recentActivities',
            'groupSummaries',
            'activeCampaigns',
            'unreadMessages'
        ));
    }
}

