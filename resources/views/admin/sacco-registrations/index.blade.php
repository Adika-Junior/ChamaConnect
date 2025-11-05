@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">SACCO Registrations</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-link">Back</a>
    </div>
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Reg No.</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($registrations as $reg)
                    <tr>
                        <td>{{ $reg->name }}</td>
                        <td>{{ $reg->registration_number }}</td>
                        <td>
                            <div>{{ $reg->contact_email }}</div>
                            <div class="text-muted">{{ $reg->contact_phone }}</div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $reg->status === 'approved' ? 'success' : ($reg->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($reg->status) }}</span>
                        </td>
                        <td class="text-end">
                            @if($reg->status === 'pending')
                            <form method="POST" action="{{ route('admin.sacco-registrations.approve', $reg) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success" type="submit">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.sacco-registrations.reject', $reg) }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="reason" value="Insufficient documentation">
                                <button class="btn btn-sm btn-outline-danger" type="submit">Reject</button>
                            </form>
                            @else
                                <span class="text-muted small">Processed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No registrations.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $registrations->links() }}</div>
    </div>
</div>
@endsection


