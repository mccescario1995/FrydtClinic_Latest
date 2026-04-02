@extends('admin-portal.layouts.app')

@section('title', 'User Details - ' . $user->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0"><i class="fas fa-user me-2"></i>{{ $user->name }}</h1>
                <p class="text-muted mb-0">User details and information</p>
            </div>
            <div>
                <a href="{{ route('admin-portal.users') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Users
                </a>
            </div>
        </div>
    </div>
</div>

<!-- User Profile Card -->
<div class="row mb-4">
    <div class="col-lg-4 mb-4">
        <div class="admin-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Profile Information</h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    @if($user->user_type === 'patient' && $user->patientProfile && $user->patientProfile->image_path)
                        <img src="{{ asset('storage/' . $user->patientProfile->image_path) }}"
                             alt="Profile Image" class="rounded-circle mx-auto"
                             style="width: 80px; height: 80px; object-fit: cover;">
                    @elseif($user->user_type === 'employee' && $user->employeeProfile && $user->employeeProfile->image_path)
                        <img src="{{ asset('storage/' . $user->employeeProfile->image_path) }}"
                             alt="Profile Image" class="rounded-circle mx-auto"
                             style="width: 80px; height: 80px; object-fit: cover;">
                    @else
                        <div class="bg-{{ $user->user_type === 'patient' ? 'success' : ($user->user_type === 'employee' ? 'info' : 'warning') }} text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                             style="width: 80px; height: 80px; font-weight: bold; font-size: 24px;">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <h5 class="mb-1">{{ $user->name }}</h5>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                <span class="badge bg-{{ $user->user_type === 'patient' ? 'success' : ($user->user_type === 'employee' ? 'info' : 'warning') }}">
                    <i class="fas fa-{{ $user->user_type === 'patient' ? 'user-injured' : ($user->user_type === 'employee' ? 'user-md' : 'user-shield') }} me-1"></i>
                    {{ ucfirst($user->user_type) }}
                </span>
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        Joined {{ $user->created_at->format('M d, Y') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- User Details -->
        <div class="admin-card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Account Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <p class="mb-0">{{ $user->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <p class="mb-0">{{ $user->email }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">User Type</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $user->user_type === 'patient' ? 'success' : ($user->user_type === 'employee' ? 'info' : 'warning') }}">
                                    {{ ucfirst($user->user_type) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Registration Status</label>
                            <p class="mb-0">{{ $user->registration_status ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Last Updated</label>
                            <p class="mb-0">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role/Type Specific Information -->
        @if($user->user_type === 'patient' && $user->patientProfile)
            <div class="admin-card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user-injured me-2"></i>Patient Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Phone</label>
                                <p class="mb-0">{{ $user->patientProfile->phone ?? 'Not provided' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Date of Birth</label>
                                <p class="mb-0">
                                    @if($user->patientProfile->birth_date)
                                        {{ \Carbon\Carbon::parse($user->patientProfile->birth_date)->format('M d, Y') }}
                                        <small class="text-muted">({{ \Carbon\Carbon::parse($user->patientProfile->birth_date)->age }} years old)</small>
                                    @else
                                        Not provided
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Gender</label>
                                <p class="mb-0">{{ $user->patientProfile->gender ? ucfirst($user->patientProfile->gender) : 'Not specified' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Civil Status</label>
                                <p class="mb-0">{{ $user->patientProfile->civil_status ? ucfirst($user->patientProfile->civil_status) : 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Address</label>
                        <p class="mb-0">{{ $user->patientProfile->address ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>
        @elseif($user->user_type === 'employee' && $user->employeeProfile)
            <div class="admin-card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user-md me-2"></i>Employee Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Employee Number</label>
                                <p class="mb-0">{{ $user->employeeProfile->employee_id ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Position</label>
                                <p class="mb-0">{{ $user->employeeProfile->position ?? 'Not specified' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Specialty</label>
                                <p class="mb-0">{{ $user->employeeProfile->specialty ?? 'Not specified' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Hire Date</label>
                                <p class="mb-0">
                                    @if($user->employeeProfile->hire_date)
                                        {{ \Carbon\Carbon::parse($user->employeeProfile->hire_date)->format('M d, Y') }}
                                    @else
                                        Not specified
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Phone</label>
                                <p class="mb-0">{{ $user->employeeProfile->phone ?? 'Not provided' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Gender</label>
                                <p class="mb-0">{{ $user->employeeProfile->gender ? ucfirst($user->employeeProfile->gender) : 'Not specified' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Employment Type</label>
                                <p class="mb-0">{{ $user->employeeProfile->employment_type ? ucfirst(str_replace('_', ' ', $user->employeeProfile->employment_type)) : 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Recent Activity -->
<div class="admin-card">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activity</h5>
    </div>
    <div class="card-body">
        @if($recentActivity->count() > 0)
            <div class="timeline">
                @foreach($recentActivity as $activity)
                    <div class="timeline-item mb-3">
                        <div class="timeline-marker bg-{{ match($activity->action) {
                            'login' => 'success',
                            'logout' => 'secondary',
                            'create' => 'primary',
                            'update' => 'info',
                            'delete' => 'danger',
                            'view' => 'warning',
                            default => 'secondary'
                        } }}"></div>
                        <div class="timeline-content">
                            <small class="text-muted">{{ $activity->created_at->format('M d, Y H:i') }}</small>
                            <div>
                                <strong>{{ ucfirst($activity->action) }}</strong>
                                @if($activity->description)
                                    - {{ $activity->description }}
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-history fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">No recent activity found for this user.</p>
            </div>
        @endif
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-left: 15px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 8px 12px;
    border-radius: 5px;
    font-size: 0.9rem;
}

.timeline-content small {
    display: block;
    margin-bottom: 2px;
}
</style>
@endsection
