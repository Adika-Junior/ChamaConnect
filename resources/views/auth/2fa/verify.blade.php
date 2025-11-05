@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Verify Your Identity</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <p class="mb-3">Please enter the 6-digit verification code sent to your phone number <strong>{{ auth()->user()->phone }}</strong>.</p>

                    <form id="verifyForm">
                        @csrf
                        <div class="mb-3">
                            <label for="code" class="form-label">Verification Code</label>
                            <input type="text" 
                                   class="form-control form-control-lg text-center" 
                                   id="code" 
                                   name="code" 
                                   maxlength="6" 
                                   pattern="[0-9]{6}"
                                   placeholder="000000"
                                   required
                                   autofocus>
                            <small class="form-text text-muted">Enter the 6-digit code</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Verify</button>
                            <button type="button" id="resendBtn" class="btn btn-outline-secondary">Resend Code</button>
                        </div>

                        <div class="mt-3 text-center">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#backupCodesModal">Use backup code instead</a>
                        </div>
                    </form>

                    <div id="errorMessage" class="alert alert-danger mt-3" style="display:none;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Backup Codes Modal -->
<div class="modal fade" id="backupCodesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enter Backup Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="backupCodeForm">
                    @csrf
                    <div class="mb-3">
                        <label for="backup_code" class="form-label">Backup Code</label>
                        <input type="text" class="form-control" id="backup_code" name="code" required>
                        <small class="form-text text-muted">Enter one of your backup codes</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Verify</button>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('code');
    const verifyForm = document.getElementById('verifyForm');
    const resendBtn = document.getElementById('resendBtn');
    const errorMessage = document.getElementById('errorMessage');

    // Auto-format code input (numbers only, 6 digits)
    codeInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        if (this.value.length === 6) {
            verifyForm.dispatchEvent(new Event('submit', { cancelable: true }));
        }
    });

    verifyForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const code = codeInput.value;
        if (code.length !== 6) {
            showError('Please enter a 6-digit code');
            return;
        }

        try {
            const response = await fetch('{{ route("2fa.verify") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code })
            });

            const data = await response.json();

            if (data.success) {
                // Redirect to intended URL or dashboard
                const intended = new URLSearchParams(window.location.search).get('intended') || '{{ route("dashboard") }}';
                window.location.href = intended;
            } else {
                showError(data.error || 'Invalid verification code');
                codeInput.value = '';
                codeInput.focus();
            }
        } catch (error) {
            showError('An error occurred. Please try again.');
        }
    });

    resendBtn.addEventListener('click', async function() {
        this.disabled = true;
        this.textContent = 'Sending...';

        try {
            const response = await fetch('{{ route("2fa.send-code") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();
            
            if (response.ok) {
                alert('Verification code sent!');
            } else {
                alert(data.error || 'Failed to send code');
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
        } finally {
            this.disabled = false;
            this.textContent = 'Resend Code';
        }
    });

    document.getElementById('backupCodeForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const code = document.getElementById('backup_code').value;

        try {
            const response = await fetch('{{ route("2fa.verify") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code })
            });

            const data = await response.json();

            if (data.success) {
                const intended = new URLSearchParams(window.location.search).get('intended') || '{{ route("dashboard") }}';
                window.location.href = intended;
            } else {
                alert(data.error || 'Invalid backup code');
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
        }
    });

    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.style.display = 'block';
        setTimeout(() => {
            errorMessage.style.display = 'none';
        }, 5000);
    }
});
</script>
@endsection
@endsection

