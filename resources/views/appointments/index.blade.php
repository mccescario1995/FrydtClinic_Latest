@extends(backpack_view('blank'))

@section('css')
    <style>
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .step {
            display: flex;
            align-items: center;
            margin: 0 10px;
        }

        .step-number {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 10px;
        }

        .step.active .step-number {
            background-color: #0d6efd;
            color: white;
        }

        .step.completed .step-number {
            background-color: #198754;
            color: white;
        }

        .step:not(.active):not(.completed) .step-number {
            background-color: #e9ecef;
            color: #6c757d;
        }

        .booking-form {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .time-slot {
            border: 2px solid #e9ecef;
            padding: 10px 15px;
            margin: 5px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-block;
        }

        .time-slot:hover {
            border-color: #0d6efd;
        }

        .time-slot.selected {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }

        .time-slot.unavailable {
            background-color: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
            opacity: 0.5;
        }

        .service-card {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .service-card:hover {
            border-color: #0d6efd;
            transform: translateY(-2px);
        }

        .service-card.selected {
            border-color: #0d6efd;
            background-color: #f8f9ff;
        }

        .employee-card {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .employee-card:hover {
            border-color: #0d6efd;
        }

        .employee-card.selected {
            border-color: #0d6efd;
            background-color: #f8f9ff;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }

        .alert-custom {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="container my-5">
        <div class="booking-form">
            <h2 class="text-center mb-4"><i class="fas fa-calendar-plus"></i> Book Appointment</h2>

            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-custom">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-custom">
                    <i class="fas fa-exclamation-triangle"></i>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <span>Service</span>
                </div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <span>Date & Time</span>
                </div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <span>Employee</span>
                </div>
                <div class="step" data-step="4">
                    <div class="step-number">4</div>
                    <span>Confirm</span>
                </div>
            </div>

            <form id="appointmentForm" method="POST" action="{{ route('appointment.booking.store') }}">
                @csrf
                <input type="hidden" name="patient_id" value="{{ Auth::id() }}">
                <input type="hidden" id="selected_service" name="service_id">
                <input type="hidden" id="selected_employee" name="employee_id">
                <input type="hidden" id="selected_date" name="appointment_date">
                <input type="hidden" id="selected_time" name="appointment_time">

                <!-- Step 1: Service Selection -->
                <div class="form-step active" data-step="1">
                    <h4 class="mb-3">Select a Service</h4>
                    <div id="services-container">
                        @forelse($services as $service)
                            <div class="service-card" data-service-id="{{ $service->id }}"
                                data-duration="{{ $service->duration_minutes }}">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="mb-1">{{ $service->name }}</h5>
                                        <p class="text-muted mb-1">{{ $service->description ?? 'No description available' }}
                                        </p>
                                        <small class="text-info">
                                            <i class="fas fa-clock"></i> {{ $service->duration_minutes }} minutes
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        @if ($service->base_price)
                                            <h5 class="text-primary mb-0">₱{{ number_format($service->base_price, 2) }}</h5>
                                        @else
                                            <h5 class="text-muted mb-0">Price on consultation</h5>
                                        @endif
                                        <span class="badge bg-secondary">{{ ucfirst($service->type) }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No services available at the moment.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Step 2: Date & Time Selection -->
                <div class="form-step" data-step="2">
                    <h4 class="mb-3">Select Date & Time</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="appointment_date" class="form-label">Appointment Date</label>
                            <input type="date" class="form-control" id="appointment_date"
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                max="{{ date('Y-m-d', strtotime('+30 days')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Available Time Slots</label>
                            <div id="time-slots-container" class="mt-2">
                                <p class="text-muted">Please select a date first</p>
                            </div>
                        </div>
                    </div>
                    <div class="loading" id="time-loading">
                        <i class="fas fa-spinner fa-spin"></i> Loading available times...
                    </div>
                </div>

                <!-- Step 3: Employee Selection -->
                <div class="form-step" data-step="3">
                    <h4 class="mb-3">Select Healthcare Provider</h4>
                    <div id="employees-container">
                        <p class="text-muted">Please complete previous steps first</p>
                    </div>
                    <div class="loading" id="employee-loading">
                        <i class="fas fa-spinner fa-spin"></i> Loading available providers...
                    </div>
                </div>

                <!-- Step 4: Confirmation -->
                <div class="form-step" data-step="4">
                    <h4 class="mb-3">Confirm Your Appointment</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Patient Information</h6>
                                    <p><strong>Name:</strong> {{ Auth::user() }}</p>
                                    <p><strong>Email:</strong> {{ Auth::user() }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Appointment Details</h6>
                                    <p><strong>Service:</strong> <span id="confirm-service">-</span></p>
                                    <p><strong>Date:</strong> <span id="confirm-date">-</span></p>
                                    <p><strong>Time:</strong> <span id="confirm-time">-</span></p>
                                    <p><strong>Provider:</strong> <span id="confirm-employee">-</span></p>
                                    <p><strong>Duration:</strong> <span id="confirm-duration">-</span></p>
                                </div>
                            </div>

                            <div class="mt-3">
                                <label for="patient_notes" class="form-label">Additional Notes (Optional)</label>
                                <textarea class="form-control" id="patient_notes" name="patient_notes" rows="3"
                                    placeholder="Any specific concerns or notes for your appointment..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary" id="prevBtn" onclick="changeStep(-1)"
                        style="display: none;">
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>
                    <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)">
                        Next <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                        <i class="fas fa-check"></i> Book Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('after_scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>
    <script>
        let currentStep = 1;
        const maxStep = 4;
        let selectedService = null;
        let selectedEmployee = null;
        let selectedDate = null;
        let selectedTime = null;

        // CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Step navigation
        function changeStep(direction) {
            if (direction === 1 && !validateCurrentStep()) {
                return;
            }

            const newStep = currentStep + direction;
            if (newStep >= 1 && newStep <= maxStep) {
                // Hide current step
                document.querySelector(`[data-step="${currentStep}"]`).classList.remove('active');

                // Update step indicator
                const currentStepIndicator = document.querySelector(`.step[data-step="${currentStep}"]`);
                if (direction === 1) {
                    currentStepIndicator.classList.add('completed');
                    currentStepIndicator.classList.remove('active');
                }

                // Show new step
                currentStep = newStep;
                document.querySelector(`[data-step="${currentStep}"]`).classList.add('active');

                // Update step indicator
                const nextStepIndicator = document.querySelector(`.step[data-step="${currentStep}"]`);
                nextStepIndicator.classList.add('active');
                if (direction === -1) {
                    nextStepIndicator.classList.remove('completed');
                }

                // Update button visibility
                updateButtonVisibility();

                // Load data for certain steps
                if (currentStep === 2 && direction === 1) {
                    // Entering date/time step
                } else if (currentStep === 3 && direction === 1) {
                    loadAvailableEmployees();
                } else if (currentStep === 4 && direction === 1) {
                    updateConfirmationDetails();
                }
            }
        }

        function updateButtonVisibility() {
            document.getElementById('prevBtn').style.display = currentStep > 1 ? 'block' : 'none';
            document.getElementById('nextBtn').style.display = currentStep < maxStep ? 'block' : 'none';
            document.getElementById('submitBtn').style.display = currentStep === maxStep ? 'block' : 'none';
        }

        function validateCurrentStep() {
            switch (currentStep) {
                case 1:
                    if (!selectedService) {
                        alert('Please select a service');
                        return false;
                    }
                    break;
                case 2:
                    if (!selectedDate || !selectedTime) {
                        alert('Please select both date and time');
                        return false;
                    }
                    break;
                case 3:
                    if (!selectedEmployee) {
                        alert('Please select a healthcare provider');
                        return false;
                    }
                    break;
            }
            return true;
        }

        // Service selection
        document.addEventListener('click', function(e) {
            if (e.target.closest('.service-card')) {
                const serviceCard = e.target.closest('.service-card');

                // Remove previous selection
                document.querySelectorAll('.service-card').forEach(card => {
                    card.classList.remove('selected');
                });

                // Select current service
                serviceCard.classList.add('selected');
                selectedService = {
                    id: serviceCard.dataset.serviceId,
                    name: serviceCard.querySelector('h5').textContent,
                    duration: serviceCard.dataset.duration
                };

                document.getElementById('selected_service').value = selectedService.id;
            }
        });

        // Date selection
        document.getElementById('appointment_date').addEventListener('change', function() {
            selectedDate = this.value;
            document.getElementById('selected_date').value = selectedDate;
            loadAvailableTimeSlots();
        });

        // Load available time slots
        function loadAvailableTimeSlots() {
            if (!selectedService || !selectedDate) return;

            const container = document.getElementById('time-slots-container');
            const loading = document.getElementById('time-loading');

            loading.style.display = 'block';
            container.innerHTML = '<p class="text-muted">Loading available times...</p>';

            // Call the API to get available time slots
            fetch('/api/appointment-get-available-times', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    employee_id: selectedEmployee?.id || null,
                    service_id: selectedService.id,
                    appointment_date: selectedDate
                })
            })
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                container.innerHTML = '';

                if (data.error) {
                    container.innerHTML = `<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> ${data.error}</div>`;
                    return;
                }

                if (data.length === 0) {
                    container.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> No available time slots for this date.</div>';
                    return;
                }

                data.forEach(slot => {
                    const timeSlot = document.createElement('div');
                    timeSlot.className = 'time-slot';
                    timeSlot.dataset.time = slot.time;
                    timeSlot.textContent = slot.display;
                    timeSlot.onclick = () => selectTimeSlot(timeSlot);
                    container.appendChild(timeSlot);
                });
            })
            .catch(error => {
                loading.style.display = 'none';
                container.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error loading available times.</div>';
                console.error('Error:', error);
            });
        }


        function selectTimeSlot(element) {
            // Remove previous selection
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.classList.remove('selected');
            });

            // Select current time
            element.classList.add('selected');
            selectedTime = element.dataset.time;
            document.getElementById('selected_time').value = selectedTime;
        }

        // Load available employees
        function loadAvailableEmployees() {
            if (!selectedService || !selectedDate) return;

            const container = document.getElementById('employees-container');
            const loading = document.getElementById('employee-loading');

            loading.style.display = 'block';

            fetch('/appointment/get-available-employees', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        service_id: selectedService.id,
                        appointment_date: selectedDate
                    })
                })
                .then(response => response.json())
                .then(employees => {
                    loading.style.display = 'none';
                    container.innerHTML = '';

                    if (employees.length === 0) {
                        container.innerHTML =
                            '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> No healthcare providers available for this date.</div>';
                        return;
                    }

                    employees.forEach(employee => {
                        const employeeCard = document.createElement('div');
                        employeeCard.className = 'employee-card';
                        employeeCard.dataset.employeeId = employee.id;
                        employeeCard.innerHTML = `
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${employee.name}</h6>
                                <p class="text-muted mb-0">${employee.email}</p>
                            </div>
                            <div>
                                <i class="fas fa-user-md fa-2x text-primary"></i>
                            </div>
                        </div>
                    `;
                        employeeCard.onclick = () => selectEmployee(employeeCard, employee);
                        container.appendChild(employeeCard);
                    });
                })
                .catch(error => {
                    loading.style.display = 'none';
                    container.innerHTML =
                        '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error loading healthcare providers.</div>';
                    console.error('Error:', error);
                });
        }

        function selectEmployee(element, employee) {
            // Remove previous selection
            document.querySelectorAll('.employee-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Select current employee
            element.classList.add('selected');
            selectedEmployee = employee;
            document.getElementById('selected_employee').value = employee.id;
        }

        // Update confirmation details
        function updateConfirmationDetails() {
            if (selectedService) {
                document.getElementById('confirm-service').textContent = selectedService.name;
                document.getElementById('confirm-duration').textContent = selectedService.duration + ' minutes';
            }

            if (selectedDate) {
                const dateObj = new Date(selectedDate);
                document.getElementById('confirm-date').textContent = dateObj.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            if (selectedTime) {
                const timeObj = new Date(`2023-01-01 ${selectedTime}`);
                document.getElementById('confirm-time').textContent = timeObj.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            if (selectedEmployee) {
                document.getElementById('confirm-employee').textContent = selectedEmployee.name;
            }
        }

        // Form submission
        document.getElementById('appointmentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validateCurrentStep()) {
                return;
            }

            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Booking...';
            submitBtn.disabled = true;

            // Submit form
            this.submit();
        });

        // Initialize
        updateButtonVisibility();
    </script>
@endsection
