@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Departments</h1>
        @can('create', App\Models\Department::class)
            <a href="{{ route('departments.create') }}" class="btn btn-primary">Create Department</a>
        @endcan
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Parent</th>
                            <th>Description</th>
                            <th>Users</th>
                            <th>Roles</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $dept)
                            <tr>
                                <td><strong>{{ $dept->name }}</strong></td>
                                <td>{{ $dept->parent ? $dept->parent->name : '—' }}</td>
                                <td>{{ $dept->description ?? '—' }}</td>
                                <td>{{ $dept->users->count() }}</td>
                                <td>{{ $dept->roles->count() }}</td>
                                <td>
                                    <a href="{{ route('departments.show', $dept) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    @can('update', $dept)
                                        <a href="{{ route('departments.edit', $dept) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    @endcan
                                    @can('delete', $dept)
                                        <form method="POST" action="{{ route('departments.destroy', $dept) }}" class="d-inline" onsubmit="return confirm('Delete this department?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No departments yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

