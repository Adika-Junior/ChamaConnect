@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-primary to-success rounded-2xl shadow-xl p-6 mb-4 text-white">
        <h1 class="h3 mb-2">Welcome back, {{ auth()->user()->name }}! 👋</h1>
        <p class="mb-0">Here's what's happening with your tasks, meetings, and groups.</p>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Pending Tasks</p>
                            <h3 class="mb-0">{{ $pendingTasks ?? 0 }}</h3>
                        </div>
                        <div class="text-primary" style="font-size: 2rem;">📋</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Upcoming Meetings</p>
                            <h3 class="mb-0">{{ $upcomingMeetings->count() ?? 0 }}</h3>
                        </div>
                        <div class="text-success" style="font-size: 2rem;">📅</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">My Groups</p>
                            <h3 class="mb-0">{{ $groupSummaries->count() ?? 0 }}</h3>
                        </div>
                        <div class="text-info" style="font-size: 2rem;">👥</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Active Campaigns</p>
                            <h3 class="mb-0">{{ $activeCampaigns->count() ?? 0 }}</h3>
                        </div>
                        <div class="text-warning" style="font-size: 2rem;">🎯</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Upcoming Meetings Calendar Widget -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>📅 Upcoming Meetings</span>
                    <a href="{{ route('meetings.index') }}" class="text-white small">View All</a>
                </div>
                <div class="card-body">
                    @if($upcomingMeetings && $upcomingMeetings->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($upcomingMeetings as $meeting)
                                <a href="{{ route('meetings.show', $meeting) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $meeting->title }}</h6>
                                            <p class="mb-1 text-muted small">{{ $meeting->scheduled_at->format('l, M d, Y \a\t H:i') }}</p>
                                            <span class="badge bg-info">{{ ucfirst($meeting->type) }}</span>
                                        </div>
                                        <small class="text-muted">{{ $meeting->scheduled_at->diffForHumans() }}</small>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No upcoming meetings in the next 7 days.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pending Tasks Summary -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <span>📋 Pending Tasks</span>
                    <a href="{{ route('tasks.index') }}" class="text-dark small">View All</a>
                </div>
                <div class="card-body">
                    @php
                        $recentPendingTasks = \App\Models\Task::where('assigned_to', auth()->id())
                            ->whereIn('status', ['pending', 'in_progress'])
                            ->orderBy('due_date')
                            ->limit(5)
                            ->get();
                    @endphp
                    @if($recentPendingTasks->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentPendingTasks as $task)
                                <a href="{{ route('tasks.show', $task) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $task->title }}</h6>
                                            <span class="badge bg-{{ $task->status === 'in_progress' ? 'primary' : 'secondary' }}">
                                                {{ ucfirst($task->status) }}
                                            </span>
                                            @if($task->due_date)
                                                <small class="d-block text-muted mt-1">Due: {{ $task->due_date->format('M d, Y') }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No pending tasks. Great job! 🎉</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Activities Feed -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <span>🔄 Recent Activities</span>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @if($recentActivities && $recentActivities->count() > 0)
                        <div class="timeline">
                            @foreach($recentActivities as $activity)
                                <div class="timeline-item mb-3 pb-3 border-bottom">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            @if($activity['type'] === 'task')
                                                <span class="badge bg-primary">📋</span>
                                            @elseif($activity['type'] === 'contribution')
                                                <span class="badge bg-success">💰</span>
                                            @elseif($activity['type'] === 'meeting')
                                                <span class="badge bg-info">📅</span>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="{{ $activity['url'] }}" class="text-decoration-none">
                                                    {{ $activity['title'] }}
                                                </a>
                                            </h6>
                                            <p class="mb-0 text-muted small">{{ $activity['description'] }}</p>
                                            <small class="text-muted">{{ $activity['time']->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No recent activities.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Group Financial Summaries -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <span>💰 Group Financial Summaries</span>
                    <a href="{{ route('groups.index') }}" class="text-white small">View All</a>
                </div>
                <div class="card-body">
                    @if($groupSummaries && $groupSummaries->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($groupSummaries as $summary)
                                <a href="{{ route('groups.show', $summary['id']) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $summary['name'] }}</h6>
                                            <p class="mb-0 small">
                                                <span class="text-success">Balance: KSh {{ number_format($summary['balance'], 2) }}</span>
                                                <br>
                                                <span class="text-muted">Your Contribution: KSh {{ number_format($summary['total_contributed'], 2) }}</span>
                                            </p>
                                        </div>
                                        <span class="badge bg-secondary">{{ $summary['member_count'] }} members</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">You're not a member of any groups yet. <a href="{{ route('groups.discover') }}">Discover SACCOs</a></p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Campaign Progress Cards -->
        @if($activeCampaigns && $activeCampaigns->count() > 0)
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <span>🎯 Campaign Progress</span>
                    <a href="{{ route('campaigns.index') }}" class="text-dark small">View All</a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($activeCampaigns as $campaign)
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <a href="{{ route('campaigns.show', $campaign['id']) }}" class="text-decoration-none">
                                                {{ $campaign['title'] }}
                                            </a>
                                        </h6>
                                        <div class="progress mb-2" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ min($campaign['progress'], 100) }}%">
                                                {{ number_format($campaign['progress'], 1) }}%
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between small text-muted">
                                            <span>KSh {{ number_format($campaign['current_amount'], 2) }}</span>
                                            <span>KSh {{ number_format($campaign['goal_amount'], 2) }}</span>
                                        </div>
                                        <p class="mb-0 mt-2 small">
                                            <strong>Remaining:</strong> KSh {{ number_format($campaign['remaining'], 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

