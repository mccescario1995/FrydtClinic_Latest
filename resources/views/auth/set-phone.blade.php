@extends('layouts.blank')

@section('title', 'Set Phone Number')

@section('css')
<style>
    .auth-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .auth-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        max-width: 450px;
        width: 100%;
        position: relative;
    }

    .auth-header {
        background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
        color: white;
        padding: 30px 40px;
        text-align: center;
        position: relative;
    }

    .auth-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="rgba(255,255,255,0.1)"/></svg>');
        background-size: 200px 200px;
        opacity: 0.3;
    }

    .auth-header h4 {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
        position: relative;
        z-index: 1;
    }

    .auth-body {
        padding: 40px;
    }

    .user-info {
        text-align: center;
        margin-bottom: 30px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }

    .user-name {
        font-size: 18px;
        font-weight: 600;
        color: #22c55e;
        margin: 0;
    }

    .phone-input-container {
        position: relative;
        margin-bottom: 30px;
    }

    .phone-input {
        width: 100%;
        padding: 16px 20px 16px 50px;
        font-size: 18px;
        text-align: center;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        background: #f8f9fa;
        transition: all 0.3s ease;
        font-weight: 500;
        letter-spacing: 1px;
    }

    .phone-input:focus {
        outline: none;
        border-color: #22c55e;
        background: white;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
        transform: translateY(-2px);
    }

    .phone-input::placeholder {
        color: #adb5bd;
        font-weight: 400;
    }

    .input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #22c55e;
        font-size: 20px;
        z-index: 1;
    }

    .phone-input:focus + .input-icon {
        color: #16a34a;
    }

    .input-hint {
        text-align: center;
        color: #6c757d;
        font-size: 14px;
        margin-top: 8px;
        font-weight: 500;
    }

    .submit-btn {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
    }

    .submit-btn:active {
        transform: translateY(0);
    }

    .auth-footer {
        text-align: center;
        padding: 20px 40px 30px;
        border-top: 1px solid #e9ecef;
    }

    .logout-link {
        color: #6c757d;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .logout-link:hover {
        color: #dc3545;
    }

    .alert {
        border-radius: 12px;
        border: none;
        margin-bottom: 20px;
    }

    .phone-preview {
        display: none;
        margin-top: 15px;
        padding: 12px;
        background: #e8f5e8;
        border-radius: 8px;
        border: 1px solid #c3e6c3;
        text-align: center;
        font-weight: 500;
        color: #155724;
    }

    @media (max-width: 480px) {
        .auth-container {
            padding: 10px;
        }

        .auth-header {
            padding: 25px 20px;
        }

        .auth-body {
            padding: 30px 20px;
        }

        .phone-input {
            font-size: 16px; /* Prevent zoom on iOS */
        }
    }
</style>
@endsection

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <!-- Header -->
        <div class="auth-header">
            <h4>
                <i class="fas fa-mobile-alt me-2"></i>Set Phone Number
            </h4>
        </div>

        <!-- Body -->
        <div class="auth-body">
            <!-- User Info -->
            <div class="user-info">
                <p class="mb-2 text-muted">Welcome,</p>
                <p class="user-name">{{ auth()->user()->name }}</p>
                <p class="mb-0 text-muted small">Please set your phone number for secure OTP verification</p>
            </div>

            <!-- Alerts -->
            @if (session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Phone Input Form -->
            <form action="{{ route('set-phone.post') }}" method="POST" id="phoneForm">
                @csrf

                <div class="phone-input-container">
                    <i class="fas fa-mobile-alt input-icon"></i>
                    <input type="tel"
                            class="phone-input @error('phone') is-invalid @enderror"
                            id="phone"
                            name="phone"
                            placeholder="09xxxxxxxxx or +639xxxxxxxxx"
                            required
                            autofocus
                            maxlength="13">
                    <div class="input-hint">Enter your Philippine mobile number</div>
                    @error('phone')
                        <div class="invalid-feedback text-center mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Phone Preview -->
                <div class="phone-preview" id="phonePreview">
                    <i class="fas fa-check-circle me-1"></i>
                    Formatted: <span id="formattedPhone"></span>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="submit-btn" id="submitBtn">
                    <i class="fas fa-save"></i>
                    <span>Set Phone Number</span>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="auth-footer">
            <a href="{{ route('backpack.auth.logout') }}" class="logout-link">
                <i class="fas fa-sign-out-alt me-1"></i>Logout instead
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    const phonePreview = document.getElementById('phonePreview');
    const formattedPhone = document.getElementById('formattedPhone');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('phoneForm');

    let isValidPhone = false;

    // Auto-focus on load
    phoneInput.focus();

    // Format phone number as user types
    function formatPhoneNumber(value) {
        let cleanValue = value.replace(/\D/g, ''); // Remove non-digits

        // Format as 09xxxxxxxxx or +639xxxxxxxxx
        if (cleanValue.startsWith('9') && cleanValue.length <= 10) {
            return '09' + cleanValue.substring(1);
        } else if (cleanValue.startsWith('639') && cleanValue.length <= 12) {
            return '+639' + cleanValue.substring(3);
        } else if (cleanValue.startsWith('63') && cleanValue.length <= 12) {
            return '+63' + cleanValue.substring(2);
        }

        return cleanValue;
    }

    // Validate phone number
    function validatePhoneNumber(value) {
        const cleanValue = value.replace(/\D/g, '');
        // Philippine mobile numbers: 09xxxxxxxxx or +639xxxxxxxxx
        const mobileRegex = /^(09\d{9}|\+639\d{9})$/;
        return mobileRegex.test(value);
    }

    // Update phone input and preview
    phoneInput.addEventListener('input', function(e) {
        const rawValue = this.value;
        const formattedValue = formatPhoneNumber(rawValue);

        // Update input value
        this.value = formattedValue;

        // Update preview
        if (formattedValue.length >= 10) {
            formattedPhone.textContent = formattedValue;
            phonePreview.style.display = 'block';

            // Validate
            isValidPhone = validatePhoneNumber(formattedValue);
            this.classList.toggle('is-invalid', !isValidPhone);
            this.classList.toggle('is-valid', isValidPhone);

            // Update submit button
            submitBtn.disabled = !isValidPhone;
            submitBtn.innerHTML = isValidPhone ?
                '<i class="fas fa-save"></i><span>Set Phone Number</span>' :
                '<i class="fas fa-times"></i><span>Invalid Phone Number</span>';
        } else {
            phonePreview.style.display = 'none';
            this.classList.remove('is-invalid', 'is-valid');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-save"></i><span>Set Phone Number</span>';
            isValidPhone = false;
        }
    });

    // Auto-submit on enter (only if valid)
    phoneInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && isValidPhone) {
            e.preventDefault();
            form.submit();
        }
    });

    // Form submission feedback
    form.addEventListener('submit', function(e) {
        if (!isValidPhone) {
            e.preventDefault();
            phoneInput.focus();
            return;
        }

        // Disable form during submission
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Setting Phone...</span>';
        phoneInput.readOnly = true;
    });

    // Handle backspace for better UX
    phoneInput.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace') {
            const start = this.selectionStart;
            const end = this.selectionEnd;

            if (start === end && start > 0) {
                // Single character deletion
                const newValue = this.value.slice(0, start - 1) + this.value.slice(end);
                this.value = formatPhoneNumber(newValue);
                this.setSelectionRange(start - 1, start - 1);
                e.preventDefault();
            }
        }
    });
});
</script>
@endsection
