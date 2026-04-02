@extends('layouts.blank')

@section('title', 'Set PIN')

@section('css')
<style>
    .pin-container {
        max-width: 400px;
        margin: 50px auto;
    }

    .pin-input {
        font-size: 2rem;
        text-align: center;
        letter-spacing: 0.5rem;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        width: 100%;
    }

    .pin-input:focus {
        border-color: #4ade80;
        box-shadow: 0 0 0 0.2rem rgba(74, 222, 128, 0.25);
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-key me-2"></i>Set Your PIN
                    </h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <p class="text-muted">Please set a 6-digit PIN for secure access</p>
                        <p class="text-success fw-bold">{{ auth()->user()->name }}</p>
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

                    <form action="{{ route('set-pin.post') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="pin" class="form-label">Enter PIN (6 digits)</label>
                            <input type="password"
                                   class="form-control pin-input @error('pin') is-invalid @enderror"
                                   id="pin"
                                   name="pin"
                                   maxlength="6"
                                   pattern="[0-9]{6}"
                                   inputmode="numeric"
                                   placeholder="000000"
                                   required>
                            @error('pin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="pin_confirmation" class="form-label">Confirm PIN</label>
                            <input type="password"
                                   class="form-control pin-input @error('pin_confirmation') is-invalid @enderror"
                                   id="pin_confirmation"
                                   name="pin_confirmation"
                                   maxlength="6"
                                   pattern="[0-9]{6}"
                                   inputmode="numeric"
                                   placeholder="000000"
                                   required>
                            @error('pin_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-2"></i>Set PIN
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('backpack.auth.logout') }}" class="text-muted">
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
    const pinInput = document.getElementById('pin');
    const confirmInput = document.getElementById('pin_confirmation');

    // Auto-focus on load
    pinInput.focus();

    // Only allow numeric input for both fields
    [pinInput, confirmInput].forEach(input => {
        input.addEventListener('input', function(e) {
            // Remove any non-numeric characters
            this.value = this.value.replace(/[^0-9]/g, '');

            // Limit to 6 digits
            if (this.value.length > 6) {
                this.value = this.value.slice(0, 6);
            }
        });
    });
});
</script>
@endsection
