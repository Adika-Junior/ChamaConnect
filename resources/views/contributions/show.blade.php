@extends('layouts.app')

@section('content')
<div class="container" data-contribution-id="{{ $contribution->id }}">
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ $contribution->title }}</h1>
        <div>
            <a href="{{ route('contributions.report', $contribution) }}" class="btn btn-outline-primary">Report</a>
            <a href="{{ route('contributions.edit', $contribution) }}" class="btn btn-secondary">Edit</a>
            <a href="{{ route('contributions.index') }}" class="btn btn-link">Back</a>
        </div>
    </div>

    <div class="mb-3">
        <strong>Status:</strong> {{ ucfirst(str_replace('_',' ', $contribution->status)) }}
    </div>

    @if($contribution->meeting)
    <div class="card mt-4">
        <div class="card-header">Meeting Recording</div>
        <div class="card-body">
            <form method="POST" action="{{ route('meetings.recordings.store', $contribution->meeting) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="contribution_id" value="{{ $contribution->id }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Upload audio/video file</label>
                        <input type="file" name="recording" class="form-control" accept="audio/*,video/*" required>
                        @error('recording')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Duration (seconds)</label>
                        <input type="number" name="duration_seconds" class="form-control" min="0">
                        @error('duration_seconds')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" type="submit">Upload</button>
                    </div>
                </div>
            </form>

            @php($recordings = \App\Models\MeetingRecording::where('meeting_id', $contribution->meeting_id)->orderByDesc('created_at')->get())
            @if($recordings->count())
                <ul class="list-group mt-3">
                    @foreach($recordings as $r)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $r->file_name }} ({{ $r->duration_seconds ? $r->duration_seconds.'s' : 'duration n/a' }})</span>
                            <a href="{{ Storage::disk('public')->url($r->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    @endif

    @if($contribution->meeting)
    <div class="card mt-4">
        <div class="card-header">Meeting Transcript</div>
        <div class="card-body">
            <form method="POST" action="{{ route('meetings.transcripts.store', $contribution->meeting) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="contribution_id" value="{{ $contribution->id }}">
                <div class="mb-3">
                    <label class="form-label">Transcript Text (optional)</label>
                    <textarea class="form-control" name="content" rows="4"></textarea>
                </div>
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Upload transcript file (txt, pdf, docx)</label>
                        <input type="file" class="form-control" name="file" accept=".txt,.pdf,.doc,.docx">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" type="submit">Save Transcript</button>
                    </div>
                </div>
            </form>

            @php($transcripts = \App\Models\MeetingTranscript::where('meeting_id', $contribution->meeting_id)->orderByDesc('created_at')->get())
            @if($transcripts->count())
                <ul class="list-group mt-3">
                    @foreach($transcripts as $t)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small text-muted">{{ $t->created_at->format('Y-m-d H:i') }}</div>
                                    @if($t->content)
                                        <div class="mt-2">{!! nl2br(e(Str::limit($t->content, 300))) !!}</div>
                                    @endif
                                </div>
                                @if($t->file_path)
                                    <a href="{{ Storage::disk('public')->url($t->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">View File</a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    @endif
    <div class="mb-3">
        <strong>Category:</strong> {{ $contribution->category }}
    </div>
    <div class="mb-3">
        <strong>Target:</strong> {{ number_format($contribution->target_amount, 2) }}
    </div>
    <div class="mb-3">
        <strong>Collected:</strong> <span data-collected-amount>{{ number_format($contribution->collected_amount, 2) }}</span> ({{ $contribution->progressPercentage() }}%)
    </div>
    <div class="mb-3">
        <strong>Deadline:</strong> {{ optional($contribution->deadline)->format('Y-m-d H:i') }}
    </div>
    <div class="mb-3">
        <strong>Organizer:</strong> {{ optional($contribution->organizer)->name }}
    </div>
    <div class="mb-3">
        <strong>Meeting:</strong> {{ optional($contribution->meeting)->title ?? '—' }}
    </div>
    <div class="mb-3">
        <strong>Description:</strong>
        <div class="border p-3 bg-light">{!! nl2br(e($contribution->description)) !!}</div>
    </div>

    @can('contribute', $contribution)
    <div class="card mb-4">
        <div class="card-header">Record a Contribution</div>
        <div class="card-body">
            <form method="POST" action="{{ route('contributions.payments.store', $contribution) }}">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                        @error('amount')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Method</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="mpesa">M-Pesa</option>
                            <option value="bank">Bank</option>
                            <option value="cash">Cash</option>
                            <option value="other">Other</option>
                        </select>
                        @error('payment_method')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Reference (optional)</label>
                        <input type="text" name="reference" class="form-control" placeholder="Txn ID, Cheque No., etc">
                        @error('reference')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Add</button>
                    </div>
                </div>
            </form>

            <hr>
            
            <!-- M-Pesa Payment -->
            <h6 class="mb-2">Pay via M-Pesa</h6>
            <form method="POST" action="{{ route('contributions.mpesa.initiate', $contribution) }}" class="row g-3 align-items-end mb-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">M-Pesa Phone</label>
                    <input type="text" name="phone" class="form-control" placeholder="2547XXXXXXXX" value="{{ auth()->user()->phone }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Amount</label>
                    <input type="number" step="0.01" name="amount" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-success w-100" type="submit">💳 Pay Now (M-Pesa)</button>
                </div>
            </form>

            <hr>

            <!-- Make a Pledge -->
            <h6 class="mb-2">Or Make a Pledge (Pay Later)</h6>
            <form method="POST" action="{{ route('pledges.store', $contribution) }}" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">Pledge Amount</label>
                    <input type="number" step="0.01" name="amount" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Notes (optional)</label>
                    <input type="text" name="notes" class="form-control" placeholder="Payment plan, installment info, etc.">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-warning w-100" type="submit">📝 Make Pledge</button>
                </div>
            </form>
            <small class="text-muted d-block mt-2">
                <i class="fas fa-info-circle"></i> A pledge is a commitment to pay later. You can fulfill it anytime before the due date.
            </small>
        </div>
    </div>
    @endcan

    <!-- Pledges Section -->
    @if($contribution->pledges->count() > 0)
    <div class="card mb-4">
        <div class="card-header">Active Pledges</div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Contributor</th>
                        <th>Amount</th>
                        <th>Pledged</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contribution->pledges->where('status', '!=', 'cancelled')->sortBy('due_date') as $pledge)
                        <tr class="{{ $pledge->isOverdue() ? 'table-danger' : '' }}">
                            <td>{{ $pledge->user->name }}</td>
                            <td>KSh {{ number_format($pledge->amount, 2) }}</td>
                            <td>{{ $pledge->pledged_at->format('M d, Y') }}</td>
                            <td>
                                {{ $pledge->due_date->format('M d, Y') }}
                                @if($pledge->isOverdue())
                                    <span class="badge bg-danger">Overdue</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $pledge->status === 'fulfilled' ? 'success' : ($pledge->status === 'overdue' ? 'danger' : 'warning') }}">
                                    {{ ucfirst(str_replace('_', ' ', $pledge->status)) }}
                                </span>
                            </td>
                            <td>
                                @if($pledge->user_id === auth()->id() && $pledge->status !== 'fulfilled')
                                    <a href="{{ route('pledges.fulfill', $pledge) }}" class="btn btn-sm btn-success">Fulfill Now</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">Contribution History</div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>Contributor</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @php($payments = \App\Models\ContributionPayment::where('contribution_id', $contribution->id)->with('user')->orderByDesc('paid_at')->get())
                    @forelse($payments as $p)
                        <tr>
                            <td>{{ optional($p->paid_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ optional($p->user)->name }}</td>
                            <td>{{ number_format($p->amount, 2) }}</td>
                            <td>{{ strtoupper($p->payment_method) }}</td>
                            <td>{{ $p->reference }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">No contributions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Participants</span>
            @can('update', $contribution)
                @php
                    $meetingParticipants = $contribution->meeting ? $contribution->meeting->participants : collect();
                    $existingIds = $contribution->participants->pluck('id')->toArray();
                    $availableParticipants = $meetingParticipants->filter(fn($u) => !in_array($u->id, $existingIds));
                @endphp
                @if($availableParticipants->isNotEmpty())
                <form class="d-flex align-items-end gap-2" method="POST" action="{{ route('contributions.participants.add', $contribution) }}">
                    @csrf
                    <div>
                        <label class="form-label">Add participant</label>
                        <select name="user_id" class="form-select">
                            @foreach($availableParticipants as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit">Add</button>
                </form>
                @elseif($contribution->meeting)
                    <span class="text-muted small">All meeting participants are already added.</span>
                @else
                    <span class="text-muted small">No meeting linked to this contribution.</span>
                @endif
            @endcan
        </div>
        <div class="card-body">
            @if($contribution->participants->isEmpty())
                <div class="text-muted">No participants yet.</div>
            @else
                <ul class="list-group">
                    @foreach($contribution->participants as $u)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $u->name }}</span>
                            @can('update', $contribution)
                            <form method="POST" action="{{ route('contributions.participants.remove', [$contribution, $u]) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Remove</button>
                            </form>
                            @endcan
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection


