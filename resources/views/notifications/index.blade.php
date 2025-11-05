@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Notifications</h1>
    <form method="POST" action="{{ route('notifications.mark_all_read') }}">
      @csrf
      <button class="btn btn-sm btn-outline-primary">Mark All Read</button>
    </form>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="card mb-3">
    <div class="card-body">
      <div class="row g-2">
        <div class="col-md-6">
          <label class="form-label small">Filter</label>
          <div class="btn-group w-100" role="group">
            <a href="{{ route('notifications.index', ['type' => $type]) }}" class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
            <a href="{{ route('notifications.index', ['filter' => 'unread', 'type' => $type]) }}" class="btn btn-sm {{ $filter === 'unread' ? 'btn-primary' : 'btn-outline-secondary' }}">Unread</a>
            <a href="{{ route('notifications.index', ['filter' => 'read', 'type' => $type]) }}" class="btn btn-sm {{ $filter === 'read' ? 'btn-primary' : 'btn-outline-secondary' }}">Read</a>
            <a href="{{ route('notifications.index', ['filter' => 'archived', 'type' => $type]) }}" class="btn btn-sm {{ $filter === 'archived' ? 'btn-primary' : 'btn-outline-secondary' }}">Archived</a>
          </div>
        </div>
        @if($types->count() > 0)
        <div class="col-md-6">
          <label class="form-label small">Type</label>
          <select class="form-select form-select-sm" onchange="window.location.href='{{ route('notifications.index', ['filter' => $filter]) }}&type='+this.value">
            <option value="">All Types</option>
            @foreach($types as $t)
            <option value="{{ $t }}" {{ $type === $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
          </select>
        </div>
        @endif
      </div>
    </div>
  </div>

  <form method="POST" action="{{ route('notifications.bulk') }}" id="bulk-form">
    @csrf
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex gap-2 align-items-center">
          <input type="checkbox" id="select-all" onchange="document.querySelectorAll('.notification-checkbox').forEach(c => c.checked = this.checked)">
          <select name="action" class="form-select form-select-sm" style="width: auto;">
            <option value="mark_read">Mark Read</option>
            <option value="mark_unread">Mark Unread</option>
            <option value="archive">Archive</option>
            @if($filter === 'archived')
            <option value="unarchive">Unarchive</option>
            <option value="delete">Delete Permanently</option>
            @endif
          </select>
          <button type="submit" class="btn btn-sm btn-primary">Apply</button>
        </div>
      </div>
      <div class="card-body">
        @forelse($notifications as $n)
        <div class="d-flex align-items-start gap-3 py-2 border-bottom">
          <input type="checkbox" name="ids[]" value="{{ $n->id }}" class="notification-checkbox mt-2">
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="fw-semibold">{{ data_get($n->data, 'title') ?? class_basename($n->type) }}</div>
                @if(data_get($n->data, 'message'))
                <div class="text-muted small">{{ data_get($n->data, 'message') }}</div>
                @endif
                <div class="text-muted small mt-1">{{ $n->created_at->diffForHumans() }}</div>
              </div>
              <div>
                @if(!$n->read_at && $filter !== 'archived')
                <span class="badge bg-primary">New</span>
                @endif
                @if($filter === 'archived')
                <span class="badge bg-secondary">Archived</span>
                @endif
              </div>
            </div>
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-4">No notifications found.</div>
        @endforelse
      </div>
    </div>
  </form>

  <div class="mt-3">{{ $notifications->withQueryString()->links() }}</div>
</div>
@endsection
