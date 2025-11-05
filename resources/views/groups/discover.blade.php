@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Discover Public SACCOs</h1>
    <a href="{{ url('/') }}" class="btn btn-outline-secondary">Home</a>
  </div>

  <div class="row g-3">
    @foreach($publicGroups as $group)
      <div class="col-md-6 col-lg-4">
        <div class="card h-100">
          <div class="card-body d-flex flex-column">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h5 class="card-title mb-0">{{ $group->name }}</h5>
              <span class="badge bg-{{ $group->accepting_applications ? 'success' : 'secondary' }}">{{ $group->accepting_applications ? 'Open' : 'Closed' }}</span>
            </div>
            <div class="text-muted small mb-2">Members: {{ $group->members->count() }}</div>
            <p class="card-text flex-grow-1">{{ Str::limit($group->description, 120) }}</p>
            <div class="mt-2 d-flex gap-2">
              <a class="btn btn-sm btn-outline-primary" href="{{ route('groups.show', $group) }}">View</a>
              @if($group->accepting_applications)
                <a class="btn btn-sm btn-primary" href="{{ route('groups.apply', $group) }}">Apply to Join</a>
              @endif
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="mt-3">{{ $publicGroups->links() }}</div>

  <div class="card mt-4">
    <div class="card-header">Your Applications</div>
    <div class="card-body">
      @if($userApplications->count())
        <ul class="list-group">
          @foreach($userApplications as $app)
            <li class="list-group-item d-flex justify-content-between">
              <div>
                <strong>{{ $app->group->name }}</strong>
                <div class="text-muted small">Submitted {{ $app->created_at->diffForHumans() }}</div>
              </div>
              <span class="badge bg-{{ $app->status === 'approved' ? 'success' : ($app->status === 'rejected' ? 'danger' : 'warning text-dark') }}">{{ ucfirst($app->status) }}</span>
            </li>
          @endforeach
        </ul>
      @else
        <div class="text-muted">No applications yet.</div>
      @endif
    </div>
  </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Discover SACCOs & Groups</h1>
            <p class="text-muted mb-0">Browse and apply to join public SACCOs and groups</p>
        </div>
        <a href="{{ route('groups.index') }}" class="btn btn-secondary">My Groups</a>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <!-- Search & Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('groups.discover') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="sacco" {{ request('type') === 'sacco' ? 'selected' : '' }}>SACCO</option>
                        <option value="committee" {{ request('type') === 'committee' ? 'selected' : '' }}>Committee</option>
                        <option value="project" {{ request('type') === 'project' ? 'selected' : '' }}>Project</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="location" class="form-select">
                        <option value="">All Locations</option>
                        @foreach(\App\Models\Group::whereNotNull('location')->distinct()->pluck('location') as $loc)
                            <option value="{{ $loc }}" {{ request('location') === $loc ? 'selected' : '' }}>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SACCOs Grid -->
    <div class="row g-4">
        @forelse($publicGroups as $group)
            @php
                $hasApplied = $userApplications->where('group_id', $group->id)->first();
                $isMember = $group->isMember(auth()->user());
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ $group->name }}</h5>
                        <small class="text-white-50">{{ ucfirst($group->type) }}</small>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">{{ Str::limit($group->description, 100) }}</p>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Members:</span>
                                <strong>{{ $group->members->count() }} / {{ $group->min_members ?? 'N/A' }}</strong>
                            </div>
                            @if($group->location)
                            <div class="d-flex justify-content-between small mt-1">
                                <span class="text-muted">Location:</span>
                                <strong>{{ $group->location }}</strong>
                            </div>
                            @endif
                            @if($group->registration_number)
                            <div class="d-flex justify-content-between small mt-1">
                                <span class="text-muted">Reg No:</span>
                                <strong class="text-success">{{ $group->registration_number }}</strong>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between small mt-1">
                                <span class="text-muted">Balance:</span>
                                <strong class="text-success">KSh {{ number_format($group->balance, 2) }}</strong>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            @if($isMember)
                                <a href="{{ route('groups.show', $group) }}" class="btn btn-success">View Group</a>
                            @elseif($hasApplied)
                                <button class="btn btn-secondary" disabled>
                                    @if($hasApplied->status === 'pending')
                                        Application Pending Review
                                    @elseif($hasApplied->status === 'approved')
                                        Application Approved
                                    @else
                                        Application Rejected
                                    @endif
                                </button>
                                <a href="{{ route('groups.show', $group) }}" class="btn btn-outline-primary">View Details</a>
                            @elseif($group->accepting_applications)
                                <a href="{{ route('groups.apply', $group) }}" class="btn btn-primary">Apply to Join</a>
                            @else
                                <button class="btn btn-secondary" disabled>Not Accepting Applications</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <h5>No public groups found</h5>
                    <p class="mb-0">There are currently no SACCOs or groups accepting new members.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $publicGroups->links() }}
    </div>
</div>
@endsection

