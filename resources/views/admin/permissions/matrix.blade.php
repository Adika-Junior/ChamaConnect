@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-4">Role & Permission Matrix</h1>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="card mb-4">
    <div class="card-header">
      <span class="badge bg-info ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Use this matrix to assign granular permissions to roles. Check the boxes to grant specific actions.">ℹ️</span>
      System Roles
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-bordered">
          <thead>
            <tr>
              <th>Permission</th>
              @foreach($roles as $role)
              <th class="text-center">{{ $role->display_name }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach($permissions->groupBy('category') as $category => $perms)
            <tr class="table-light">
              <td colspan="{{ $roles->count() + 1 }}" class="fw-bold">{{ ucfirst($category) }}</td>
            </tr>
            @foreach($perms as $perm)
            <tr>
              <td>
                {{ $perm->display_name }}
                @if($perm->description)
                <span class="badge bg-info ms-1" data-bs-toggle="tooltip" title="{{ $perm->description }}">?</span>
                @endif
              </td>
              @foreach($roles as $role)
              <td class="text-center">
                <input type="checkbox" 
                  class="form-check-input role-perm" 
                  data-role="{{ $role->id }}" 
                  data-perm="{{ $perm->id }}"
                  {{ $role->permissions->contains($perm->id) ? 'checked' : '' }}>
              </td>
              @endforeach
            </tr>
            @endforeach
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="mt-3">
        <button id="save-matrix" class="btn btn-primary">Save Changes</button>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">SACCO Role Templates</div>
    <div class="card-body">
      <ul class="list-group">
        @foreach($templates as $template)
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <strong>{{ $template->display_name }}</strong>
            @if($template->description)
            <div class="text-muted small">{{ $template->description }}</div>
            @endif
            <div class="mt-1">
              @foreach($template->permissions ?? [] as $p)
              <span class="badge bg-secondary me-1">{{ $p }}</span>
              @endforeach
            </div>
          </div>
          <a href="{{ route('admin.sacco_role_templates.show', $template) }}" class="btn btn-sm btn-outline-primary">Edit</a>
        </li>
        @endforeach
      </ul>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const checkboxes = document.querySelectorAll('.role-perm');
  const saveBtn = document.getElementById('save-matrix');
  let changes = {};

  checkboxes.forEach(cb => {
    cb.addEventListener('change', function() {
      const roleId = this.dataset.role;
      if (!changes[roleId]) changes[roleId] = [];
      
      if (this.checked) {
        if (!changes[roleId].includes(this.dataset.perm)) {
          changes[roleId].push(this.dataset.perm);
        }
      } else {
        changes[roleId] = changes[roleId].filter(p => p !== this.dataset.perm);
      }
    });
  });

  saveBtn.addEventListener('click', async function() {
    for (const [roleId, permIds] of Object.entries(changes)) {
      await fetch(`/admin/roles/${roleId}/permissions`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ permissions: permIds })
      });
    }
    location.reload();
  });

  // Initialize tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
</script>
@endsection

