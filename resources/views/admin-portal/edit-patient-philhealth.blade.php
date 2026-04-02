@extends('admin-portal.layouts.app')

@section('title', 'Edit PhilHealth Information')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0"><i class="fas fa-id-card me-2"></i>Edit PhilHealth Information</h1>
                <p class="text-muted mb-0">Update PhilHealth details for {{ $patient->name }}</p>
            </div>
            <div>
                <a href="{{ route('admin-portal.patients.show', $patient->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Patient
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Patient Information Card -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Patient Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Patient Name</small>
                            <strong>{{ $patient->name }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Email</small>
                            <strong>{{ $patient->email }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Phone</small>
                            <strong>{{ $patient->patientProfile->phone ?? 'Not provided' }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Birth Date</small>
                            <strong>{{ $patient->patientProfile->birth_date ? $patient->patientProfile->birth_date->format('M d, Y') : 'Not provided' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PhilHealth Form -->
<div class="row">
    <div class="col-md-8">
        <div class="admin-card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>PhilHealth Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin-portal.patients.philhealth.update', $patient->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="philhealth_membership" class="form-label">PhilHealth Membership</label>
                            <select class="form-select" id="philhealth_membership" name="philhealth_membership">
                                <option value="">Select Membership Status</option>
                                <option value="yes" {{ $patient->patientProfile->philhealth_membership === 'yes' ? 'selected' : '' }}>Yes - Member</option>
                                <option value="no" {{ $patient->patientProfile->philhealth_membership === 'no' ? 'selected' : '' }}>No - Not a Member</option>
                            </select>
                            <div class="form-text">Select whether the patient is a PhilHealth member</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="philhealth_number" class="form-label">PhilHealth Number</label>
                            <input type="text" class="form-control" id="philhealth_number" name="philhealth_number"
                                   value="{{ $patient->patientProfile->philhealth_number }}"
                                   placeholder="XX-XXXXXXXXX-X" maxlength="20">
                            <div class="form-text">Enter the PhilHealth Identification Number (PIN) in format: XX-XXXXXXXXX-X</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-save me-1"></i>Update PhilHealth Information
                            </button>
                            <a href="{{ route('admin-portal.patients.show', $patient->id) }}" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Information Section -->
        <div class="admin-card mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>PhilHealth Information</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-lightbulb me-2"></i>PhilHealth Membership Benefits</h6>
                    <ul class="mb-0">
                        <li>Access to subsidized healthcare services</li>
                        <li>Coverage for hospitalization and outpatient care</li>
                        <li>Maternity care benefits</li>
                        <li>Annual physical examination</li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Important Notes</h6>
                    <ul class="mb-0">
                        <li>PhilHealth Number format: XX-XXXXXXXXX-X (12 digits with hyphens)</li>
                        <li>Ensure the number is entered correctly to avoid claim rejections</li>
                        <li>Non-members may still receive emergency care but won't have PhilHealth coverage</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Current PhilHealth Status -->
    <div class="col-md-4">
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Current Status</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Membership Status</small>
                    @if($patient->patientProfile->philhealth_membership === 'yes')
                        <span class="badge bg-success">Member</span>
                    @elseif($patient->patientProfile->philhealth_membership === 'no')
                        <span class="badge bg-secondary">Not a Member</span>
                    @else
                        <span class="badge bg-warning">Not Specified</span>
                    @endif
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">PhilHealth Number</small>
                    <strong>{{ $patient->patientProfile->philhealth_number ?: 'Not provided' }}</strong>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Last Updated</small>
                    <strong>{{ $patient->patientProfile->updated_at ? $patient->patientProfile->updated_at->format('M d, Y H:i') : 'Never' }}</strong>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="admin-card mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin-portal.patients.edit', $patient->id) }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-1"></i>Edit Patient Profile
                    </a>
                    <a href="{{ route('admin-portal.patients.show', $patient->id) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye me-1"></i>View Patient Details
                    </a>
                    <a href="{{ route('admin-portal.patients') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-1"></i>Back to Patients List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Auto-format PhilHealth number input
document.getElementById('philhealth_number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, ''); // Remove non-digits

    // Format as XX-XXXXXXXXX-X
    if (value.length >= 2) {
        value = value.substring(0, 2) + '-' + value.substring(2);
    }
    if (value.length >= 13) {
        value = value.substring(0, 13) + '-' + value.substring(13, 14);
    }

    e.target.value = value;
});
</script>
@endsection
