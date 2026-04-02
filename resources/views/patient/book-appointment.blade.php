@extends('patient.layouts.app')

@section('title', 'Book Appointment')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Book New Appointment</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('patient.store-appointment') }}" id="appointmentForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="service_id" class="form-label">Service <span class="text-danger">*</span></label>
                                <select name="service_id" id="service_id"
                                    class="form-select @error('service_id') is-invalid @enderror" required>
                                    <option value="">Select a service</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}"
                                            {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }} ({{ $service->duration_minutes }} mins)
                                            @if ($service->base_price)
                                                - ₱{{ number_format($service->base_price, 2) }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="appointment_date" class="form-label">Appointment Date <span class="text-danger">*</span></label>
                                <input type="date" name="appointment_date" id="appointment_date"
                                    class="form-control @error('appointment_date') is-invalid @enderror"
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ old('appointment_date') }}"
                                    required>
                                @error('appointment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="employee_id" class="form-label">Doctor/Staff <span class="text-danger">*</span></label>
                                <select name="employee_id" id="employee_id"
                                    class="form-select @error('employee_id') is-invalid @enderror" required disabled>
                                    <option value="">Select service and date first</option>
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="appointment_time" class="form-label">Time <span class="text-danger">*</span></label>
                                <select name="appointment_time" id="appointment_time"
                                    class="form-select @error('appointment_time') is-invalid @enderror" required disabled>
                                    <option value="">Select employee first</option>
                                </select>
                                @error('appointment_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="patient_notes" class="form-label">Notes (Optional)</label>
                            <textarea name="patient_notes" id="patient_notes" rows="3"
                                class="form-control @error('patient_notes') is-invalid @enderror"
                                placeholder="Any additional information or special requests">{{ old('patient_notes') }}</textarea>
                            @error('patient_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('patient.appointments') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Appointments
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-1"></i>Book Appointment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#service_id, #appointment_date').change(function() {
                if ($('#service_id').val() && $('#appointment_date').val()) {
                    loadEmployees();
                }
            });

            $('#employee_id').change(function() {
                if ($(this).val()) {
                    loadTimeSlots();
                }
            });

            function loadEmployees() {
                $.get('{{ route('patient.api.available-employees') }}', {
                        service_id: $('#service_id').val(),
                        appointment_date: $('#appointment_date').val()
                    })
                    .done(function(data) {
                        let options = '<option value="">Select a doctor/staff</option>';
                        data.forEach(function(employee) {
                            options += `<option value="${employee.id}">${employee.name} - ${employee.position}</option>`;
                        });
                        $('#employee_id').html(options).prop('disabled', false);
                        $('#appointment_time').html('<option value="">Select employee first</option>').prop(
                            'disabled', true);
                    })
                    .fail(function() {
                        alert('Error loading employees. Please try again.');
                    });
            }

            function loadTimeSlots() {
                $.get('{{ route('patient.api.available-time-slots') }}', {
                        employee_id: $('#employee_id').val(),
                        service_id: $('#service_id').val(),
                        appointment_date: $('#appointment_date').val()
                    })
                    .done(function(data) {
                        let options = '<option value="">Select a time</option>';
                        if (data.length === 0) {
                            options = '<option value="">No available time slots</option>';
                        } else {
                            data.forEach(function(slot) {
                                options += `<option value="${slot.time}">${slot.display}</option>`;
                            });
                        }
                        $('#appointment_time').html(options).prop('disabled', data.length === 0);
                    })
                    .fail(function(xhr) {
                        if (xhr.status === 404) {
                            $('#appointment_time').html('<option value="">Employee not available</option>').prop('disabled', true);
                        } else {
                            alert('Error loading time slots. Please try again.');
                        }
                    });
            }
        });
    </script>
@endpush
