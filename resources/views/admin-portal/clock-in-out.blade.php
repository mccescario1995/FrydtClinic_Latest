@extends('admin-portal.layouts.app')

@section('title', 'Time In/Out - Admin Portal')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-clock me-2"></i>Employee Time In/Out
    </h1>
    <p class="page-subtitle">Process employee attendance with PIN verification</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 p-3">
                    <i class="fas fa-fingerprint me-2"></i>Time In / Time Out
                </h5>
            </div>
            <div class="card-body pt-5">
                <div id="datetime-display" class="text-center mb-4">
                    <h4 class="text-primary fw-bold">--:--:-- --</h4>
                    <p class="text-muted mb-0">Current Time</p>
                </div>

                {{-- Success message overlay --}}
                <div id="success-overlay" class="success-overlay" style="display: none;">
                    <div class="success-content">
                        <div class="alert alert-success text-center mb-0">
                            <h3 class="alert-heading mb-3">
                                <i class="fas fa-check-circle fa-2x me-2"></i>{{ $message ?? 'Success!' }}
                            </h3>
                            @isset($user)
                            <div class="employee-info">
                                <h4 class="mb-2">{{ $user->name }}</h4>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <strong>Date:</strong><br>
                                        {{ \Carbon\Carbon::parse($attendance->date)->format('F d, Y') }}
                                    </div>
                                    <div class="col-6">
                                        <strong>Time:</strong><br>
                                        {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i:s A') }}
                                        @if ($attendance->check_out_time)
                                            <br><small>(Check Out)</small>
                                        @else
                                            <br><small>(Check In)</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endisset
                        </div>
                    </div>
                </div>

                {{-- Main clock-in/out form --}}
                <div id="clock-form">
                    {{-- Hidden camera elements --}}
                    <video id="video" autoplay style="width: 10px; height: 10px; opacity: 0; position: absolute;"></video>
                    <canvas id="canvas" style="display:none;"></canvas>

                    <div class="text-center mb-4">
                        <form id="attendance-form" action="{{ route('admin-portal.process-clock-in-out') }}" method="POST" class="d-inline-block">
                            @csrf
                            <div class="mb-4">
                                <label for="pin" class="form-label fw-bold fs-5">Enter 6-Digit PIN</label>
                                <input type="password" id="pin" name="pin" class="form-control form-control-lg text-center"
                                       maxlength="6" required style="font-size: 1.5rem; letter-spacing: 0.5rem;">
                                <div class="form-text">Enter your employee PIN to time in or out</div>
                            </div>
                            <input type="hidden" name="image_data" id="image_data">

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check me-2"></i>Submit
                                </button>
                                <a href="{{ route('admin-portal.attendance') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-list me-1"></i>View Attendance Records
                                </a>
                            </div>
                        </form>
                    </div>

                <div id="message" class="mt-3">
                    @if (session('error'))
                        <div class="alert alert-danger text-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger text-center">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first('pin') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.success-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.success-content {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.employee-info {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1rem;
    margin-top: 1rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const pinInput = document.getElementById('pin');
    const datetimeDisplay = document.getElementById('datetime-display');
    const successOverlay = document.getElementById('success-overlay');
    const clockForm = document.getElementById('clock-form');

    // Show success overlay if we have success data
    @isset($user)
        if (successOverlay) {
            successOverlay.style.display = 'flex';
            if (clockForm) clockForm.style.display = 'none';

            // Auto-hide after 3 seconds and redirect
            setTimeout(() => {
                window.location.href = '{{ route("admin-portal.clock-in-out") }}';
            }, 3000);
        }
    @endisset

    // Access the camera (hidden)
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');

    if (video) {
        navigator.mediaDevices.getUserMedia({
            video: {
                width: { ideal: 640 },
                height: { ideal: 480 },
                facingMode: 'user'
            }
        })
        .then(stream => {
            video.srcObject = stream;
            video.play(); // Ensure video plays
        })
        .catch(err => {
            console.error("Error accessing the camera:", err);
            // No alert, just log
        });
    }

    // Function to update the date and time display
    function updateDateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        });
        const dateString = now.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        if (datetimeDisplay) {
            datetimeDisplay.innerHTML = `
                <h4 class="text-primary fw-bold mb-1">${timeString}</h4>
                <p class="text-muted mb-0">${dateString}</p>
            `;
        }
    }

    // Update the time every second
    setInterval(updateDateTime, 1000);
    updateDateTime(); // Initial call

    // Function to capture the image from the video feed (silently)
    function captureImage() {
        return new Promise((resolve, reject) => {
            if (!video || !canvas) {
                reject(new Error('Video or canvas not available'));
                return;
            }

            // Wait for video to be ready
            const checkReady = () => {
                if (video.readyState === 4 && video.videoWidth > 0) {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const context = canvas.getContext('2d');
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);

                    const imageData = canvas.toDataURL('image/jpeg', 0.8);
                    resolve(imageData);
                } else if (video.readyState >= 2) {
                    // Wait a bit more
                    setTimeout(checkReady, 100);
                } else {
                    reject(new Error('Video not ready'));
                }
            };

            // Initial delay
            setTimeout(checkReady, 1000);
        });
    }

    let isSubmitting = false;

    if (pinInput) {
        pinInput.addEventListener('keyup', async function(event) {
            if (this.value.length === 6 && !isSubmitting) {
                isSubmitting = true;

                try {
                    // Capture image silently
                    const imageData = await captureImage();
                    document.getElementById('image_data').value = imageData;

                    // Submit the form
                    document.getElementById('attendance-form').submit();
                } catch (error) {
                    console.error('Capture failed:', error);
                    alert('Failed to capture image. Please try again.');
                    isSubmitting = false;
                }
            }
        });
    }
});
</script>
@endsection
