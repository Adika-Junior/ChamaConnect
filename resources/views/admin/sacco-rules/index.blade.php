@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">SACCO Contribution Rules</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-link">Back to Dashboard</a>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">Add New Rule</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.sacco-rules.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            @error('name')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug (optional)</label>
                            <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="e.g., monthly_contribution">
                            @error('slug')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>
                        <button class="btn btn-primary" type="submit">Add Rule</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header">Existing Rules</div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($rules as $rule)
                            <tr>
                                <td>{{ $rule->name }}</td>
                                <td><code>{{ $rule->slug }}</code></td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.sacco-rules.destroy', $rule) }}" onsubmit="return confirm('Delete this rule?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted text-center">No rules yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


