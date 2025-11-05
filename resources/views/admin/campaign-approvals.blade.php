@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Campaign Approvals</h1>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
  @endif

  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>Title</th>
          <th>Organizer</th>
          <th>Goal</th>
          <th>Created</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
      @forelse($campaigns as $c)
        <tr>
          <td><a href="{{ route('campaigns.show', $c) }}" target="_blank">{{ $c->title }}</a></td>
          <td>{{ $c->organizer?->name ?? '—' }}</td>
          <td>KSh {{ number_format($c->goal_amount, 0) }}</td>
          <td>{{ $c->created_at->diffForHumans() }}</td>
          <td class="text-end d-flex gap-2 justify-content-end">
            <form method="POST" action="{{ route('admin.campaigns.approve', $c) }}">
              @csrf
              <button type="submit" class="btn btn-sm btn-success">Approve</button>
            </form>
            <form method="POST" action="{{ route('admin.campaigns.reject', $c) }}" class="d-flex gap-2">
              @csrf
              <input type="text" name="rejection_reason" class="form-control form-control-sm" placeholder="Reason" required>
              <button type="submit" class="btn btn-sm btn-outline-danger">Reject</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="5" class="text-center text-muted py-4">No pending campaigns.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">{{ $campaigns->links() }}</div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-3">Campaign Approvals</h1>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Campaign</th>
                        <th>Organizer</th>
                        <th>Goal</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $campaign)
                        <tr>
                            <td>
                                <strong>{{ $campaign->title }}</strong>
                                <div class="small text-muted">{{ Str::limit($campaign->description, 50) }}</div>
                            </td>
                            <td>{{ $campaign->organizer->name }}</td>
                            <td>KSh {{ number_format($campaign->goal_amount, 2) }}</td>
                            <td>{{ $campaign->created_at->diffForHumans() }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.campaigns.approve', $campaign) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $campaign->id }}">Reject</button>
                            </td>
                        </tr>
                        
                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal{{ $campaign->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.campaigns.reject', $campaign) }}">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reject Campaign</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="rejection_reason{{ $campaign->id }}" class="form-label">Reason for Rejection</label>
                                                <textarea name="rejection_reason" id="rejection_reason{{ $campaign->id }}" class="form-control" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Reject Campaign</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No pending campaigns.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

