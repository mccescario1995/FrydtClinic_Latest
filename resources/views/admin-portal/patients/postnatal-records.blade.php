@extends('admin-portal.layouts.app')

@section('title', 'Postnatal Records - ' . $patient->name)

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-child me-2"></i>Postnatal Records
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
        <a href="{{ route('admin-portal.patients.create-postnatal-record', $patient->id) }}" class="btn btn-admin-primary">
            <i class="fas fa-plus me-1"></i>Add Postnatal Record
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

<!-- Postnatal Records -->
<div class="admin-card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Postnatal Records ({{ $postnatalRecords->total() }})</h5>
    </div>
    <div class="card-body">
        @if($postnatalRecords->count() > 0)
            <div class="table-responsive">
                <table class="admin-table table">
                    <thead>
                        <tr>
                            <th>Visit Date</th>
                            <th>Visit #</th>
                            <th>Provider</th>
                            <th>Postpartum Days</th>
                            <th>Breastfeeding</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($postnatalRecords as $record)
                            <tr>
                                <td>
                                    <div>
                                        <div>{{ \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') }}</div>
                                        <small class="text-muted">Visit {{ $record->visit_number }}</small>
                                    </div>
                                </td>
                                <td>{{ $record->visit_number }}</td>
                                <td>{{ $record->provider->name ?? 'N/A' }}</td>
                                <td>
                                    @if($record->days_postpartum)
                                        {{ $record->days_postpartum }} days
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->breastfeeding_status)
                                        <span class="badge bg-success">{{ ucfirst($record->breastfeeding_status) }}</span>
                                    @else
                                        <span class="badge bg-secondary">Not recorded</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info" onclick="showRecordDetails({{ $record->id }})" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin-portal.patients.delete-postnatal-record', [$patient->id, $record->id]) }}" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this postnatal record? This action cannot be undone.')">
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
                {{ $postnatalRecords->appends(request()->query())->links('vendor.pagination.admin-portal') }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-child"></i>
                </div>
                <h5>No Postnatal Records</h5>
                <p class="text-muted">This patient has no postnatal records yet.</p>
                <a href="{{ route('admin-portal.patients.create-postnatal-record', $patient->id) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Add First Postnatal Record
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
                <h5 class="modal-title">Postnatal Record Details</h5>
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
    fetch(`/admin-portal/patients/${{{ $patient->id }}}/postnatal-records/${recordId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const record = data.record;
                let content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Visit Information</h6>
                            <p><strong>Visit Date:</strong> ${new Date(record.visit_date).toLocaleDateString()}</p>
                            <p><strong>Visit Number:</strong> ${record.visit_number}</p>
                            <p><strong>Provider:</strong> ${record.provider_name || 'N/A'}</p>
                            <p><strong>Days Postpartum:</strong> ${record.days_postpartum || 'N/A'}</p>
                            <p><strong>Weeks Postpartum:</strong> ${record.weeks_postpartum || 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Vital Signs</h6>
                            <p><strong>Weight:</strong> ${record.weight || 'N/A'} kg</p>
                            <p><strong>Blood Pressure:</strong> ${record.blood_pressure_systolic || 'N/A'}/${record.blood_pressure_diastolic || 'N/A'}</p>
                            <p><strong>Heart Rate:</strong> ${record.heart_rate || 'N/A'} bpm</p>
                            <p><strong>Temperature:</strong> ${record.temperature || 'N/A'}°C</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6>Breastfeeding & Newborn</h6>
                            <p><strong>Breastfeeding Status:</strong> ${record.breastfeeding_status || 'N/A'}</p>
                            <p><strong>Newborn Check:</strong> ${record.newborn_check ? 'Yes' : 'No'}</p>
                            <p><strong>Newborn Weight:</strong> ${record.newborn_weight || 'N/A'} kg</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Family Planning</h6>
                            <p><strong>Family Planning Method:</strong> ${record.family_planning_method || 'N/A'}</p>
                            <p><strong>Follow-up Date:</strong> ${record.follow_up_date ? new Date(record.follow_up_date).toLocaleDateString() : 'N/A'}</p>
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
