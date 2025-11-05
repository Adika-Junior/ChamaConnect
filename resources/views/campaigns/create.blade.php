@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create Campaign</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('campaigns.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Campaign Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="goal_amount" class="form-label">Goal Amount (KSh) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('goal_amount') is-invalid @enderror" id="goal_amount" name="goal_amount" value="{{ old('goal_amount') }}" required>
                            @error('goal_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_public" name="is_public" {{ old('is_public') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_public">Make campaign public</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="allow_anonymous" name="allow_anonymous" {{ old('allow_anonymous') ? 'checked' : 'checked' }}>
                                <label class="form-check-label" for="allow_anonymous">Allow anonymous donations</label>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Create Campaign</button>
                            <a href="{{ route('campaigns.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

