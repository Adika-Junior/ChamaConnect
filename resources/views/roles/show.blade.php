@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ $role->display_name }}</h1>
        <div class="d-flex gap-2">
            @can('update', $role)
                <a href="{{ route('roles.edit', $role) }}" class="btn btn-secondary">Edit</a>
            @endcan
            @can('delete', $role)
                <form method="POST" action="{{ route('roles.destroy', $role) }}" onsubmit="return confirm('Delete this role?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            @endcan
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Details</div>
                <div class="card-body">
                    <p><strong>Name:</strong> <code>{{ $role->name }}</code></p>
                    <p><strong>Display Name:</strong> {{ $role->display_name }}</p>
                    <p><strong>Department:</strong> {{ $role->department ? $role->department->name : 'Global (All Departments)' }}</p>
                    <p><strong>Level:</strong> <span class="badge bg-info">Level {{ $role->level }}</span></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Users with this Role ({{ $role->users->count() }})</div>
                <div class="card-body">
                    @if($role->users->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($role->users as $user)
                                <li class="list-group-item">
                                    {{ $user->name }}
                                    @if($role->department)
                                        <span class="badge bg-secondary ms-2">{{ $user->department->name ?? 'No dept' }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No users have this role.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

