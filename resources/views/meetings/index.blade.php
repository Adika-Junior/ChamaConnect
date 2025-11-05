@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Meetings</h1>
        <a href="{{ route('meetings.create') }}" class="btn btn-primary">Schedule Meeting</a>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Scheduled</th>
                        <th>Contribution</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($meetings as $meeting)
                    <tr>
                        <td><a href="{{ route('meetings.show', $meeting) }}">{{ $meeting->title }}</a></td>
                        <td>{{ ucfirst($meeting->type) }}</td>
                        <td>{{ $meeting->scheduled_at?->format('Y-m-d H:i') }}</td>
                        <td>{{ $meeting->contribution?->title ?? '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('meetings.edit', $meeting) }}" class="btn btn-sm btn-secondary">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No meetings yet.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $meetings->links() }}
    </div>
</div>
@endsection


