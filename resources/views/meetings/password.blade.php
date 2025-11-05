@extends('layouts.app')

@section('content')
<div class="container py-5" style="max-width:480px;">
  <div class="card shadow-sm">
    <div class="card-body">
      <h1 class="h5 mb-3">Enter Meeting Password</h1>
      @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
      @endif
      <form method="POST" action="{{ route('meetings.password.verify', $meeting) }}" class="vstack gap-3">
        @csrf
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <button type="submit" class="btn btn-primary w-100">Continue</button>
      </form>
      <div class="text-center mt-3">
        <a href="{{ route('meetings.show', $meeting) }}" class="small">Back</a>
      </div>
    </div>
  </div>
  <div class="text-muted small text-center mt-3">This meeting is protected. Please provide the password.</div>
  </div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Meeting Password Required</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">This meeting is password protected. Please enter the password to continue.</p>
                    
                    <form method="POST" action="{{ route('meetings.password.verify', $meeting) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="password" class="form-label">Meeting Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autofocus>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Join Meeting</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

