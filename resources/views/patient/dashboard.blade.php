@extends('patient.layouts.app')

@section('title', 'Patient Dashboard - FRYDT')

@section('content')
<!-- Welcome Section -->
<div class="welcome-section">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="mb-2">
                <i class="fas fa-hand-wave me-2"></i>Welcome back, {{ $user->name }}!
            </h1>
            <p class="mb-0 opacity-90">
                Manage your appointments and health records from your personal dashboard.
            </p>
        </div>
        <div class="col-md-4 text-end">
            <div class="d-flex align-items-center justify-content-end">
                <div class="me-3">
                    <small class="opacity-75">Today is</small><br>
                    <strong>{{ now()->format('l, F j, Y') }}</strong>
                </div>
                <i class="fas fa-calendar-day fa-2x opacity-75"></i>
            </div>
        </div>
    </div>
</div>

<!-- Payment Approval Notifications -->
@if($recentlyApprovedPayments->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-2x me-3"></i>
                <div>
                    <h5 class="alert-heading mb-1">Payment Approved!</h5>
                    <p class="mb-2">
                        @if($recentlyApprovedPayments->count() === 1)
                            Your payment of {{ $recentlyApprovedPayments->first()->formatted_amount }} has been approved and confirmed.
                        @else
                            {{ $recentlyApprovedPayments->count() }} of your payments have been recently approved.
                        @endif
                    </p>
                    <a href="{{ route('patient.payments.index') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-eye me-1"></i>View Payments
                    </a>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-body stat-card">
                <div class="stat-number text-primary">{{ $totalAppointments }}</div>
                <div class="stat-label">Total Appointments</div>
                <i class="fas fa-calendar-alt fa-2x text-primary opacity-25 mt-2"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-body stat-card">
                <div class="stat-number text-success">{{ $completedAppointments }}</div>
                <div class="stat-label">Completed</div>
                <i class="fas fa-check-circle fa-2x text-success opacity-25 mt-2"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-body stat-card">
                <div class="stat-number text-info">{{ $upcomingAppointments->count() }}</div>
                <div class="stat-label">Upcoming</div>
                <i class="fas fa-clock fa-2x text-info opacity-25 mt-2"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-body stat-card">
                <div class="stat-number text-warning">{{ $cancelledAppointments }}</div>
                <div class="stat-label">Cancelled</div>
                <i class="fas fa-times-circle fa-2x text-warning opacity-25 mt-2"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Upcoming Appointments -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-check me-2"></i>Upcoming Appointments
                </h5>
                <a href="{{ route('patient.book-appointment') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus me-1"></i>Book New
                </a>
            </div>
            <div class="card-body">
                @if($upcomingAppointments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Service</th>
                                    <th>Doctor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingAppointments as $appointment)
                                <tr>
                                    <td>
                                        <strong>{{ $appointment->appointment_datetime->format('M j, Y') }}</strong><br>
                                        <small class="text-muted">{{ $appointment->appointment_datetime->format('g:i A') }}</small>
                                    </td>
                                    <td>
                                        <i class="fas fa-stethoscope me-1 text-primary"></i>
                                        {{ $appointment->service->name }}
                                    </td>
                                    <td>{{ $appointment->employee->name }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($appointment->status) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('patient.appointments') }}" class="btn btn-outline-primary">
                            View All Appointments
                        </a>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No upcoming appointments</h6>
                        <a href="{{ route('patient.book-appointment') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-plus me-1"></i>Book Your First Appointment
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions & Profile Summary -->
    <div class="col-lg-4 mb-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('patient.book-appointment') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-plus me-2"></i>Book Appointment
                    </a>
                    <a href="{{ route('patient.appointments') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>View Appointments
                    </a>
                    <a href="{{ route('patient.profile') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-user-edit me-2"></i>Update Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Profile Summary -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>Profile Summary
                </h5>
            </div>
            <div class="card-body">
                @if($patientProfile)
                    <div class="text-center mb-3">
                        @if($patientProfile->image_path)
                            <img src="{{ asset('storage/app/public/' . $patientProfile->image_path) }}"
                                 alt="Profile Image" class="rounded-circle mb-2"
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2"
                                 style="width: 80px; height: 80px; font-weight: bold; font-size: 24px;">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Name</small>
                        <strong>{{ $user->name }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Email</small>
                        <strong>{{ $user->email }}</strong>
                    </div>

                    <!-- Personal Information -->
                    @if($patientProfile->phone)
                    <div class="mb-3">
                        <small class="text-muted d-block">Phone</small>
                        <strong>{{ $patientProfile->phone }}</strong>
                    </div>
                    @endif

                    @if($patientProfile->birth_date)
                    <div class="mb-3">
                        <small class="text-muted d-block">Age</small>
                        <strong>{{ \Carbon\Carbon::parse($patientProfile->birth_date)->age }} years old</strong>
                    </div>
                    @endif

                    @if($patientProfile->gender)
                    <div class="mb-3">
                        <small class="text-muted d-block">Gender</small>
                        <strong>{{ ucfirst($patientProfile->gender) }}</strong>
                    </div>
                    @endif

                    @if($patientProfile->civil_status)
                    <div class="mb-3">
                        <small class="text-muted d-block">Civil Status</small>
                        <strong>{{ ucfirst($patientProfile->civil_status) }}</strong>
                    </div>
                    @endif

                    <!-- Medical Information -->
                    @if($patientProfile->blood_type)
                    <div class="mb-3">
                        <small class="text-muted d-block">Blood Type</small>
                        <strong class="text-danger">{{ $patientProfile->blood_type }}</strong>
                    </div>
                    @endif

                    @if($patientProfile->occupation)
                    <div class="mb-3">
                        <small class="text-muted d-block">Occupation</small>
                        <strong>{{ $patientProfile->occupation }}</strong>
                    </div>
                    @endif

                    <!-- PhilHealth Information -->
                    @if($patientProfile->philhealth_membership)
                    <div class="mb-3">
                        <small class="text-muted d-block">PhilHealth</small>
                        <strong class="text-success">{{ ucfirst(str_replace('_', ' ', $patientProfile->philhealth_membership)) }}</strong>
                        @if($patientProfile->philhealth_number)
                        <br><small class="text-muted">{{ $patientProfile->philhealth_number }}</small>
                        @endif
                    </div>
                    @endif

                    <!-- Emergency Contact -->
                    @if($patientProfile->emergency_contact_name)
                    <div class="mb-3">
                        <small class="text-muted d-block">Emergency Contact</small>
                        <strong>{{ $patientProfile->emergency_contact_name }}</strong>
                        @if($patientProfile->emergency_contact_phone)
                        <br><small class="text-muted">{{ $patientProfile->emergency_contact_phone }}</small>
                        @endif
                        @if($patientProfile->emergency_contact_relationship)
                        <br><small class="text-muted">{{ $patientProfile->emergency_contact_relationship }}</small>
                        @endif
                    </div>
                    @endif
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-user-plus fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-2">Complete your profile for better service</p>
                        <a href="{{ route('patient.profile') }}" class="btn btn-primary btn-sm">
                            Complete Profile
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Appointment History -->
@if($recentAppointments->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Recent Appointment History
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Service</th>
                                <th>Doctor/Staff</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentAppointments as $appointment)
                            <tr>
                                <td>{{ $appointment->appointment_datetime->format('M j, Y') }}</td>
                                <td>{{ $appointment->service->name }}</td>
                                <td>{{ $appointment->employee->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $appointment->status === 'completed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'info') }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
