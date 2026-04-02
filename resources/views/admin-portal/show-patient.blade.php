@extends('admin-portal.layouts.app')

@section('title', 'Patient Details - ' . $patient->name)

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-user-injured me-2"></i>{{ $patient->name }}
    </h1>
    <p class="page-subtitle">Patient ID: {{ $patient->id }} • Status: <span class="status-badge bg-{{ $patient->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($patient->status) }}</span></p>
</div>

<!-- Action Buttons -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin-portal.patients') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Patients
        </a>
    </div>
    <div>
        <a href="{{ route('admin-portal.patients.edit', $patient->id) }}" class="btn btn-admin-primary me-2">
            <i class="fas fa-edit me-1"></i>Edit Patient
        </a>
        <form method="POST" action="{{ route('admin-portal.patients.delete', $patient->id) }}" class="d-inline"
              onsubmit="return confirm('Are you sure you want to delete this patient? This action cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">
                <i class="fas fa-trash me-1"></i>Delete Patient
            </button>
        </form>
    </div>
</div>

<div class="row">
    <!-- Patient Information -->
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Patient Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <p class="mb-0">{{ $patient->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <p class="mb-0">{{ $patient->email }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone</label>
                            <p class="mb-0">{{ $patient->patientProfile->phone ?? 'Not provided' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Date of Birth</label>
                            <p class="mb-0">
                                @if($patient->patientProfile && $patient->patientProfile->birth_date)
                                    {{ \Carbon\Carbon::parse($patient->patientProfile->birth_date)->format('F d, Y') }}
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($patient->patientProfile->birth_date)->age }} years old</small>
                                @else
                                    Not provided
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Gender</label>
                            <p class="mb-0">{{ $patient->patientProfile->gender ? ucfirst($patient->patientProfile->gender) : 'Not specified' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Civil Status</label>
                            <p class="mb-0">{{ $patient->patientProfile->civil_status ? ucfirst($patient->patientProfile->civil_status) : 'Not specified' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Address</label>
                    <p class="mb-0">{{ $patient->patientProfile->address ?? 'Not provided' }}</p>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Emergency Contact</label>
                            <p class="mb-0">{{ $patient->patientProfile->emergency_contact_name ?? 'Not provided' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Contact Phone</label>
                            <p class="mb-0">{{ $patient->patientProfile->emergency_contact_phone ?? 'Not provided' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Relationship</label>
                            <p class="mb-0">{{ $patient->patientProfile->emergency_contact_relationship ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>

                <!-- PhilHealth Information -->
                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0"><i class="fas fa-id-card me-2"></i>PhilHealth Information</h6>
                        <a href="{{ route('admin-portal.patients.philhealth.edit', $patient->id) }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-edit me-1"></i>Edit PhilHealth
                        </a>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Membership Status</label>
                                <p class="mb-0">
                                    @if($patient->patientProfile->philhealth_membership === 'yes')
                                        <span class="badge bg-success">Member</span>
                                    @elseif($patient->patientProfile->philhealth_membership === 'no')
                                        <span class="badge bg-secondary">Not a Member</span>
                                    @else
                                        <span class="badge bg-warning">Not Specified</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">PhilHealth Number</label>
                                <p class="mb-0">{{ $patient->patientProfile->philhealth_number ?: 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Appointments -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Recent Appointments</h5>
            </div>
            <div class="card-body">
                @if($appointments->count() > 0)
                    <div class="table-responsive">
                        <table class="admin-table table">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Service</th>
                                    <th>Employee</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $appointment)
                                    <tr>
                                        <td>
                                            <div>
                                                <div class="fw-bold">{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('h:i A') }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $appointment->service->name ?? 'N/A' }}</td>
                                        <td>{{ $appointment->employee->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="status-badge bg-{{ match($appointment->status) {
                                                'scheduled' => 'warning',
                                                'confirmed' => 'info',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                                default => 'secondary'
                                            } }}">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <p class="empty-text">No appointments found for this patient.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Account Information -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-user-circle me-2"></i>Account Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Account Status</label>
                    <p class="mb-0">
                        <span class="status-badge bg-{{ $patient->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($patient->status) }}
                        </span>
                    </p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">User Type</label>
                    <p class="mb-0">{{ ucfirst($patient->user_type) }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Registration Date</label>
                    <p class="mb-0">{{ \Carbon\Carbon::parse($patient->created_at)->format('F d, Y') }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Last Updated</label>
                    <p class="mb-0">{{ \Carbon\Carbon::parse($patient->updated_at)->format('F d, Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Medical Records Management -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-file-medical me-2"></i>Medical Records Management</h6>
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col-6 col-md-3">
                        <div class="mb-3">
                            <div class="h4 mb-0 text-primary">{{ $prenatalRecords->count() }}</div>
                            <small class="text-muted">Prenatal</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="mb-3">
                            <div class="h4 mb-0 text-success">{{ $postnatalRecords->count() }}</div>
                            <small class="text-muted">Postnatal</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="mb-3">
                            <div class="h4 mb-0 text-warning">{{ $postpartumRecords->count() }}</div>
                            <small class="text-muted">Postpartum</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="mb-3">
                            <div class="h4 mb-0 text-danger">{{ $deliveryRecords->count() }}</div>
                            <small class="text-muted">Delivery</small>
                        </div>
                    </div>
                </div>

                <!-- Medical Records Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-grid gap-2">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <a href="{{ route('admin-portal.medical-records.prenatal') . '?patient_id=' . $patient->id }}" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-baby me-1"></i>Manage Prenatal Records
                                    </a>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <a href="{{ route('admin-portal.medical-records.postnatal') . '?patient_id=' . $patient->id }}" class="btn btn-outline-success btn-sm w-100">
                                        <i class="fas fa-child me-1"></i>Manage Postnatal Records
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <a href="{{ route('admin-portal.medical-records.postpartum') . '?patient_id=' . $patient->id }}" class="btn btn-outline-warning btn-sm w-100">
                                        <i class="fas fa-female me-1"></i>Manage Postpartum Records
                                    </a>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <a href="{{ route('admin-portal.medical-records.delivery') . '?patient_id=' . $patient->id }}" class="btn btn-outline-danger btn-sm w-100">
                                        <i class="fas fa-hospital me-1"></i>Manage Delivery Records
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <a href="{{ route('admin-portal.medical-records.lab-results') . '?patient_id=' . $patient->id }}" class="btn btn-outline-info btn-sm w-100">
                                        <i class="fas fa-flask me-1"></i>Manage Lab Results
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Information</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <small class="text-muted">
                        For detailed medical record management and appointment scheduling,<br>
                        please use the <strong>Employee Portal</strong>.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
