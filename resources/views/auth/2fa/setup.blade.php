@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Two-Factor Authentication</h5>
                </div>
                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if($user->two_factor_enabled)
                        <div class="alert alert-success">
                            <strong>✓ Two-factor authentication is enabled</strong>
                            <p class="mb-0 mt-2">Your account is protected with two-factor authentication.</p>
                        </div>

                        @if(session('backup_codes'))
                            <div class="alert alert-warning">
                                <strong>Important: Save these backup codes!</strong>
                                <p class="mb-2">These codes can be used to access your account if you lose access to your phone.</p>
                                <div class="bg-light p-3 rounded">
                                    @foreach(session('backup_codes') as $code)
                                        <code class="d-block mb-1">{{ $code }}</code>
                                    @endforeach
                                </div>
                                <small class="text-muted">Save these codes in a secure location. Each code can only be used once.</small>
                            </div>
                        @endif

                        @if($user->two_factor_backup_codes && count($user->two_factor_backup_codes) > 0)
                            <div class="mb-3">
                                <p><strong>Remaining backup codes:</strong> {{ count($user->two_factor_backup_codes) }}</p>
                            </div>
                        @endif

                        <div class="mb-3">
                            <form method="POST" action="{{ route('2fa.disable') }}" onsubmit="return confirm('Are you sure you want to disable 2FA? This will make your account less secure.');">
                                @csrf
                                <div class="mb-2">
                                    <label for="password" class="form-label">Current Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <button type="submit" class="btn btn-danger">Disable Two-Factor Authentication</button>
                            </form>
                        </div>

                        <div class="mb-3">
                            <form method="POST" action="{{ route('2fa.regenerate-backup-codes') }}" onsubmit="return confirm('This will invalidate your current backup codes. Generate new ones?');">
                                @csrf
                                <div class="mb-2">
                                    <label for="password_regen" class="form-label">Current Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password_regen" name="password" required>
                                </div>
                                <button type="submit" class="btn btn-warning">Regenerate Backup Codes</button>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <strong>Two-factor authentication is not enabled</strong>
                            <p class="mb-0 mt-2">Enable 2FA to add an extra layer of security to your account.</p>
                        </div>

                        @if(!$user->phone)
                            <div class="alert alert-warning">
                                <strong>Phone number required</strong>
                                <p class="mb-0">Please add a phone number to your profile to enable 2FA.</p>
                                <a href="{{ route('profile.show') }}" class="btn btn-sm btn-primary mt-2">Update Profile</a>
                            </div>
                        @else
                            <form method="POST" action="{{ route('2fa.enable') }}">
                                @csrf
                                <p>When enabled, you'll receive a verification code via SMS to <strong>{{ $user->phone }}</strong> when logging in.</p>
                                <button type="submit" class="btn btn-primary">Enable Two-Factor Authentication</button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

