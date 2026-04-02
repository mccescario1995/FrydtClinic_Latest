@extends('employee.layouts.app')

@section('title', 'My Attendance Records')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card employee-card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>My Attendance Records
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="text-primary">{{ $totalDays }}</h5>
                                    <small class="text-muted">Total Working Days</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success">
                                <div class="card-body text-center text-white">
                                    <h5>{{ $presentDays }}</h5>
                                    <small>Days Present</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning">
                                <div class="card-body text-center text-white">
                                    <h5>{{ $lateDays }}</h5>
                                    <small>Late Arrivals</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info">
                                <div class="card-body text-center text-white">
                                    <h5>{{ number_format($totalHours, 1) }}h</h5>
                                    <small>Total Hours</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @if($attendanceRecords->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped employee-table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-calendar me-1"></i>Date</th>
                                        <th><i class="fas fa-sign-in-alt me-1"></i>Time In</th>
                                        <th><i class="fas fa-sign-out-alt me-1"></i>Time Out</th>
                                        <th><i class="fas fa-clock me-1"></i>Hours Worked</th>
                                        <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                        <th><i class="fas fa-sticky-note me-1"></i>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendanceRecords as $record)
                                        <tr>
                                            <td>
                                                <strong>{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($record->date)->format('l') }}</small>
                                            </td>
                                            <td>
                                                @if($record->clock_in)
                                                    <span class="text-success">
                                                        <i class="fas fa-check-circle me-1"></i>
                                                        {{ \Carbon\Carbon::parse($record->clock_in)->format('h:i A') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($record->clock_out)
                                                    <span class="text-primary">
                                                        <i class="fas fa-check-circle me-1"></i>
                                                        {{ \Carbon\Carbon::parse($record->clock_out)->format('h:i A') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($record->clock_in && $record->clock_out)
                                                    @php
                                                        $start = \Carbon\Carbon::parse($record->clock_in);
                                                        $end = \Carbon\Carbon::parse($record->clock_out);
                                                        $totalMinutes = $start->diffInMinutes($end);
                                                        $hours = floor($totalMinutes / 60);
                                                        $minutes = $totalMinutes % 60;
                                                    @endphp
                                                    <span class="badge bg-info">
                                                        {{ $hours }}h {{ $minutes }}m
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($record->clock_in && $record->clock_out)
                                                    <span class="status-badge status-active">Complete</span>
                                                @elseif($record->clock_in)
                                                    <span class="status-badge status-inactive">Clocked In</span>
                                                @else
                                                    <span class="badge bg-secondary">Not Started</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($record->notes)
                                                    <small class="text-muted">{{ $record->notes }}</small>
                                                @else
                                                    <small class="text-muted">No notes</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $attendanceRecords->appends(request()->query())->links('vendor.pagination.admin-portal') }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-calendar-times fa-3x text-muted"></i>
                            </div>
                            <h4 class="text-muted">No Attendance Records</h4>
                            <p class="text-muted">Your attendance records will appear here once you start logging your work hours.</p>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Note: Attendance is managed by your administrator. Contact them if you need assistance.
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
