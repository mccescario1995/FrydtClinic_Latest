@extends('admin-portal.layouts.app')

@section('title', 'Delivery Records - ' . $patient->name)

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-hospital me-2"></i>Delivery Records
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
        <a href="{{ route('admin-portal.patients.create-delivery-record', $patient->id) }}" class="btn btn-admin-primary">
            <i class="fas fa-plus me-1"></i>Add Delivery Record
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

<!-- Delivery Records -->
<div class="admin-card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Delivery Records ({{ $deliveryRecords->total() }})</h5>
    </div>
    <div class="card-body">
        @if($deliveryRecords->count() > 0)
            <div class="table-responsive">
                <table class="admin-table table">
                    <thead>
                        <tr>
                            <th>Delivery Date</th>
                            <th>Attending Provider</th>
                            <th>Delivery Type</th>
                            <th>Newborn Gender</th>
                            <th>Newborn Weight</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveryRecords as $record)
                            <tr>
                                <td>
                                    <div>
                                        <div>{{ \Carbon\Carbon::parse($record->delivery_date_time)->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($record->delivery_date_time)->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td>{{ $record->attendingProvider->name ?? 'N/A' }}</td>
                                <td>
                                    @if($record->delivery_type)
                                        <span class="badge bg-primary">{{ ucfirst($record->delivery_type) }}</span>
                                    @else
                                        <span class="badge bg-secondary">Not specified</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->newborn_gender)
                                        <span class="badge bg-info">{{ ucfirst($record->newborn_gender) }}</span>
                                    @else
                                        <span class="badge bg-secondary">Not recorded</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->newborn_weight)
                                        {{ $record->newborn_weight }} kg
                                    @else
                                        <span class="text-muted">Not recorded</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info" onclick="showRecordDetails({{ $record->id }})" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin-portal.patients.delete-delivery-record', [$patient->id, $record->id]) }}" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this delivery record? This action cannot be undone.')">
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
                {{ $deliveryRecords->appends(request()->query())->links('vendor.pagination.admin-portal') }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-hospital"></i>
                </div>
                <h5>No Delivery Records</h5>
                <p class="text-muted">This patient has no delivery records yet.</p>
                <a href="{{ route('admin-portal.patients.create-delivery-record', $patient->id) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Add First Delivery Record
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Record Details Modal -->
<div class="modal fade" id="recordDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delivery Record Details</h5>
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
    fetch(`/admin-portal/patients/${{{ $patient->id }}}/delivery-records/${recordId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const record = data.record;
                let content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Delivery Information</h6>
                            <p><strong>Delivery Date/Time:</strong> ${new Date(record.delivery_date_time).toLocaleString()}</p>
                            <p><strong>Delivery Type:</strong> ${record.delivery_type || 'N/A'}</p>
                            <p><strong>Delivery Place:</strong> ${record.delivery_place || 'N/A'}</p>
                            <p><strong>Attending Provider:</strong> ${record.attending_provider_name || 'N/A'}</p>
                            <p><strong>Delivering Provider:</strong> ${record.delivering_provider_name || 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Labor Information</h6>
                            <p><strong>Labor Duration:</strong> ${record.labor_duration_hours || 0}h ${record.labor_duration_minutes || 0}m</p>
                            <p><strong>Presentation:</strong> ${record.presentation || 'N/A'}</p>
                            <p><strong>Position:</strong> ${record.position || 'N/A'}</p>
                            <p><strong>Episiotomy:</strong> ${record.episiotomy_performed ? 'Yes' : 'No'}</p>
                            <p><strong>Anesthesia Type:</strong> ${record.anesthesia_type || 'N/A'}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6>Newborn Information</h6>
                            <p><strong>Gender:</strong> ${record.newborn_gender ? record.newborn_gender.toUpperCase() : 'N/A'}</p>
                            <p><strong>Weight:</strong> ${record.newborn_weight || 'N/A'} kg</p>
                            <p><strong>Length:</strong> ${record.newborn_length || 'N/A'} cm</p>
                            <p><strong>APGAR 1min:</strong> ${record.newborn_apgar_1min || 'N/A'}</p>
                            <p><strong>APGAR 5min:</strong> ${record.newborn_apgar_5min || 'N/A'}</p>
                            <p><strong>APGAR 10min:</strong> ${record.newborn_apgar_10min || 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Postpartum Care</h6>
                            <p><strong>Blood Loss:</strong> ${record.estimated_blood_loss || 'N/A'} mL</p>
                            <p><strong>Placenta Complete:</strong> ${record.placenta_complete ? 'Yes' : 'No'}</p>
                            <p><strong>Breastfeeding Initiation:</strong> ${record.breastfeeding_initiation || 'N/A'}</p>
                            <p><strong>Expected Discharge:</strong> ${record.expected_discharge_date ? new Date(record.expected_discharge_date).toLocaleDateString() : 'N/A'}</p>
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
