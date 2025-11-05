@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Group</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('groups.update', $group) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Group Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $group->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Group Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="sacco" {{ old('type', $group->type) === 'sacco' ? 'selected' : '' }}>SACCO</option>
                                <option value="committee" {{ old('type', $group->type) === 'committee' ? 'selected' : '' }}>Committee</option>
                                <option value="project" {{ old('type', $group->type) === 'project' ? 'selected' : '' }}>Project</option>
                                <option value="other" {{ old('type', $group->type) === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $group->description) }}</textarea>
                        </div>
                        @if(auth()->user()->isAdmin())
                        <div class="mb-3">
                            <label for="member_quota" class="form-label">
                                Member Quota (System Admin)
                                <span class="badge bg-info ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Set a maximum member limit for billing/payment purposes. Group admins cannot add members beyond this limit. Leave blank for unlimited.">ℹ️</span>
                            </label>
                            <input type="number" class="form-control" id="member_quota" name="member_quota" value="{{ old('member_quota', $group->member_quota) }}" min="1" placeholder="e.g., 100">
                            <div class="form-text">Maximum members allowed in this group. Leave blank for unlimited.</div>
                        </div>
                        @endif
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Group</button>
                            <a href="{{ route('groups.show', $group) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
</script>
@endsection

