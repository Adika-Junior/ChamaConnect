@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-3">Edit Meeting</h1>

    <form method="POST" action="{{ route('meetings.update', $meeting) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $meeting->title) }}" required>
            @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $meeting->description) }}</textarea>
        </div>

        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select" required>
                    <option value="online" @selected(old('type', $meeting->type)==='online')>Online</option>
                    <option value="physical" @selected(old('type', $meeting->type)==='physical')>Physical</option>
                    <option value="hybrid" @selected(old('type', $meeting->type)==='hybrid')>Hybrid</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Scheduled At</label>
                <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at', optional($meeting->scheduled_at)->format('Y-m-d\TH:i')) }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Duration (minutes)</label>
                <input type="number" name="duration" class="form-control" value="{{ old('duration', $meeting->duration) }}" min="0">
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="scheduled" @selected(old('status', $meeting->status)==='scheduled')>Scheduled</option>
                    <option value="ongoing" @selected(old('status', $meeting->status)==='ongoing')>Ongoing</option>
                    <option value="completed" @selected(old('status', $meeting->status)==='completed')>Completed</option>
                    <option value="cancelled" @selected(old('status', $meeting->status)==='cancelled')>Cancelled</option>
                </select>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <label class="form-label">Meeting Link</label>
                <input type="url" name="meeting_link" class="form-control" value="{{ old('meeting_link', $meeting->meeting_link) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Link to Contribution (optional)</label>
                <select name="contribution_id" class="form-select">
                    <option value="">— None —</option>
                    @foreach($contributions as $c)
                        <option value="{{ $c->id }}" @selected(old('contribution_id', $meeting->contribution_id)==$c->id)>{{ $c->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-md-4">
                <div class="form-check">
                    <input type="checkbox" name="has_waiting_room" id="has_waiting_room" class="form-check-input" value="1" {{ old('has_waiting_room', $meeting->has_waiting_room) ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_waiting_room">Enable Waiting Room</label>
                    <small class="form-text text-muted d-block">Host must admit participants</small>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Meeting Password (optional)</label>
                <input type="text" name="password" class="form-control" value="{{ old('password', $meeting->password) }}" placeholder="Leave empty for no password">
                <small class="form-text text-muted">Participants will need this to join</small>
            </div>
            <div class="col-md-4">
                <div class="form-check mt-4">
                    <input type="checkbox" name="is_locked" id="is_locked" class="form-check-input" value="1" {{ old('is_locked', $meeting->is_locked) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_locked">Lock Meeting</label>
                    <small class="form-text text-muted d-block">Prevent new participants</small>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>
@endsection


