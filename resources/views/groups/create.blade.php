@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create Group</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('groups.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Group Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Group Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="sacco" {{ old('type') === 'sacco' ? 'selected' : '' }}>SACCO</option>
                                <option value="committee" {{ old('type') === 'committee' ? 'selected' : '' }}>Committee</option>
                                <option value="project" {{ old('type') === 'project' ? 'selected' : '' }}>Project</option>
                                <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="treasurer_id" class="form-label">Treasurer</label>
                            <select class="form-control" id="treasurer_id" name="treasurer_id">
                                <option value="">Select later</option>
                                @foreach(\App\Models\User::where('status', 'active')->get() as $user)
                                    <option value="{{ $user->id }}" {{ old('treasurer_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="secretary_id" class="form-label">Secretary</label>
                            <select class="form-control" id="secretary_id" name="secretary_id">
                                <option value="">Select later</option>
                                @foreach(\App\Models\User::where('status', 'active')->get() as $user)
                                    <option value="{{ $user->id }}" {{ old('secretary_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Registration Information -->
                        <div class="card mb-3">
                            <div class="card-header">Registration Information (Optional)</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Registration Number</label>
                                        <input type="text" name="registration_number" class="form-control" 
                                               value="{{ old('registration_number') }}" placeholder="e.g., COOP/2024/12345">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Registration Date</label>
                                        <input type="date" name="registered_at" class="form-control" value="{{ old('registered_at') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Location</label>
                                        <input type="text" name="location" class="form-control" 
                                               value="{{ old('location') }}" placeholder="e.g., Nairobi, Kenya">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Minimum Members</label>
                                        <input type="number" name="min_members" class="form-control" 
                                               value="{{ old('min_members', 10) }}" min="1">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contact Email</label>
                                        <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contact Phone</label>
                                        <input type="text" name="contact_phone" class="form-control" 
                                               value="{{ old('contact_phone') }}" placeholder="2547XXXXXXXX">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">By-Laws</label>
                                        <textarea name="by_laws" class="form-control" rows="4" 
                                                  placeholder="Describe the group's by-laws...">{{ old('by_laws') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Membership Settings -->
                        <div class="card mb-3">
                            <div class="card-header">Membership Settings</div>
                            <div class="card-body">
                                <div class="mb-3 form-check">
                                    <input type="checkbox" name="is_public" id="is_public" class="form-check-input" 
                                           value="1" {{ old('is_public') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_public">
                                        Make this group public (appears in discovery page)
                                    </label>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" name="accepting_applications" id="accepting_applications" 
                                           class="form-check-input" value="1" {{ old('accepting_applications', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="accepting_applications">
                                        Accepting new member applications
                                    </label>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Application Requirements</label>
                                    <textarea name="application_requirements" class="form-control" rows="3" 
                                              placeholder="Describe what documents or information applicants need...">{{ old('application_requirements') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Create Group</button>
                            <a href="{{ route('groups.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

