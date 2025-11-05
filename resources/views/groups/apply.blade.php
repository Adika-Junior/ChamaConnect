@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 720px;">
  <a href="{{ route('groups.discover') }}" class="small text-muted">← Back to Discover</a>
  <h1 class="h4 mt-2">Apply to Join: {{ $group->name }}</h1>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
  @endif

  <div class="card mt-3">
    <div class="card-body">
      <form method="POST" action="{{ route('groups.applications.store', $group) }}" class="vstack gap-3">
        @csrf
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">National ID Number</label>
            <input type="text" name="id_number" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" placeholder="254XXXXXXXXX" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Occupation</label>
            <input type="text" name="occupation" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Address (optional)</label>
            <input type="text" name="address" class="form-control">
          </div>
        </div>

        <div>
          <label class="form-label">Why do you want to join?</label>
          <textarea name="reason" class="form-control" rows="4" placeholder="Describe your motivation" required></textarea>
        </div>

        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="terms_accepted" id="terms" required>
          <label class="form-check-label" for="terms">I agree to abide by this group's rules and code of conduct.</label>
        </div>

        <div class="d-flex gap-2">
          <a href="{{ route('groups.discover') }}" class="btn btn-light">Cancel</a>
          <button type="submit" class="btn btn-primary">Submit Application</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Apply to Join: {{ $group->name }}</h4>
                    <small class="text-white-50">{{ ucfirst($group->type) }}</small>
                </div>
                <div class="card-body">
                    @if($group->description)
                    <div class="alert alert-info">
                        <strong>About:</strong> {{ $group->description }}
                    </div>
                    @endif

                    @if($group->application_requirements)
                    <div class="alert alert-warning">
                        <strong>Requirements:</strong>
                        <p class="mb-0">{{ $group->application_requirements }}</p>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('groups.applications.store', $group) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                            <small class="text-muted">This is your registered name in the system</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="id_number" class="form-label">National ID Number <span class="text-danger">*</span></label>
                            <input type="text" name="id_number" id="id_number" class="form-control @error('id_number') is-invalid @enderror" 
                                   value="{{ old('id_number') }}" required maxlength="20">
                            @error('id_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone', auth()->user()->phone) }}" required maxlength="20" placeholder="254XXXXXXXXX">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="occupation" class="form-label">Occupation/Profession <span class="text-danger">*</span></label>
                            <input type="text" name="occupation" id="occupation" class="form-control @error('occupation') is-invalid @enderror" 
                                   value="{{ old('occupation') }}" required>
                            @error('occupation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Physical Address</label>
                            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" 
                                      rows="2">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Why do you want to join this {{ $group->type }}? <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror" 
                                      rows="4" required minlength="20" maxlength="1000"
                                      placeholder="Please provide a detailed reason (minimum 20 characters)...">{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimum 20 characters, maximum 1000 characters</small>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="terms_accepted" id="terms_accepted" class="form-check-input @error('terms_accepted') is-invalid @enderror" 
                                   required value="1">
                            <label class="form-check-label" for="terms_accepted">
                                I accept the terms and conditions and confirm that all information provided is accurate <span class="text-danger">*</span>
                            </label>
                            @error('terms_accepted')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Submit Application</button>
                            <a href="{{ route('groups.discover') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

