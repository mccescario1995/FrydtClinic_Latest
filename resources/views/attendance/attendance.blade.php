@extends('layouts.blank')

@section('title', 'Employee Attendance')

@section('css')
    <style>
        .container {
            max-width: 600px;
            margin-top: 50px;
        }

        #video {
            width: 100%;
            height: auto;
            border: 1px solid #ccc;
            border-radius: 8px;
            display: none;
        }

        .camera-container {
            position: relative;
        }

        #pin-input-container {
            margin-top: 20px;
        }

        #datetime-display {
            font-size: 1.25rem;
            font-weight: bold;
        }

        .details-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .fade-out {
            opacity: 0;
            transition: opacity 0.5s ease;
            /* fade out over 0.5s */
        }

        .card {
            background-color: #d0f7d8;
        }
    </style>
@endsection

@section('background-color', 'bg-container')

@section('content')
    <div class="container">
        <h1 class="text-center mb-4">Employee Attendance</h1>
        <div class="card p-4">
            <h2 class="text-center">Time In / Time Out</h2>

            <div id="datetime-display" class="text-center mb-3"></div>

            <div class="camera-container text-center mb-3">
                <video id="video" autoplay></video>
                <canvas id="canvas" style="display:none;"></canvas>
            </div>

            {{-- Conditional rendering based on whether data exists from the controller --}}
            @isset($user)
                {{-- This div is the one we will show/hide --}}
                <div id="user-details" style="display: block;">
                    <div id="employee-details" class="details-card ">
                        <div class="text-center mb-3">
                            <h3 class="text-success">{{ $message }}</h3>
                            <p class="lead text-bold text-dark">Welcome, {{ $user->name }}!</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item rounded mb-2">
                                Date: <p>
                                    {{ Carbon\Carbon::parse($attendance->date)->format('F d, Y') }}
                                </p>
                            </li>
                            <li class="list-group-item rounded mb-2">
                                Time In: <p>
                                    {{ $attendance->check_in_time->format('h:i:s A') }}
                                </p>
                            </li>
                            @if ($attendance->check_out_time)
                                <li class="list-group-item rounded mb-2">
                                    Time Out: <p>
                                        {{ $attendance->check_out_time->format('h:i:s A') }}
                                    </p>

                                </li>
                            @endif
                        </ul>

                    </div>
                    <div class="details-card mt-3">
                        <form id="attendance-form" action="{{ route('attendance.process') }}" method="POST">
                            @csrf
                            <div id="pin-input-container" class="mb-3 text-center">
                                <label for="pin" class="form-label fw-bold h1">PIN <span class="text-danger">*</span>:</label>
                                <input type="password" id="pin" name="pin" class="form-control" maxlength="6"
                                    required>
                            </div>
                            <input type="hidden" name="image_data" id="image_data">
                            <input type="hidden" name="action_type" id="action_type">
                        </form>
                    </div>
                </div>
            @else
                {{-- This form is shown when there is no user profile --}}
                <div class="details-card">
                    <form id="attendance-form" action="{{ route('attendance.process') }}" method="POST">
                        @csrf
                        <div id="pin-input-container" class="mb-3  text-center ">
                            <label for="pin" class="form-label fw-bold h1">PIN <span class="text-danger">*</span></label>
                            <input type="password" id="pin" name="pin" class="form-control" maxlength="6" required>
                        </div>
                        <input type="hidden" name="image_data" id="image_data">
                        <input type="hidden" name="action_type" id="action_type">
                    </form>
                </div>
            @endisset
            <div id="message" class="mt-3 text-center">
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first('pin') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const pinInput = document.getElementById('pin');
            if (pinInput) {
                pinInput.focus();
            }

            const userDetailsCard = document.getElementById('employee-details');
            // This checks if the user details card is present on the page.
            if (userDetailsCard) {
                setTimeout(() => {
                    // Start fade out
                    userDetailsCard.classList.add('fade-out');

                    // After transition ends, hide completely
                    setTimeout(() => {
                        userDetailsCard.style.display = 'none';
                    }, 500); // match transition duration (0.5s)
                }, 3000);
            }
        });

        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const pinInput = document.getElementById('pin');
        const form = document.getElementById('attendance-form');
        const message = document.getElementById('message');
        const datetimeDisplay = document.getElementById('datetime-display');

        // Access the camera
        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(err => {
                console.error("Error accessing the camera:", err);
                message.innerText = "Error accessing the camera. Please allow camera access.";
                message.classList.add('text-danger');
            });

        // Function to update the date and time display
        function updateDateTime() {
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const dateString = now.toLocaleDateString('en-US', options);
            const timeString = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            datetimeDisplay.innerText = `${dateString} | ${timeString}`;
        }

        // Update the time every second
        setInterval(updateDateTime, 1000);
        updateDateTime(); // Initial call to display the time immediately

        // Function to capture the image from the video feed
        function captureImage() {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            return canvas.toDataURL('image/jpeg');
        }

        let isSubmitting = false;

        if (pinInput) {
            pinInput.addEventListener('keyup', function(event) {
                if (this.value.length === 6 && !isSubmitting) {
                    isSubmitting = true; // lock

                    // Capture image and set hidden field
                    const imageData = captureImage();
                    document.getElementById('image_data').value = imageData;

                    // Submit the form automatically
                    form.submit();
                }
            });
        }
    </script>
@endsection
