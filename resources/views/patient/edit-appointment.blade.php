@extends('patient.layouts.app')

@section('title', 'Edit Appointment')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-edit me-2"></i>Edit Appointment</h2>
    <a href="{{ route('patient.appointments') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Appointments
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Appointment Details</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('patient.appointments.update', $appointment->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="service_id" class="form-label">Service <span class="text-danger">*</span></label>
                                <select class="form-select @error('service_id') is-invalid @enderror" id="service_id" name="service_id" required>
                                    <option value="">Select a service</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" {{ $appointment->service_id == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }} - ₱{{ number_format($service->price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="appointment_date" class="form-label">Appointment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('appointment_date') is-invalid @enderror"
                                       id="appointment_date" name="appointment_date"
                                       value="{{ old('appointment_date', $appointment->appointment_datetime->format('Y-m-d')) }}"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                @error('appointment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="appointment_time" class="form-label">Appointment Time <span class="text-danger">*</span></label>
                                <select class="form-select @error('appointment_time') is-invalid @enderror" id="appointment_time" name="appointment_time" required>
                                    <option value="">Select a time</option>
                                </select>
                                @error('appointment_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="employee_id" class="form-label">Doctor/Staff <span class="text-danger">*</span></label>
                                <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                    <option value="">Select a doctor/staff</option>
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="patient_notes" class="form-label">Additional Notes</label>
                        <textarea class="form-control @error('patient_notes') is-invalid @enderror" id="patient_notes" name="patient_notes" rows="3"
                                  placeholder="Any special requests or notes...">{{ old('patient_notes', $appointment->patient_notes) }}</textarea>
                        @error('patient_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Appointment
                        </button>
                        <a href="{{ route('patient.appointments') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceSelect = document.getElementById('service_id');
    const dateInput = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');
    const employeeSelect = document.getElementById('employee_id');

    // Set initial values
    const currentEmployeeId = '{{ $appointment->employee_id }}';
    const currentTime = '{{ $appointment->appointment_datetime->format('H:i') }}';

    // Load available employees when service changes
    serviceSelect.addEventListener('change', function() {
        if (this.value && dateInput.value) {
            loadAvailableEmployees();
        }
    });

    // Load available time slots when date or employee changes
    dateInput.addEventListener('change', function() {
        if (serviceSelect.value) {
            loadAvailableEmployees();
        }
    });

    employeeSelect.addEventListener('change', function() {
        if (this.value && dateInput.value) {
            loadAvailableTimeSlots();
        }
    });

    function loadAvailableEmployees() {
        const serviceId = serviceSelect.value;
        const appointmentDate = dateInput.value;

        if (!serviceId || !appointmentDate) return;

        fetch(`/patient/api/available-employees?service_id=${serviceId}&appointment_date=${appointmentDate}`)
            .then(response => response.json())
            .then(data => {
                employeeSelect.innerHTML = '<option value="">Select a doctor/staff</option>';
                data.forEach(employee => {
                    const selected = employee.id == currentEmployeeId ? 'selected' : '';
                    employeeSelect.innerHTML += `<option value="${employee.id}" ${selected}>${employee.name} (${employee.position})</option>`;
                });

                // Load time slots if employee is already selected
                if (employeeSelect.value) {
                    loadAvailableTimeSlots();
                }
            })
            .catch(error => console.error('Error loading employees:', error));
    }

    function loadAvailableTimeSlots() {
        const employeeId = employeeSelect.value;
        const serviceId = serviceSelect.value;
        const appointmentDate = dateInput.value;

        if (!employeeId || !serviceId || !appointmentDate) return;

        fetch(`/patient/api/available-time-slots?employee_id=${employeeId}&service_id=${serviceId}&appointment_date=${appointmentDate}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                timeSelect.innerHTML = '<option value="">Select a time</option>';
                data.forEach(slot => {
                    const selected = slot.time == currentTime ? 'selected' : '';
                    timeSelect.innerHTML += `<option value="${slot.time}" ${selected}>${slot.display}</option>`;
                });
            })
            .catch(error => console.error('Error loading time slots:', error));
    }

    // Initialize with current values
    if (serviceSelect.value && dateInput.value) {
        loadAvailableEmployees();
    }
});
</script>
@endsection
