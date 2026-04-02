@extends('admin-portal.layouts.app')

@section('title', 'Edit Employee Schedule')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0"><i class="fas fa-calendar-edit me-2"></i>Edit Schedule</h1>
                <p class="text-muted mb-0">Update work schedule for {{ $schedule->employee->name }}</p>
            </div>
            <div>
                <a href="{{ route('admin-portal.schedules.show', $schedule->id) }}" class="btn btn-outline-info me-2">
                    <i class="fas fa-eye me-1"></i>View Schedule
                </a>
                <a href="{{ route('admin-portal.schedules') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Schedules
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-calendar-edit me-2"></i>Schedule Details</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin-portal.schedules.update', $schedule->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="employee_id" class="form-label">Employee <span class="text-danger">*</span></label>
                            <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ (old('employee_id', $schedule->employeeProfile->employee_id) == $employee->id) ? 'selected' : '' }}>
                                        {{ $employee->name }} ({{ $employee->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="day_of_week" class="form-label">Day of Week <span class="text-danger">*</span></label>
                            <select class="form-select @error('day_of_week') is-invalid @enderror" id="day_of_week" name="day_of_week" required>
                                <option value="">Select Day</option>
                                <option value="1" {{ old('day_of_week', $schedule->day_of_week) == '1' ? 'selected' : '' }}>Monday</option>
                                <option value="2" {{ old('day_of_week', $schedule->day_of_week) == '2' ? 'selected' : '' }}>Tuesday</option>
                                <option value="3" {{ old('day_of_week', $schedule->day_of_week) == '3' ? 'selected' : '' }}>Wednesday</option>
                                <option value="4" {{ old('day_of_week', $schedule->day_of_week) == '4' ? 'selected' : '' }}>Thursday</option>
                                <option value="5" {{ old('day_of_week', $schedule->day_of_week) == '5' ? 'selected' : '' }}>Friday</option>
                                <option value="6" {{ old('day_of_week', $schedule->day_of_week) == '6' ? 'selected' : '' }}>Saturday</option>
                                <option value="7" {{ old('day_of_week', $schedule->day_of_week) == '7' ? 'selected' : '' }}>Sunday</option>
                            </select>
                            @error('day_of_week')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                   id="start_time" name="start_time" value="{{ old('start_time', $schedule->start_time ? $schedule->start_time->format('H:i') : '') }}" required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                   id="end_time" name="end_time" value="{{ old('end_time', $schedule->end_time ? $schedule->end_time->format('H:i') : '') }}" required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> End time must be after start time. The system will validate this automatically.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin-portal.schedules.show', $schedule->id) }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-admin-primary">
                            <i class="fas fa-save me-1"></i>Update Schedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Current Schedule Info -->
        <div class="admin-card mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Current Schedule</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Employee:</strong><br>
                    {{ $schedule->employee->name }}
                </div>
                <div class="mb-3">
                    <strong>Day:</strong><br>
                    <span class="badge bg-primary">{{ $schedule->dayName }}</span>
                </div>
                <div class="mb-3">
                    <strong>Time:</strong><br>
                    {{ $schedule->start_time ? $schedule->start_time->format('H:i') : 'N/A' }} -
                    {{ $schedule->end_time ? $schedule->end_time->format('H:i') : 'N/A' }}
                </div>
                <div class="mb-0">
                    <strong>Duration:</strong><br>
                    @if($schedule->start_time && $schedule->end_time)
                        {{ $schedule->start_time->diff($schedule->end_time)->format('%Hh %Im') }}
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="admin-card mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-question-circle me-2"></i>Help & Tips</h6>
            </div>
            <div class="card-body">
                <h6>Schedule Guidelines:</h6>
                <ul class="mb-0 small">
                    <li>Each employee can have only one schedule per day</li>
                    <li>Start time must be before end time</li>
                    <li>Use 24-hour format for times (e.g., 08:00 for 8 AM)</li>
                    <li>Consider employee availability and clinic hours</li>
                </ul>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="admin-card">
            <div class="card-header bg-warning text-white">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin-portal.schedules.show', $schedule->id) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye me-1"></i>View Details
                    </a>
                    <form method="POST" action="{{ route('admin-portal.schedules.delete', $schedule->id) }}" onsubmit="return confirm('Are you sure you want to delete this schedule?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-trash me-1"></i>Delete Schedule
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
