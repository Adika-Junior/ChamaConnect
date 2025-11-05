@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Donor Wall - {{ $campaign->title }}</h1>
    <div class="d-flex gap-2 align-items-center">
      @if($pending > 0)
      <span class="badge bg-warning">{{ $pending }} pending</span>
      @endif
      <div class="btn-group">
        <a href="{{ route('admin.donor_wall.index', ['campaign' => $campaign, 'filter' => 'approved']) }}" class="btn btn-sm {{ $filter === 'approved' ? 'btn-primary' : 'btn-outline-secondary' }}">Approved</a>
        <a href="{{ route('admin.donor_wall.index', ['campaign' => $campaign, 'filter' => 'pending']) }}" class="btn btn-sm {{ $filter === 'pending' ? 'btn-primary' : 'btn-outline-secondary' }}">Pending</a>
        <a href="{{ route('admin.donor_wall.index', ['campaign' => $campaign, 'filter' => 'all']) }}" class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
      </div>
    </div>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="row g-3">
    @foreach($donations as $donation)
    <div class="col-md-4">
      <div class="card">
        <div class="card-body text-center">
          @if($donation->avatar_url)
          <img src="{{ $donation->avatar_url }}" alt="{{ $donation->donor_name }}" class="rounded-circle mb-2" style="width: 60px; height: 60px; object-fit: cover;">
          @else
          <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px; font-size: 24px;">
            {{ strtoupper(substr($donation->donor_name ?? 'A', 0, 1)) }}
          </div>
          @endif
          <div class="fw-semibold">{{ $donation->donor_name }}</div>
          <div class="text-primary fw-bold">KES {{ number_format($donation->amount ?? ($donation->amount_cents / 100), 2) }}</div>
          @if($donation->message)
          <div class="text-muted small mt-2">{{ Str::limit($donation->message, 100) }}</div>
          @endif
          <div class="mt-2 d-flex gap-1 justify-content-center">
            @if($donation->moderation_status === 'pending')
            <form method="POST" action="{{ route('admin.donor_wall.moderate', $donation) }}" class="d-inline">
              @csrf
              <input type="hidden" name="action" value="approve">
              <button class="btn btn-sm btn-success">Approve</button>
            </form>
            <form method="POST" action="{{ route('admin.donor_wall.moderate', $donation) }}" class="d-inline">
              @csrf
              <input type="hidden" name="action" value="reject">
              <button class="btn btn-sm btn-danger">Reject</button>
            </form>
            @else
            <form method="POST" action="{{ route('admin.donor_wall.moderate', $donation) }}" class="d-inline">
              @csrf
              <input type="hidden" name="action" value="remove">
              <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove from wall?')">Remove</button>
            </form>
            @endif
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  <div class="mt-3">{{ $donations->links() }}</div>
</div>
@endsection

