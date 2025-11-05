@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Roles</h1>
        @can('create', App\Models\Role::class)
            <a href="{{ route('roles.create') }}" class="btn btn-primary">Create Role</a>
        @endcan
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('roles.index') }}" class="row g-2">
                <div class="col-md-4">
                    <select name="department_id" class="form-control" onchange="this.form.submit()">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Display Name</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Level</th>
                            <th>Users</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td><strong>{{ $role->display_name }}</strong></td>
                                <td><code>{{ $role->name }}</code></td>
                                <td>{{ $role->department ? $role->department->name : 'Global' }}</td>
                                <td><span class="badge bg-info">Level {{ $role->level }}</span></td>
                                <td>{{ $role->users->count() }}</td>
                                <td>
                                    <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    @can('update', $role)
                                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    @endcan
                                    @can('delete', $role)
                                        <form method="POST" action="{{ route('roles.destroy', $role) }}" class="d-inline" onsubmit="return confirm('Delete this role?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No roles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

