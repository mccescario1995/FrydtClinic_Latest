@extends('admin-portal.layouts.app')

@section('title', 'Edit Attendance - ' . $attendance->employee->name)

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-edit me-2"></i>Edit Attendance
    </h1>
    <p class="page-subtitle">Modify attendance record for {{ $attendance->employee->name }}</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="admin-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Attendance Details
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Employee:</strong> {{ $attendance->employee->name }}
                </div>
                <div class="mb-3">
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($attendance->date)->format('F d, Y') }}
                </div>

                <form method="POST" action="{{ route('admin-portal.attendance.update', $attendance->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="check_in_time" class="form-label">Check In Time</label>
                        <input type="time" class="form-control" id="check_in_time" name="check_in_time"
                               value="{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '' }}">
                        <div class="form-text">Leave empty to remove check-in time</div>
                    </div>

                    <div class="mb-3">
                        <label for="check_out_time" class="form-label">Check Out Time</label>
                        <input type="time" class="form-control" id="check_out_time" name="check_out_time"
                               value="{{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '' }}">
                        <div class="form-text">Leave empty to remove check-out time</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-admin-primary">
                            <i class="fas fa-save me-1"></i>Update Attendance
                        </button>
                        <a href="{{ route('admin-portal.attendance') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
