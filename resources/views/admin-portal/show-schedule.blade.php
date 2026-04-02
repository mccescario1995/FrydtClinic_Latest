@extends('admin-portal.layouts.app')

@section('title', 'Employee Schedule Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0"><i class="fas fa-calendar-check me-2"></i>Schedule Details</h1>
                <p class="text-muted mb-0">{{ $schedule->employee->name }} - {{ $schedule->dayName }}</p>
            </div>
            <div>
                <a href="{{ route('admin-portal.schedules.edit', $schedule->id) }}" class="btn btn-admin-primary me-2">
                    <i class="fas fa-edit me-1"></i>Edit Schedule
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
        <!-- Schedule Details -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Schedule Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Employee</label>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    <img src="{{ $schedule->employee->employeeProfile->avatar ?? 'https://via.placeholder.com/40x40?text=' . substr($schedule->employee->name, 0, 1) }}" alt="Avatar" class="rounded-circle">
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $schedule->employee->name }}</div>
                                    <small class="text-muted">{{ $schedule->employee->email }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Day of Week</label>
                            <div>
                                <span class="badge bg-primary fs-6">{{ $schedule->dayName }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Start Time</label>
                            <div class="h5 text-success">
                                <i class="fas fa-clock me-2"></i>
                                {{ $schedule->start_time ? $schedule->start_time->format('H:i') : 'N/A' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">End Time</label>
                            <div class="h5 text-danger">
                                <i class="fas fa-clock me-2"></i>
                                {{ $schedule->end_time ? $schedule->end_time->format('H:i') : 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Duration</label>
                            <div class="h5 text-info">
                                <i class="fas fa-hourglass-half me-2"></i>
                                @if($schedule->start_time && $schedule->end_time)
                                    {{ $schedule->start_time->diff($schedule->end_time)->format('%Hh %Im') }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Created</label>
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-plus me-1"></i>
                                    {{ $schedule->created_at->format('M d, Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                @if($schedule->updated_at != $schedule->created_at)
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-0">
                                <label class="form-label fw-bold">Last Updated</label>
                                <div>
                                    <small class="text-muted">
                                        <i class="fas fa-edit me-1"></i>
                                        {{ $schedule->updated_at->format('M d, Y H:i') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Employee's Other Schedules -->
        @if($otherSchedules->count() > 0)
            <div class="admin-card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-calendar-week me-2"></i>{{ $schedule->employee->name }}'s Other Schedules</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Duration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($otherSchedules as $otherSchedule)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">{{ $otherSchedule->dayName }}</span>
                                        </td>
                                        <td>{{ $otherSchedule->start_time ? $otherSchedule->start_time->format('H:i') : 'N/A' }}</td>
                                        <td>{{ $otherSchedule->end_time ? $otherSchedule->end_time->format('H:i') : 'N/A' }}</td>
                                        <td>
                                            @if($otherSchedule->start_time && $otherSchedule->end_time)
                                                {{ $otherSchedule->start_time->diff($otherSchedule->end_time)->format('%Hh %Im') }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin-portal.schedules.show', $otherSchedule->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="admin-card mb-4">
            <div class="card-header bg-warning text-white">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin-portal.schedules.edit', $schedule->id) }}" class="btn btn-admin-primary">
                        <i class="fas fa-edit me-1"></i>Edit Schedule
                    </a>
                    <a href="{{ route('admin-portal.schedules.create') }}" class="btn btn-outline-success">
                        <i class="fas fa-plus me-1"></i>Add New Schedule
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

        <!-- Schedule Statistics -->
        <div class="admin-card mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Schedule Stats</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Total Schedules:</strong><br>
                    <span class="h5 text-info">{{ \App\Models\EmployeeSchedule::where('employee_id', $schedule->employee_id)->count() }}</span>
                </div>
                <div class="mb-3">
                    <strong>Weekly Hours:</strong><br>
                    @php
                        $weeklyHours = \App\Models\EmployeeSchedule::where('employee_id', $schedule->employee_id)
                            ->get()
                            ->sum(function($s) {
                                if ($s->start_time && $s->end_time) {
                                    return $s->start_time->diffInHours($s->end_time);
                                }
                                return 0;
                            });
                    @endphp
                    <span class="h5 text-success">{{ $weeklyHours }}h</span>
                </div>
                <div class="mb-0">
                    <strong>Work Days:</strong><br>
                    <span class="h5 text-primary">{{ \App\Models\EmployeeSchedule::where('employee_id', $schedule->employee_id)->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="admin-card">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activity</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item mb-3">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <small class="text-muted">{{ $schedule->created_at->format('M d, Y H:i') }}</small>
                            <div>Schedule created</div>
                        </div>
                    </div>
                    @if($schedule->updated_at != $schedule->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <small class="text-muted">{{ $schedule->updated_at->format('M d, Y H:i') }}</small>
                                <div>Schedule updated</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
