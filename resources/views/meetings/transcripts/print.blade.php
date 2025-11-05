@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0">Transcript - {{ $meeting->title }}</h1>
    <button class="btn btn-primary" onclick="window.print()">Print / Save as PDF</button>
  </div>
  <div class="card">
    <div class="card-body">
      @if($transcript->file_path)
        <div class="text-muted">Attached file: <a href="{{ asset('storage/'.$transcript->file_path) }}" target="_blank">{{ $transcript->file_name }}</a></div>
      @endif
      @if($transcript->content)
        <pre class="mt-3" style="white-space: pre-wrap;">{{ $transcript->content }}</pre>
      @else
        <div class="text-muted">No text content available. Download the attached file above.</div>
      @endif
    </div>
  </div>
</div>
@endsection


