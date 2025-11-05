@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0">Deploy Verification</h1>
    <a class="btn btn-outline-primary" href="{{ route('admin.deploy.verify') }}">Re-run</a>
  </div>
  <div class="bg-white p-4 rounded shadow">
    <div class="mb-2">Exit code: <span class="fw-semibold">{{ $code }}</span></div>
    <pre class="mb-0" style="white-space: pre-wrap;">{{ trim($output) }}</pre>
  </div>
  <div class="mt-3 text-muted small">Base URL: {{ config('app.url') }}</div>
  <div class="mt-1"><a class="btn btn-sm btn-secondary" href="{{ route('admin.metrics') }}" target="_blank">View Metrics</a></div>
  <div class="mt-3 text-muted small">Tip: Add a CI step to run: <code>php artisan deploy:verify --base-url=$APP_URL</code></div>
  </div>
</div>
@endsection


