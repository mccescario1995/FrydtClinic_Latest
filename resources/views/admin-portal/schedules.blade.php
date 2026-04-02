@extends('admin-portal.layouts.app')

@section('title', 'Employee Schedules')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0"><i class="fas fa-calendar-alt me-2"></i>Employee Schedules</h1>
                    <p class="text-muted mb-0">Select an employee to manage their work schedules</p>
                </div>
                <div>
                    <button type="button" class="btn btn-admin-primary" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
                        <i class="fas fa-plus me-1"></i>Create New Schedule
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid mb-4">
        <div class="stats-card">
            <div class="card-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="card-title">Total Schedules</div>
            <div class="card-value">{{ $totalSchedules }}</div>
        </div>
        <div class="stats-card">
            <div class="card-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-title">Employees with Schedules</div>
            <div class="card-value">{{ $uniqueEmployees }}</div>
        </div>
        <div class="stats-card">
            <div class="card-icon">
                <i class="fas fa-user-clock"></i>
            </div>
            <div class="card-title">Filtered Employees</div>
            <div class="card-value">{{ $employees->total() }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" action="{{ route('admin-portal.schedules') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search Employee</label>
                <input type="text" class="form-control" id="search" name="search"
                       value="{{ request('search') }}"
                       placeholder="Search by name or email...">
            </div>
            <div class="col-md-3">
                <label for="position" class="form-label">Position</label>
                <select class="form-select" id="position" name="position">
                    <option value="">All Positions</option>
                    @php
                        $positions = \App\Models\User::where('user_type', 'employee')
                            ->join('employee_profiles', 'users.id', '=', 'employee_profiles.employee_id')
                            ->whereNotNull('employee_profiles.position')
                            ->distinct()
                            ->pluck('employee_profiles.position')
                            ->filter()
                            ->sort()
                            ->values();
                    @endphp
                    @foreach($positions as $pos)
                        <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>
                            {{ $pos }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="schedule_status" class="form-label">Schedule Status</label>
                <select class="form-select" id="schedule_status" name="schedule_status">
                    <option value="">All Employees</option>
                    <option value="with_schedules" {{ request('schedule_status') == 'with_schedules' ? 'selected' : '' }}>
                        Has Schedules
                    </option>
                    <option value="without_schedules" {{ request('schedule_status') == 'without_schedules' ? 'selected' : '' }}>
                        No Schedules
                    </option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-admin-primary me-2">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                @if(request()->hasAny(['search', 'position', 'schedule_status']))
                    <a href="{{ route('admin-portal.schedules') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Employee Selection -->
    <div class="row">
        <div class="col-12">
            <div class="admin-card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-users me-2"></i>Select Employee to Manage</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($employees as $employee)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100 employee-card" style="cursor: pointer;" onclick="window.location.href='{{ route('admin-portal.schedules.manage', $employee->id) }}'">
                                    <div class="card-body text-center">
                                        @if ($employee->employeeProfile && $employee->employeeProfile->image_path)
                                            <img src="{{ asset('storage/app/public/' . $employee->employeeProfile->image_path) }}"
                                                alt="Profile Image" class="rounded-circle mb-3"
                                                style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                                style="width: 60px; height: 60px; font-weight: bold; font-size: 24px;">
                                                {{ substr($employee->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <h6 class="card-title mb-1">{{ $employee->name }}</h6>
                                        <p class="card-text text-muted small mb-2">{{ $employee->email }}</p>
                                        <div class="mb-2">
                                            @php
                                                $scheduleCount = $employee->schedules()->count();
                                            @endphp
                                            <span class="badge bg-{{ $scheduleCount > 0 ? 'success' : 'secondary' }}">
                                                {{ $scheduleCount }} schedule{{ $scheduleCount !== 1 ? 's' : '' }}
                                            </span>
                                        </div>
                                        <small class="text-primary">Click to manage schedules</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="empty-title">No Employees Found</div>
                                    <div class="empty-text">
                                        There are no employees in the system yet.
                                    </div>
                                    <a href="{{ route('admin-portal.users.create') }}" class="btn btn-admin-primary">
                                        <i class="fas fa-plus me-1"></i>Add First Employee
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if ($employees->hasPages())
        <div class="mt-4">
            {{ $employees->appends(request()->query())->links('vendor.pagination.admin-portal') }}
        </div>
    @endif

@endsection

<!-- Create Schedule Modal -->
<div class="modal fade" id="createScheduleModal" tabindex="-1" aria-labelledby="createScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createScheduleModalLabel">
                    <i class="fas fa-plus me-2"></i>Create New Schedule
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin-portal.schedules.store') }}" id="createScheduleForm">
                @csrf
                <div class="modal-body">
                    <!-- Employee Selection -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <label for="create_employee_id" class="form-label">Select Employee <span class="text-danger">*</span></label>
                            <select class="form-select" id="create_employee_id" name="employee_id" required>
                                <option value="">Choose an employee...</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->email }})</option>
                                @endforeach
                            </select>
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
                                            <label for="create_copy_start_time" class="form-label">Start Time</label>
                                            <select class="form-select" id="create_copy_start_time">
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
                                            <label for="create_copy_end_time" class="form-label">End Time</label>
                                            <select class="form-select" id="create_copy_end_time">
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
                                            <button type="button" class="btn btn-outline-primary me-2" id="createCopyToAllBtn">
                                                <i class="fas fa-copy me-1"></i>Copy to All Days
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="createClearAllBtn">
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
                                                    <input class="form-check-input create-day-checkbox" type="checkbox"
                                                           id="create_day_{{ $dayNum }}"
                                                           name="days[{{ $dayNum }}][enabled]"
                                                           value="1">
                                                    <label class="form-check-label" for="create_day_{{ $dayNum }}">
                                                        Working
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="card-body create-day-schedule" id="create_schedule_{{ $dayNum }}" style="display: none;">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label for="create_start_time_{{ $dayNum }}" class="form-label">Start Time</label>
                                                        <select class="form-select create-time-select"
                                                                id="create_start_time_{{ $dayNum }}"
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
                                                        <label for="create_end_time_{{ $dayNum }}" class="form-label">End Time</label>
                                                        <select class="form-select create-time-select"
                                                                id="create_end_time_{{ $dayNum }}"
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-admin-primary">
                        <i class="fas fa-save me-1"></i>Create Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.employee-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.employee-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-color: #007bff;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle day checkbox changes in create modal
    document.querySelectorAll('.create-day-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const dayNum = this.id.split('_')[2];
            const scheduleDiv = document.getElementById('create_schedule_' + dayNum);
            const startSelect = document.getElementById('create_start_time_' + dayNum);
            const endSelect = document.getElementById('create_end_time_' + dayNum);

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

    // Copy times to all days in create modal
    document.getElementById('createCopyToAllBtn').addEventListener('click', function() {
        const startTime = document.getElementById('create_copy_start_time').value;
        const endTime = document.getElementById('create_copy_end_time').value;

        if (!startTime || !endTime) {
            alert('Please select both start and end times to copy.');
            return;
        }

        // Copy to all checked days
        document.querySelectorAll('.create-day-checkbox:checked').forEach(function(checkbox) {
            const dayNum = checkbox.id.split('_')[2];
            document.getElementById('create_start_time_' + dayNum).value = startTime;
            document.getElementById('create_end_time_' + dayNum).value = endTime;
        });

        alert('Times copied to all working days.');
    });

    // Clear all times in create modal
    document.getElementById('createClearAllBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to clear all schedule times?')) {
            document.querySelectorAll('.create-time-select').forEach(function(select) {
                select.value = '';
            });
            document.getElementById('create_copy_start_time').value = '';
            document.getElementById('create_copy_end_time').value = '';
        }
    });

    // Form validation for create modal
    document.getElementById('createScheduleForm').addEventListener('submit', function(e) {
        let hasErrors = false;
        let errorMessages = [];

        document.querySelectorAll('.create-day-checkbox:checked').forEach(function(checkbox) {
            const dayNum = checkbox.id.split('_')[2];
            const startTime = document.getElementById('create_start_time_' + dayNum).value;
            const endTime = document.getElementById('create_end_time_' + dayNum).value;
            const dayName = document.querySelector(`label[for="create_day_${dayNum}"]`).textContent.trim();

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

    // Reset modal when closed
    document.getElementById('createScheduleModal').addEventListener('hidden.bs.modal', function() {
        // Reset form
        document.getElementById('createScheduleForm').reset();
        // Hide all day schedules
        document.querySelectorAll('.create-day-schedule').forEach(function(div) {
            div.style.display = 'none';
        });
        // Reset required attributes
        document.querySelectorAll('.create-time-select').forEach(function(select) {
            select.required = false;
        });
    });
});
</script>
@endpush
