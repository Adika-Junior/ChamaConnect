@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ $group->name }}</h1>
        <div class="d-flex gap-2">
            @can('update', $group)
                <a href="{{ route('groups.edit', $group) }}" class="btn btn-secondary">Edit</a>
            @endcan
            @can('view', $group)
                <a href="{{ route('groups.report', $group) }}" class="btn btn-info">Report</a>
            @endcan
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-md-8">
            @if($canManage && $group->wasRecentlyApproved)
            <div class="alert alert-info">
                <strong>Onboarding Checklist</strong>
                <ul class="mt-2 mb-0">
                    <li>Create your first contribution rule and payment schedule.</li>
                    <li>Add at least 5 members and assign roles (treasurer/secretary).</li>
                    <li>Set up M‑Pesa collection till or bank details.</li>
                    <li>Schedule your kickoff meeting in the Meetings calendar.</li>
                </ul>
            </div>
            @endif
            @if($isMember)
            <div class="card mb-3">
                <div class="card-body">
                    <a href="{{ route('contributions.create', ['group_id' => $group->id]) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Contribution for this Group
                    </a>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header">Financial Summary</div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="h4 text-success">KSh {{ number_format($group->total_contributions, 2) }}</div>
                            <small class="text-muted">Total Contributions</small>
                        </div>
                        <div class="col-4">
                            <div class="h4 text-danger">KSh {{ number_format($group->total_expenses, 2) }}</div>
                            <small class="text-muted">Total Expenses</small>
                        </div>
                        <div class="col-4">
                            <div class="h4 text-primary">KSh {{ number_format($group->balance, 2) }}</div>
                            <small class="text-muted">Balance</small>
                        </div>
                    </div>
                </div>
            </div>

            @if($canManage)
            <div class="card mt-3">
                <div class="card-header">Recent Expenses</div>
                <div class="card-body">
                    @if($group->expenses->count() > 0)
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group->expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->title }}</td>
                                        <td>KSh {{ number_format($expense->amount, 2) }}</td>
                                        <td><span class="badge bg-{{ $expense->status === 'approved' ? 'success' : ($expense->status === 'pending' ? 'warning' : 'secondary') }}">{{ ucfirst($expense->status) }}</span></td>
                                        <td>
                                            @if($expense->status === 'pending')
                                                @can('update', $group)
                                                    <form method="POST" action="{{ route('group-expenses.approve', [$group, $expense]) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                                    </form>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $expense->id }}">Reject</button>
                                                @endcan
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No expenses yet.</p>
                    @endif

                    <!-- Reject Expense Modals -->
                    @foreach($group->expenses as $expense)
                        @if($expense->status === 'pending')
                            <div class="modal fade" id="rejectModal{{ $expense->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('group-expenses.reject', [$group, $expense]) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Reject Expense</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Expense: <strong>{{ $expense->title }}</strong> - KSh {{ number_format($expense->amount, 2) }}</p>
                                                <div class="mb-3">
                                                    <label for="rejection_reason{{ $expense->id }}" class="form-label">Reason for Rejection</label>
                                                    <textarea name="rejection_reason" id="rejection_reason{{ $expense->id }}" class="form-control" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Reject Expense</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if($isMember)
                        <form method="POST" action="{{ route('group-expenses.store', $group) }}" class="mt-3">
                            @csrf
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" name="title" class="form-control form-control-sm" placeholder="Expense title" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" step="0.01" name="amount" class="form-control form-control-sm" placeholder="Amount" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="category" class="form-control form-control-sm" placeholder="Category">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-sm btn-primary w-100">Request</button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
            @endif

            <!-- Group Contributions -->
            @if($group->contributions->count() > 0)
            <div class="card mt-3">
                <div class="card-header">Group Contributions</div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($group->contributions->take(5) as $contribution)
                            <a href="{{ route('contributions.show', $contribution) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $contribution->title }}</h6>
                                        <p class="mb-1 small text-muted">{{ $contribution->category }}</p>
                                        <small class="text-muted">Progress: {{ number_format($contribution->progressPercentage(), 1) }}%</small>
                                    </div>
                                    <span class="badge bg-{{ $contribution->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($contribution->status) }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    @if($group->contributions->count() > 5)
                        <div class="mt-3 text-center">
                            <a href="{{ route('contributions.index', ['group_id' => $group->id]) }}" class="btn btn-sm btn-outline-primary">
                                View All Contributions ({{ $group->contributions->count() }})
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Pending Applications (for admins) -->
            @if($canManage && $group->applications()->where('status', 'pending')->count() > 0)
            <div class="card mt-3">
                <div class="card-header bg-warning text-dark">Pending Applications ({{ $group->applications()->where('status', 'pending')->count() }})</div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($group->applications()->where('status', 'pending')->with('user')->get() as $application)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $application->user->name }}</h6>
                                        <p class="mb-1 small">{{ $application->user->email }}</p>
                                        <p class="mb-0 small text-muted">{{ Str::limit($application->reason, 100) }}</p>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <form method="POST" action="{{ route('groups.applications.approve', [$group, $application]) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success">Approve</button>
                                        </form>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectApplicationModal{{ $application->id }}">
                                            Reject
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Reject Modal -->
                            <div class="modal fade" id="rejectApplicationModal{{ $application->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('groups.applications.reject', [$group, $application]) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Reject Application</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Rejecting application from: <strong>{{ $application->user->name }}</strong></p>
                                                <div class="mb-3">
                                                    <label class="form-label">Reason (optional)</label>
                                                    <textarea name="rejection_reason" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Reject Application</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Members ({{ $group->members->count() }})</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($group->members as $member)
                            <li class="list-group-item d-flex justify-content-between">
                                <div>
                                    {{ $member->name }}
                                    <span class="badge bg-secondary ms-2">{{ $member->pivot->role }}</span>
                                </div>
                                @if($canManage && $member->id !== auth()->id())
                                    <form method="POST" action="{{ route('groups.members.destroy', [$group, $member]) }}" class="d-inline" onsubmit="return confirm('Remove member?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                    </form>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    @if($canManage)
                        <form method="POST" action="{{ route('groups.members.store', $group) }}" class="mt-3">
                            @csrf
                            <div class="input-group input-group-sm">
                                <select name="user_id" class="form-control" required>
                                    <option value="">Add member...</option>
                                    @foreach(\App\Models\User::whereNotIn('id', $group->members->pluck('id'))->where('status', 'active')->get() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <select name="role" class="form-control" required>
                                    <option value="member">Member</option>
                                    <option value="treasurer">Treasurer</option>
                                    <option value="secretary">Secretary</option>
                                    <option value="admin">Admin</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Add</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

