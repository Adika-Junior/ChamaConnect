@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create Role</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('roles.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name (slug) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. manager, admin, member" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Use lowercase with underscores (e.g., department_manager)</small>
                        </div>
                        <div class="mb-3">
                            <label for="display_name" class="form-label">Display Name</label>
                            <input type="text" class="form-control" id="display_name" name="display_name" value="{{ old('display_name') }}" placeholder="e.g. Department Manager">
                            <small class="form-text text-muted">Leave blank to auto-generate from name</small>
                        </div>
                        <div class="mb-3">
                            <label for="department_id" class="form-label">Department</label>
                            <select class="form-control" id="department_id" name="department_id">
                                <option value="">Global (All Departments)</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Select a department to make this role department-specific, or leave blank for global role</small>
                        </div>
                        <div class="mb-3">
                            <label for="level" class="form-label">Level <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('level') is-invalid @enderror" id="level" name="level" value="{{ old('level', 1) }}" min="1" max="10" required>
                            @error('level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Hierarchy level (1 = lowest, 10 = highest)</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Create Role</button>
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

