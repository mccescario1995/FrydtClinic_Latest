@extends('admin-portal.layouts.app')

@section('title', 'Prenatal Records - ' . $patient->name)

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-baby me-2"></i>Prenatal Records
    </h1>
    <p class="page-subtitle">{{ $patient->name }} • Patient ID: {{ $patient->id }}</p>
</div>

<!-- Action Buttons -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin-portal.patients.show', $patient->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Patient
        </a>
    </div>
    <div>
        <a href="{{ route('admin-portal.patients.create-prenatal-record', $patient->id) }}" class="btn btn-admin-primary">
            <i class="fas fa-plus me-1"></i>Add Prenatal Record
        </a>
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

<!-- Prenatal Records -->
<div class="admin-card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Prenatal Records ({{ $prenatalRecords->total() }})</h5>
    </div>
    <div class="card-body">
        @if($prenatalRecords->count() > 0)
            <div class="table-responsive">
                <table class="admin-table table">
                    <thead>
                        <tr>
                            <th>Visit Date</th>
                            <th>Provider</th>
                            <th>Gestational Age</th>
                            <th>Risk Level</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prenatalRecords as $record)
                            <tr>
                                <td>
                                    <div>
                                        <div>{{ \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $record->visit_time ? \Carbon\Carbon::parse($record->visit_time)->format('h:i A') : 'No time' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div>{{ $record->attendingPhysician->name ?? 'N/A' }}</div>
                                        @if($record->midwife)
                                            <small class="text-muted">Midwife: {{ $record->midwife->name }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($record->gestational_age_weeks)
                                        {{ $record->gestational_age_weeks }}w {{ $record->gestational_age_days ?? 0 }}d
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->risk_level)
                                        <span class="badge bg-{{ match($record->risk_level) {
                                            'low' => 'success',
                                            'moderate' => 'warning',
                                            'high' => 'danger',
                                            default => 'secondary'
                                        } }}">
                                            {{ ucfirst($record->risk_level) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Not assessed</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ match($record->pregnancy_status) {
                                        'active' => 'success',
                                        'completed' => 'primary',
                                        'terminated' => 'danger',
                                        default => 'secondary'
                                    } }}">
                                        {{ ucfirst($record->pregnancy_status ?? 'active') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info" onclick="showRecordDetails({{ $record->id }})" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('admin-portal.patients.edit-prenatal-record', [$patient->id, $record->id]) }}" class="btn btn-sm btn-outline-warning" title="Edit Record">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin-portal.patients.delete-prenatal-record', [$patient->id, $record->id]) }}" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this prenatal record? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Record">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $prenatalRecords->appends(request()->query())->links('vendor.pagination.admin-portal') }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-baby"></i>
                </div>
                <h5>No Prenatal Records</h5>
                <p class="text-muted">This patient has no prenatal records yet.</p>
                <a href="{{ route('admin-portal.patients.create-prenatal-record', $patient->id) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Add First Prenatal Record
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Record Details Modal -->
<div class="modal fade" id="recordDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Prenatal Record Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="recordDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function showRecordDetails(recordId) {
    fetch(`/admin-portal/patients/${{{ $patient->id }}}/prenatal-records/${recordId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const record = data.record;
                let content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Visit Information</h6>
                            <p><strong>Visit Date:</strong> ${record.visit_date_formatted}</p>
                            <p><strong>Visit Time:</strong> ${record.visit_time || 'Not specified'}</p>
                            <p><strong>Attending Physician:</strong> ${record.attending_physician_name}</p>
                            <p><strong>Midwife:</strong> ${record.midwife_name}</p>
                            <p><strong>Pregnancy Status:</strong> ${record.pregnancy_status_formatted}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Gestational Information</h6>
                            <p><strong>Last Menstrual Period:</strong> ${record.last_menstrual_period_formatted || 'Not specified'}</p>
                            <p><strong>Estimated Due Date:</strong> ${record.estimated_due_date_formatted || 'Not specified'}</p>
                            <p><strong>Gestational Age:</strong> ${record.gestational_age_weeks ? record.gestational_age_weeks + ' weeks ' + (record.gestational_age_days || 0) + ' days' : 'Not specified'}</p>
                            <p><strong>Risk Level:</strong> ${record.risk_level_formatted}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Vital Signs</h6>
                            <div class="row">
                                <div class="col-md-3"><p><strong>BP:</strong> ${record.blood_pressure_systolic || 'N/A'}/${record.blood_pressure_diastolic || 'N/A'}</p></div>
                                <div class="col-md-3"><p><strong>Weight:</strong> ${record.weight_kg || 'N/A'} kg</p></div>
                                <div class="col-md-3"><p><strong>Height:</strong> ${record.height_cm || 'N/A'} cm</p></div>
                                <div class="col-md-3"><p><strong>BMI:</strong> ${record.bmi || 'N/A'}</p></div>
                            </div>
                        </div>
                    </div>
                `;
                document.getElementById('recordDetailsContent').innerHTML = content;
                new bootstrap.Modal(document.getElementById('recordDetailsModal')).show();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load record details.');
        });
}
</script>
@endsection
