@extends('admin-portal.layouts.app')

@section('title', 'Lab Results - ' . $patient->name)

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-flask me-2"></i>Lab Results
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
        <a href="{{ route('admin-portal.patients.create-lab-result', $patient->id) }}" class="btn btn-admin-primary">
            <i class="fas fa-plus me-1"></i>Add Lab Result
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

<!-- Lab Results -->
<div class="admin-card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Lab Results ({{ $labResults->total() }})</h5>
    </div>
    <div class="card-body">
        @if($labResults->count() > 0)
            <div class="table-responsive">
                <table class="admin-table table">
                    <thead>
                        <tr>
                            <th>Test Name</th>
                            <th>Test Date</th>
                            <th>Status</th>
                            <th>Result</th>
                            <th>Provider</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($labResults as $result)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $result->test_name }}</strong>
                                        <br><small class="text-muted">{{ $result->test_category }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div>{{ \Carbon\Carbon::parse($result->test_ordered_date_time)->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($result->test_ordered_date_time)->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if($result->test_status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($result->test_status == 'ordered')
                                        <span class="badge bg-warning">Ordered</span>
                                    @elseif($result->test_status == 'in_progress')
                                        <span class="badge bg-info">In Progress</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($result->test_status ?? 'unknown') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($result->result_status == 'normal')
                                        <span class="badge bg-success">Normal</span>
                                    @elseif($result->result_status == 'abnormal')
                                        <span class="badge bg-danger">Abnormal</span>
                                    @elseif($result->result_status == 'critical')
                                        <span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i>Critical</span>
                                    @else
                                        <span class="text-muted">{{ $result->result_status ?? 'N/A' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <div>{{ $result->orderingProvider->name ?? 'N/A' }}</div>
                                        @if($result->performed_by)
                                            <small class="text-muted">Performed by: {{ $result->performingTechnician->name ?? 'N/A' }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info" onclick="showResultDetails({{ $result->id }})" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin-portal.patients.delete-lab-result', [$patient->id, $result->id]) }}" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this lab result? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Result">
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
                {{ $labResults->appends(request()->query())->links('vendor.pagination.admin-portal') }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-flask"></i>
                </div>
                <h5>No Lab Results</h5>
                <p class="text-muted">This patient has no lab results yet.</p>
                <a href="{{ route('admin-portal.patients.create-lab-result', $patient->id) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Add First Lab Result
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Result Details Modal -->
<div class="modal fade" id="resultDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lab Result Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="resultDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function showResultDetails(resultId) {
    fetch(`/admin-portal/patients/${{{ $patient->id }}}/lab-results/${resultId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const result = data.result;
                let content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Test Information</h6>
                            <p><strong>Test Name:</strong> ${result.test_name}</p>
                            <p><strong>Test Category:</strong> ${result.test_category}</p>
                            <p><strong>Test Code:</strong> ${result.test_code || 'N/A'}</p>
                            <p><strong>Sample Type:</strong> ${result.sample_type}</p>
                            <p><strong>Test Status:</strong> ${result.test_status}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Result Information</h6>
                            <p><strong>Result:</strong> ${result.result_value || 'N/A'} ${result.result_unit || ''}</p>
                            <p><strong>Reference Range:</strong> ${result.reference_range || 'N/A'}</p>
                            <p><strong>Result Status:</strong> ${result.result_status || 'N/A'}</p>
                            <p><strong>Ordered Date:</strong> ${new Date(result.test_ordered_date_time).toLocaleString()}</p>
                            <p><strong>Performed Date:</strong> ${result.test_performed_date_time ? new Date(result.test_performed_date_time).toLocaleString() : 'N/A'}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Additional Information</h6>
                            <p><strong>Clinical Indication:</strong> ${result.clinical_indication || 'N/A'}</p>
                            <p><strong>Interpretation:</strong> ${result.interpretation || 'N/A'}</p>
                            <p><strong>Comments:</strong> ${result.comments || 'N/A'}</p>
                        </div>
                    </div>
                `;
                document.getElementById('resultDetailsContent').innerHTML = content;
                new bootstrap.Modal(document.getElementById('resultDetailsModal')).show();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load result details.');
        });
}
</script>
@endsection
