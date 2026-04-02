@extends('admin-portal.layouts.app')

@section('title', 'Create Employee Schedule')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0"><i class="fas fa-plus me-2"></i>Create Schedule</h1>
                <p class="text-muted mb-0">Create a new work schedule for an employee</p>
            </div>
            <div>
                <a href="{{ route('admin-portal.schedules') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Schedules
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Schedule Details</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin-portal.schedules.store') }}" id="createScheduleForm">
                    @csrf

                    <!-- Employee Selection -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="employee_id" class="form-label">Employee <span class="text-danger">*</span></label>
                            <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }} ({{ $employee->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Quick Setup Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-copy me-2"></i>Quick Setup</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-3">Set times for one day and copy to others:</p>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="copy_start_time" class="form-label">Start Time</label>
                                            <select class="form-select" id="copy_start_time">
                                                <option value="">Select Start Time</option>
                                                @for ($hour = 0; $hour < 24; $hour++)
                                                    @for ($minute = 0; $minute < 60; $minute += 15)
                                                        @php
                                                            $time = sprintf('%02d:%02d', $hour, $minute);
                                                        @endphp
                                                        <option value="{{ $time }}">{{ $time }}</option>
                                                    @endfor
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="copy_end_time" class="form-label">End Time</label>
                                            <select class="form-select" id="copy_end_time">
                                                <option value="">Select End Time</option>
                                                @for ($hour = 0; $hour < 24; $hour++)
                                                    @for ($minute = 0; $minute < 60; $minute += 15)
                                                        @php
                                                            $time = sprintf('%02d:%02d', $hour, $minute);
                                                        @endphp
                                                        <option value="{{ $time }}">{{ $time }}</option>
                                                    @endfor
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="button" class="btn btn-outline-primary me-2" id="copyToAllBtn">
                                                <i class="fas fa-copy me-1"></i>Copy to All Days
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="clearAllBtn">
                                                <i class="fas fa-times me-1"></i>Clear All
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Days Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="mb-3">Set Schedule for Each Day</h6>
                            <div class="row">
                                @php
                                    $days = [
                                        1 => 'Monday',
                                        2 => 'Tuesday',
                                        3 => 'Wednesday',
                                        4 => 'Thursday',
                                        5 => 'Friday',
                                        6 => 'Saturday',
                                        7 => 'Sunday'
                                    ];
                                @endphp
                                @foreach($days as $dayNum => $dayName)
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">{{ $dayName }}</h6>
                                                <div class="form-check">
                                                    <input class="form-check-input day-checkbox" type="checkbox"
                                                           id="day_{{ $dayNum }}"
                                                           name="days[{{ $dayNum }}][enabled]"
                                                           value="1">
                                                    <label class="form-check-label" for="day_{{ $dayNum }}">
                                                        Working
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="card-body day-schedule" id="schedule_{{ $dayNum }}" style="display: none;">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label for="start_time_{{ $dayNum }}" class="form-label">Start Time</label>
                                                        <select class="form-select time-select"
                                                                id="start_time_{{ $dayNum }}"
                                                                name="days[{{ $dayNum }}][start_time]">
                                                            <option value="">Select Start Time</option>
                                                            @for ($hour = 0; $hour < 24; $hour++)
                                                                @for ($minute = 0; $minute < 60; $minute += 15)
                                                                    @php
                                                                        $time = sprintf('%02d:%02d', $hour, $minute);
                                                                    @endphp
                                                                    <option value="{{ $time }}">{{ $time }}</option>
                                                                @endfor
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <label for="end_time_{{ $dayNum }}" class="form-label">End Time</label>
                                                        <select class="form-select time-select"
                                                                id="end_time_{{ $dayNum }}"
                                                                name="days[{{ $dayNum }}][end_time]">
                                                            <option value="">Select End Time</option>
                                                            @for ($hour = 0; $hour < 24; $hour++)
                                                                @for ($minute = 0; $minute < 60; $minute += 15)
                                                                    @php
                                                                        $time = sprintf('%02d:%02d', $hour, $minute);
                                                                    @endphp
                                                                    <option value="{{ $time }}">{{ $time }}</option>
                                                                @endfor
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> Only checked days will have schedules created. End time must be after start time for each working day.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin-portal.schedules') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-admin-primary">
                            <i class="fas fa-save me-1"></i>Create Schedule
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
document.addEventListener('DOMContentLoaded', function() {
    // Handle day checkbox changes
    document.querySelectorAll('.day-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const dayNum = this.id.split('_')[1];
            const scheduleDiv = document.getElementById('schedule_' + dayNum);
            const startSelect = document.getElementById('start_time_' + dayNum);
            const endSelect = document.getElementById('end_time_' + dayNum);

            if (this.checked) {
                scheduleDiv.style.display = 'block';
                startSelect.required = true;
                endSelect.required = true;
            } else {
                scheduleDiv.style.display = 'none';
                startSelect.required = false;
                endSelect.required = false;
                startSelect.value = '';
                endSelect.value = '';
            }
        });
    });

    // Copy times to all days
    document.getElementById('copyToAllBtn').addEventListener('click', function() {
        const startTime = document.getElementById('copy_start_time').value;
        const endTime = document.getElementById('copy_end_time').value;

        if (!startTime || !endTime) {
            alert('Please select both start and end times to copy.');
            return;
        }

        // Copy to all checked days
        document.querySelectorAll('.day-checkbox:checked').forEach(function(checkbox) {
            const dayNum = checkbox.id.split('_')[1];
            document.getElementById('start_time_' + dayNum).value = startTime;
            document.getElementById('end_time_' + dayNum).value = endTime;
        });

        alert('Times copied to all working days.');
    });

    // Clear all times
    document.getElementById('clearAllBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to clear all schedule times?')) {
            document.querySelectorAll('.time-select').forEach(function(select) {
                select.value = '';
            });
            document.getElementById('copy_start_time').value = '';
            document.getElementById('copy_end_time').value = '';
        }
    });

    // Form validation
    document.getElementById('createScheduleForm').addEventListener('submit', function(e) {
        let hasErrors = false;
        let errorMessages = [];

        document.querySelectorAll('.day-checkbox:checked').forEach(function(checkbox) {
            const dayNum = checkbox.id.split('_')[1];
            const startTime = document.getElementById('start_time_' + dayNum).value;
            const endTime = document.getElementById('end_time_' + dayNum).value;
            const dayName = document.querySelector(`label[for="day_${dayNum}"]`).textContent.trim();

            if (!startTime || !endTime) {
                hasErrors = true;
                errorMessages.push(`${dayName}: Both start and end times are required.`);
            } else if (startTime >= endTime) {
                hasErrors = true;
                errorMessages.push(`${dayName}: End time must be after start time.`);
            }
        });

        if (hasErrors) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n' + errorMessages.join('\n'));
        }
    });
});
</script>
@endpush
