@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ $department->name }}</h1>
        <div class="d-flex gap-2">
            @can('update', $department)
                <a href="{{ route('departments.edit', $department) }}" class="btn btn-secondary">Edit</a>
            @endcan
            @can('delete', $department)
                <form method="POST" action="{{ route('departments.destroy', $department) }}" onsubmit="return confirm('Delete this department?');">
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
                    <p><strong>Full Path:</strong> {{ $department->full_path }}</p>
                    @if($department->description)
                        <p><strong>Description:</strong> {{ $department->description }}</p>
                    @endif
                    @if($department->parent)
                        <p><strong>Parent:</strong> <a href="{{ route('departments.show', $department->parent) }}">{{ $department->parent->name }}</a></p>
                    @endif
                </div>
            </div>

            @if($department->children->count() > 0)
            <div class="card mt-3">
                <div class="card-header">Sub-Departments</div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($department->children as $child)
                            <li class="list-group-item">
                                <a href="{{ route('departments.show', $child) }}">{{ $child->name }}</a>
                                <span class="badge bg-secondary ms-2">{{ $child->users->count() }} users</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <div class="card mt-3">
                <div class="card-header">Roles</div>
                <div class="card-body">
                    @if($department->roles->count() > 0)
                        <ul class="list-group">
                            @foreach($department->roles as $role)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $role->display_name }}</strong>
                                        <span class="text-muted ms-2">({{ $role->name }})</span>
                                        <span class="badge bg-info ms-2">Level {{ $role->level }}</span>
                                    </div>
                                    <span class="badge bg-secondary">{{ $role->users->count() }} users</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No roles defined for this department.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Users ({{ $department->users->count() }})</div>
                <div class="card-body">
                    @if($department->users->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($department->users as $user)
                                <li class="list-group-item">
                                    {{ $user->name }}
                                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">{{ $user->status }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No users in this department.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

