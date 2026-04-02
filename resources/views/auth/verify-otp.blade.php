@extends('layouts.blank')

@section('title', 'Verify OTP')

@section('css')
<style>
    .otp-container {
        max-width: 400px;
        margin: 50px auto;
    }

    .otp-input {
        font-size: 2rem;
        text-align: center;
        letter-spacing: 0.5rem;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        width: 100%;
    }

    .otp-input:focus {
        border-color: #4ade80;
        box-shadow: 0 0 0 0.2rem rgba(74, 222, 128, 0.25);
    }

    .countdown {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .resend-btn:disabled {
        opacity: 0.6;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-mobile-alt me-2"></i>Verify OTP
                    </h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <p class="text-muted">Enter the 6-digit code sent to</p>
                        <p class="text-primary fw-bold">{{ $phone ?? 'your phone' }}</p>
                        <p class="text-muted small">Code expires in <span id="countdown" class="countdown">5:00</span></p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('verify-otp.post') }}" method="POST" id="otp-form">
                        @csrf

                        <div class="mb-3">
                            <label for="otp" class="form-label">OTP Code <span class="text-danger">*</span></label>
                            <input type="text"
                                    class="form-control otp-input @error('otp') is-invalid @enderror"
                                    id="otp"
                                    name="otp"
                                    maxlength="6"
                                    pattern="[0-9]{6}"
                                    inputmode="numeric"
                                    placeholder="000000"
                                    required
                                    autofocus>
                            <div class="input-hint">Enter the 6-digit code sent to your phone</div>
                            @error('otp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="verify-btn">
                                <i class="fas fa-check me-2"></i>Verify OTP
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <button type="button"
                                class="btn btn-link text-primary"
                                id="resend-btn"
                                @if(!($can_resend ?? true)) disabled @endif>
                            <small>Resend OTP</small>
                        </button>
                    </div>

                    <div class="text-center mt-2">
                        <a href="{{ route('logout') }}" class="text-muted">
                            <small>Logout</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const otpInput = document.getElementById('otp');
    const resendBtn = document.getElementById('resend-btn');
    const countdownEl = document.getElementById('countdown');
    const form = document.getElementById('otp-form');
    const verifyBtn = document.getElementById('verify-btn');

    let countdownInterval;
    let expiresAt = new Date('{{ $otp_expires_at ?? now()->addMinutes(5)->toISOString() }}');

    // Auto-focus on load
    otpInput.focus();

    // Only allow numeric input
    otpInput.addEventListener('input', function(e) {
        // Remove any non-numeric characters
        this.value = this.value.replace(/[^0-9]/g, '');

        // Limit to 6 digits
        if (this.value.length > 6) {
            this.value = this.value.slice(0, 6);
        }
    });

    // Auto-submit when 6 digits are entered
    otpInput.addEventListener('input', function(e) {
        if (this.value.length === 6) {
            // Small delay to show the last digit
            setTimeout(() => {
                form.submit();
            }, 300);
        }
    });

    // Start countdown
    function startCountdown() {
        countdownInterval = setInterval(() => {
            const now = new Date();
            const diff = expiresAt - now;

            if (diff <= 0) {
                clearInterval(countdownInterval);
                countdownEl.textContent = 'Expired';
                countdownEl.classList.add('text-danger');
                otpInput.disabled = true;
                verifyBtn.disabled = true;
                return;
            }

            const minutes = Math.floor(diff / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            countdownEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }, 1000);
    }

    // Resend OTP
    resendBtn.addEventListener('click', function() {
        resendBtn.disabled = true;
        resendBtn.innerHTML = '<small>Sending...</small>';

        fetch('{{ route("resend-otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                expiresAt = new Date(data.expires_at);
                startCountdown();
                countdownEl.classList.remove('text-danger');
                otpInput.disabled = false;
                verifyBtn.disabled = false;
                otpInput.value = '';
                otpInput.focus();

                // Show success message
                showAlert('success', data.message);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'Failed to resend OTP. Please try again.');
        })
        .finally(() => {
            setTimeout(() => {
                resendBtn.disabled = false;
                resendBtn.innerHTML = '<small>Resend OTP</small>';
            }, 30000); // Re-enable after 30 seconds
        });
    });

    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        const cardBody = document.querySelector('.card-body');
        cardBody.insertBefore(alertDiv, cardBody.firstChild);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Start countdown on page load
    startCountdown();
});
</script>
@endsection
