@extends('admin-portal.layouts.app')

@section('title', 'Manage Employee Schedules')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0"><i class="fas fa-calendar-alt me-2"></i>Manage Schedules</h1>
                <p class="text-muted mb-0">Managing schedules for {{ $employee->name }}</p>
            </div>
            <div>
                <a href="{{ route('admin-portal.schedules') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Back to Employees
                </a>
                <button type="button" class="btn btn-admin-primary" id="editSchedulesBtn">
                    <i class="fas fa-edit me-1"></i>Edit Schedules
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Employee Info Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="admin-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        @if ($employee->employeeProfile && $employee->employeeProfile->image_path)
                            <img src="{{ asset('storage/app/public/' . $employee->employeeProfile->image_path) }}"
                                alt="Profile Image" class="rounded-circle"
                                style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                style="width: 80px; height: 80px; font-weight: bold; font-size: 32px;">
                                {{ substr($employee->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <h5 class="mb-1">{{ $employee->name }}</h5>
                        <p class="text-muted mb-1">{{ $employee->email }}</p>
                        @if ($employee->employeeProfile && $employee->employeeProfile->position)
                            <small class="text-primary">{{ $employee->employeeProfile->position }}</small>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="row text-center">
                            <div class="col-3">
                                <div class="h4 mb-0 text-primary">{{ $schedules->count() }}</div>
                                <small class="text-muted">Working Days</small>
                            </div>
                            <div class="col-3">
                                <div class="h4 mb-0 text-success">
                                    @php
                                        $totalHours = 0;
                                        foreach ($schedules as $schedule) {
                                            if ($schedule->start_time && $schedule->end_time) {
                                                $totalHours += $schedule->start_time->diffInHours($schedule->end_time);
                                            }
                                        }
                                        echo $totalHours;
                                    @endphp
                                </div>
                                <small class="text-muted">Hours/Week</small>
                            </div>
                            <div class="col-3">
                                <div class="h4 mb-0 text-info">
                                    @php
                                        echo $totalHours > 0 ? round($totalHours / $schedules->count(), 1) : 0;
                                    @endphp
                                </div>
                                <small class="text-muted">Avg Hours/Day</small>
                            </div>
                            <div class="col-3">
                                <div class="h4 mb-0 text-warning">{{ $schedules->where('day_of_week', now()->dayOfWeekIso)->count() > 0 ? 'Yes' : 'No' }}</div>
                                <small class="text-muted">Working Today</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Mode -->
<div id="viewMode" class="row">
    <div class="col-12">
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-calendar-week me-2"></i>Weekly Schedule</h6>
            </div>
            <div class="card-body">
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
                        @php
                            $schedule = $schedules->where('day_of_week', $dayNum)->first();
                            $isToday = now()->dayOfWeekIso == $dayNum;
                        @endphp
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 {{ $isToday ? 'border-primary' : '' }}">
                                <div class="card-header d-flex justify-content-between align-items-center {{ $isToday ? 'bg-primary text-white' : '' }}">
                                    <h6 class="mb-0">{{ $dayName }}</h6>
                                    @if ($isToday)
                                        <span class="badge bg-light text-primary">Today</span>
                                    @endif
                                </div>
                                <div class="card-body">
                                    @if ($schedule)
                                        <div class="text-center">
                                            <div class="h5 text-success mb-2">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $schedule->start_time ? $schedule->start_time->format('H:i') : 'N/A' }} -
                                                {{ $schedule->end_time ? $schedule->end_time->format('H:i') : 'N/A' }}
                                            </div>
                                            @if ($schedule->start_time && $schedule->end_time)
                                                <small class="text-muted">
                                                    Duration: {{ $schedule->start_time->diff($schedule->end_time)->format('%Hh %Im') }}
                                                </small>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                            <div>No schedule</div>
                                            <small>Day off</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Mode -->
<div id="editMode" class="row" style="display: none;">
    <div class="col-12">
        <div class="admin-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Weekly Schedule</h6>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="cancelEditBtn">
                    <i class="fas fa-times me-1"></i>Cancel Edit
                </button>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin-portal.schedules.update-employee', $employee->id) }}" id="editScheduleForm">
                    @csrf
                    @method('PUT')

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
                                            <label for="edit_copy_start_time" class="form-label">Start Time</label>
                                            <select class="form-select" id="edit_copy_start_time">
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
                                            <label for="edit_copy_end_time" class="form-label">End Time</label>
                                            <select class="form-select" id="edit_copy_end_time">
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
                                            <button type="button" class="btn btn-outline-primary me-2" id="editCopyToAllBtn">
                                                <i class="fas fa-copy me-1"></i>Copy to All Days
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="editClearAllBtn">
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
                                @foreach($days as $dayNum => $dayName)
                                    @php
                                        $existingSchedule = $schedules->where('day_of_week', $dayNum)->first();
                                    @endphp
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">{{ $dayName }}</h6>
                                                <div class="form-check">
                                                    <input class="form-check-input day-checkbox" type="checkbox"
                                                           id="edit_day_{{ $dayNum }}"
                                                           name="days[{{ $dayNum }}][enabled]"
                                                           value="1" {{ $existingSchedule ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="edit_day_{{ $dayNum }}">
                                                        Working
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="card-body day-schedule" id="edit_schedule_{{ $dayNum }}" style="{{ $existingSchedule ? '' : 'display: none;' }}">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label for="edit_start_time_{{ $dayNum }}" class="form-label">Start Time</label>
                                                        <select class="form-select time-select"
                                                                id="edit_start_time_{{ $dayNum }}"
                                                                name="days[{{ $dayNum }}][start_time]">
                                                            <option value="">Select Start Time</option>
                                                            @for ($hour = 0; $hour < 24; $hour++)
                                                                @for ($minute = 0; $minute < 60; $minute += 15)
                                                                    @php
                                                                        $time = sprintf('%02d:%02d', $hour, $minute);
                                                                    @endphp
                                                                    <option value="{{ $time }}" {{ $existingSchedule && $existingSchedule->start_time && $existingSchedule->start_time->format('H:i') == $time ? 'selected' : '' }}>{{ $time }}</option>
                                                                @endfor
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <label for="edit_end_time_{{ $dayNum }}" class="form-label">End Time</label>
                                                        <select class="form-select time-select"
                                                                id="edit_end_time_{{ $dayNum }}"
                                                                name="days[{{ $dayNum }}][end_time]">
                                                            <option value="">Select End Time</option>
                                                            @for ($hour = 0; $hour < 24; $hour++)
                                                                @for ($minute = 0; $minute < 60; $minute += 15)
                                                                    @php
                                                                        $time = sprintf('%02d:%02d', $hour, $minute);
                                                                    @endphp
                                                                    <option value="{{ $time }}" {{ $existingSchedule && $existingSchedule->end_time && $existingSchedule->end_time->format('H:i') == $time ? 'selected' : '' }}>{{ $time }}</option>
                                                                @endfor
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                                @if ($existingSchedule)
                                                    <div class="mt-2">
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-schedule-btn"
                                                                data-schedule-id="{{ $existingSchedule->id }}"
                                                                data-day-name="{{ $dayName }}">
                                                            <i class="fas fa-trash me-1"></i>Delete this schedule
                                                        </button>
                                                    </div>
                                                @endif
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
                                <strong>Note:</strong> Only checked days will have schedules created/updated. End time must be after start time for each working day.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-secondary me-2" id="formCancelBtn">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-admin-primary">
                            <i class="fas fa-save me-1"></i>Update Weekly Schedule
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
function toggleEditMode() {
    const viewMode = document.getElementById('viewMode');
    const editMode = document.getElementById('editMode');

    if (viewMode.style.display === 'none') {
        viewMode.style.display = 'block';
        editMode.style.display = 'none';
    } else {
        viewMode.style.display = 'none';
        editMode.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Attach event listeners to buttons
    const editBtn = document.getElementById('editSchedulesBtn');
    if (editBtn) {
        editBtn.addEventListener('click', toggleEditMode);
    }

    const cancelBtn = document.getElementById('cancelEditBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', toggleEditMode);
    }

    // Attach event listeners to delete buttons
    document.querySelectorAll('.delete-schedule-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const scheduleId = this.getAttribute('data-schedule-id');
            const dayName = this.getAttribute('data-day-name');
            deleteSchedule(scheduleId, dayName);
        });
    });

    // Attach event listener to form cancel button
    const formCancelBtn = document.getElementById('formCancelBtn');
    if (formCancelBtn) {
        formCancelBtn.addEventListener('click', toggleEditMode);
    }

function deleteSchedule(scheduleId, dayName) {
    if (confirm(`Are you sure you want to delete the schedule for ${dayName}?`)) {
        // Create a form to submit delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin-portal/schedules/${scheduleId}`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Handle day checkbox changes in edit mode
    document.querySelectorAll('#editMode .day-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const dayNum = this.id.split('_')[2];
            const scheduleDiv = document.getElementById('edit_schedule_' + dayNum);
            const startSelect = document.getElementById('edit_start_time_' + dayNum);
            const endSelect = document.getElementById('edit_end_time_' + dayNum);

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

    // Copy times to all days in edit mode
    document.getElementById('editCopyToAllBtn').addEventListener('click', function() {
        const startTime = document.getElementById('edit_copy_start_time').value;
        const endTime = document.getElementById('edit_copy_end_time').value;

        if (!startTime || !endTime) {
            alert('Please select both start and end times to copy.');
            return;
        }

        // Copy to all checked days
        document.querySelectorAll('#editMode .day-checkbox:checked').forEach(function(checkbox) {
            const dayNum = checkbox.id.split('_')[2];
            document.getElementById('edit_start_time_' + dayNum).value = startTime;
            document.getElementById('edit_end_time_' + dayNum).value = endTime;
        });

        alert('Times copied to all working days.');
    });

    // Clear all times in edit mode
    document.getElementById('editClearAllBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to clear all schedule times?')) {
            document.querySelectorAll('#editMode .time-select').forEach(function(select) {
                select.value = '';
            });
            document.getElementById('edit_copy_start_time').value = '';
            document.getElementById('edit_copy_end_time').value = '';
        }
    });

    // Form validation
    document.getElementById('editScheduleForm').addEventListener('submit', function(e) {
        let hasErrors = false;
        let errorMessages = [];

        document.querySelectorAll('#editMode .day-checkbox:checked').forEach(function(checkbox) {
            const dayNum = checkbox.id.split('_')[2];
            const startTime = document.getElementById('edit_start_time_' + dayNum).value;
            const endTime = document.getElementById('edit_end_time_' + dayNum).value;
            const dayName = document.querySelector(`label[for="edit_day_${dayNum}"]`).textContent.trim();

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
