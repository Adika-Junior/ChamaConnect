@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-4">Settings</h1>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="row g-4">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Notification Preferences</div>
        <div class="card-body">
          <form method="POST" action="{{ route('settings.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <label for="digest_frequency" class="form-label">Digest Frequency</label>
              <select name="digest_frequency" id="digest_frequency" class="form-select">
                <option value="daily" {{ old('digest_frequency', $user->digest_frequency ?? 'daily') === 'daily' ? 'selected' : '' }}>Daily</option>
                <option value="weekly" {{ old('digest_frequency', $user->digest_frequency ?? 'daily') === 'weekly' ? 'selected' : '' }}>Weekly</option>
              </select>
              <div class="form-text">Receive a summary of payments, contributions, and meetings updates.</div>
            </div>

            <div class="mb-3">
              <label for="quiet_hours_start" class="form-label">Quiet Hours Start</label>
              <input type="time" name="quiet_hours_start" id="quiet_hours_start" class="form-control" value="{{ old('quiet_hours_start', $user->quiet_hours_start) }}">
              <div class="form-text">Optional: Start time for quiet hours (notifications will be delayed).</div>
            </div>

            <div class="mb-3">
              <label for="quiet_hours_end" class="form-label">Quiet Hours End</label>
              <input type="time" name="quiet_hours_end" id="quiet_hours_end" class="form-control" value="{{ old('quiet_hours_end', $user->quiet_hours_end) }}">
            </div>

            <button type="submit" class="btn btn-primary">Save Preferences</button>
          </form>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-header">Calendar Subscription</div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">iCal Feed URL</label>
            <div class="input-group">
              <input type="text" class="form-control" value="{{ $icalUrl }}" readonly id="ical-url">
              <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('ical-url')">Copy</button>
            </div>
            <div class="form-text">Subscribe to this URL in your calendar app (Google Calendar, Outlook, Apple Calendar) to see your meetings.</div>
          </div>

          <form method="POST" action="{{ route('settings.update') }}" onsubmit="return confirm('Regenerate calendar token? You will need to update your calendar subscription.');">
            @csrf
            @method('PUT')
            <input type="hidden" name="regenerate_calendar_token" value="1">
            <button type="submit" class="btn btn-outline-warning">Regenerate Token</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function copyToClipboard(id) {
  const el = document.getElementById(id);
  el.select();
  document.execCommand('copy');
  alert('Copied to clipboard!');
}
</script>
@endsection

