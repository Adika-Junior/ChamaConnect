@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Groups</h1>
        @can('create', App\Models\Group::class)
            <a href="{{ route('groups.create') }}" class="btn btn-primary">Create Group</a>
        @endcan
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-3">
        @forelse($groups as $group)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $group->name }}</h5>
                        <p class="text-muted small mb-2">{{ ucfirst($group->type) }}</p>
                        <p class="card-text small">{{ Str::limit($group->description, 100) }}</p>
                        <div class="mb-2">
                            <strong>Balance:</strong> KSh {{ number_format($group->balance, 2) }}
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">{{ $group->members_count }} members</small>
                        </div>
                        <a href="{{ route('groups.show', $group) }}" class="btn btn-sm btn-primary">View Group</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center text-muted py-5">
                    <p>No groups yet. Create one to get started!</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $groups->links() }}</div>
</div>
@endsection

