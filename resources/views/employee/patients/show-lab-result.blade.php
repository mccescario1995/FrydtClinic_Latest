@extends('employee.layouts.app')

@section('title', 'Lab Result Details - ' . $patient->name)

@push('styles')
<style>
    .record-card {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .record-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .info-item {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        border-left: 4px solid #17a2b8;
    }
    .info-item h6 {
        margin-bottom: 0.5rem;
        color: #495057;
        font-weight: 600;
    }
    .info-item p {
        margin-bottom: 0;
        color: #6c757d;
    }
    .section-title {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }
    .compact-table th, .compact-table td {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .result-highlight {
        background: #f8f9fa;
        border: 2px solid #007bff;
        border-radius: 8px;
        padding: 1.5rem;
        margin: 1rem 0;
        text-align: center;
    }
    .result-value {
        font-size: 2rem;
        font-weight: bold;
        color: #007bff;
        margin-bottom: 0.5rem;
    }
    .result-unit {
        font-size: 1.2rem;
        color: #6c757d;
    }
    .result-range {
        font-size: 0.9rem;
        color: #28a745;
        margin-top: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Card -->
            <div class="card record-card mb-4">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-flask me-2"></i>
                                Lab Result Details
                            </h4>
                            <small class="text-white-50">Patient: {{ $patient->name }}</small>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('employee.patients.edit-lab-result', [$patient->id, $result->id]) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>Edit Result
                            </a>
                            <a href="{{ route('employee.patients.lab-results', $patient->id) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Back to Results
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Info Grid -->
            <div class="info-grid">
                <div class="info-item">
                    <h6><i class="fas fa-vial text-info me-1"></i>Test Name</h6>
                    <p>{{ $result->test_name }}</p>
                </div>
                <div class="info-item">
                    <h6><i class="fas fa-tags text-primary me-1"></i>Test Category</h6>
                    <p>{{ $result->test_category }}</p>
                </div>
                <div class="info-item">
                    <h6><i class="fas fa-clock text-warning me-1"></i>Test Status</h6>
                    <p>
                        <span class="status-badge {{ $result->test_status === 'completed' ? 'bg-success' : ($result->test_status === 'in_progress' ? 'bg-warning' : 'bg-secondary') }}">
                            {{ ucfirst(str_replace('_', ' ', $result->test_status)) }}
                        </span>
                    </p>
                </div>
                <div class="info-item">
                    <h6><i class="fas fa-check-circle text-success me-1"></i>Result Status</h6>
                    <p>
                        <span class="status-badge {{ $result->result_status === 'normal' ? 'bg-success' : ($result->result_status === 'abnormal' ? 'bg-danger' : 'bg-secondary') }}">
                            {{ ucfirst($result->result_status ?: 'N/A') }}
                        </span>
                    </p>
                </div>
                <div class="info-item">
                    <h6><i class="fas fa-calendar-plus text-secondary me-1"></i>Ordered</h6>
                    <p>{{ \Carbon\Carbon::parse($result->test_ordered_date_time)->format('M d, Y H:i') }}</p>
                </div>
                <div class="info-item">
                    <h6><i class="fas fa-calendar-check text-success me-1"></i>Completed</h6>
                    <p>{{ $result->result_available_date_time ? \Carbon\Carbon::parse($result->result_available_date_time)->format('M d, Y H:i') : 'N/A' }}</p>
                </div>
            </div>

            <div class="row">
                <!-- Test Information -->
                <div class="col-lg-6">
                    <div class="card record-card">
                        <div class="card-header">
                            <h5 class="section-title mb-0">
                                <i class="fas fa-info-circle text-primary me-2"></i>Test Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table compact-table table-borderless">
                                <tr>
                                    <th width="45%">Test Code:</th>
                                    <td><strong>{{ $result->test_code ?: 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Sample Type:</th>
                                    <td><strong>{{ $result->sample_type }}{{ $result->sample_type === 'other' ? ' (' . $result->sample_type_other . ')' : '' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>QC Passed:</th>
                                    <td>
                                        @if($result->qc_passed)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-danger">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Urgent:</th>
                                    <td>
                                        @if($result->urgent)
                                            <span class="badge bg-danger">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>STAT:</th>
                                    <td>
                                        @if($result->stat)
                                            <span class="badge bg-danger">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            @if($result->test_description)
                            <div class="mt-3">
                                <h6>Test Description</h6>
                                <p class="text-muted">{{ $result->test_description }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Healthcare Providers -->
                <div class="col-lg-6">
                    <div class="card record-card">
                        <div class="card-header">
                            <h5 class="section-title mb-0">
                                <i class="fas fa-user-md text-success me-2"></i>Healthcare Providers
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table compact-table table-borderless">
                                <tr>
                                    <th width="45%">Ordered By:</th>
                                    <td><strong>{{ $result->orderingProvider->name ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Performed By:</th>
                                    <td><strong>{{ $result->performingTechnician->name ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Reviewed By:</th>
                                    <td><strong>{{ $result->reviewingProvider->name ?? 'N/A' }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Results Highlight -->
            @if($result->result_value)
            <div class="result-highlight">
                <div class="result-value">{{ $result->result_value }}</div>
                <div class="result-unit">{{ $result->result_unit ?: 'units' }}</div>
                @if($result->reference_range)
                <div class="result-range">Reference Range: {{ $result->reference_range }}</div>
                @endif
            </div>
            @endif

            <!-- Detailed Test Results -->
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="card record-card">
                        <div class="card-header">
                            <h5 class="section-title mb-0">
                                <i class="fas fa-chart-line text-danger me-2"></i>Test Results
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table compact-table table-borderless">
                                <tr>
                                    <th width="45%">Result Value:</th>
                                    <td><strong>{{ $result->result_value ?: 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Result Unit:</th>
                                    <td><strong>{{ $result->result_unit ?: 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Reference Range:</th>
                                    <td><strong>{{ $result->reference_range ?: 'N/A' }}</strong></td>
                                </tr>
                            </table>

                            @if($result->test_result)
                            <div class="mt-3">
                                <h6>Detailed Test Result</h6>
                                <div class="bg-light p-3 rounded">
                                    {{ $result->test_result }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Billing Information -->
                <div class="col-lg-6">
                    <div class="card record-card">
                        <div class="card-header">
                            <h5 class="section-title mb-0">
                                <i class="fas fa-money-bill text-warning me-2"></i>Billing Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table compact-table table-borderless">
                                <tr>
                                    <th width="45%">Test Cost:</th>
                                    <td><strong>{{ $result->test_cost ? '₱' . number_format($result->test_cost, 2) : 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>PhilHealth Coverage:</th>
                                    <td>
                                        @if($result->covered_by_philhealth)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Coverage Amount:</th>
                                    <td><strong>{{ $result->philhealth_coverage_amount ? '₱' . number_format($result->philhealth_coverage_amount, 2) : 'N/A' }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            @if($result->clinical_indication || $result->interpretation || $result->comments || $result->qc_notes)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card record-card">
                        <div class="card-header">
                            <h5 class="section-title mb-0">
                                <i class="fas fa-file-alt text-muted me-2"></i>Additional Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($result->clinical_indication)
                                <div class="col-md-6">
                                    <h6>Clinical Indication</h6>
                                    <p class="text-muted">{{ $result->clinical_indication }}</p>
                                </div>
                                @endif
                                @if($result->interpretation)
                                <div class="col-md-6">
                                    <h6>Interpretation</h6>
                                    <p class="text-muted">{{ $result->interpretation }}</p>
                                </div>
                                @endif
                                @if($result->comments)
                                <div class="col-md-6">
                                    <h6>Comments</h6>
                                    <p class="text-muted">{{ $result->comments }}</p>
                                </div>
                                @endif
                                @if($result->qc_notes)
                                <div class="col-md-6">
                                    <h6>QC Notes</h6>
                                    <p class="text-muted">{{ $result->qc_notes }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Follow-up Information -->
            @if($result->requires_follow_up || $result->follow_up_instructions || $result->follow_up_date)
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="card record-card">
                        <div class="card-header">
                            <h5 class="section-title mb-0">
                                <i class="fas fa-calendar-check text-secondary me-2"></i>Follow-up Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table compact-table table-borderless">
                                <tr>
                                    <th width="45%">Requires Follow-up:</th>
                                    <td>
                                        @if($result->requires_follow_up)
                                            <span class="badge bg-warning">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Follow-up Date:</th>
                                    <td><strong>{{ $result->follow_up_date ? \Carbon\Carbon::parse($result->follow_up_date)->format('M d, Y') : 'N/A' }}</strong></td>
                                </tr>
                            </table>

                            @if($result->follow_up_instructions)
                            <div class="mt-3">
                                <h6>Follow-up Instructions</h6>
                                <p class="text-muted">{{ $result->follow_up_instructions }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Rejection Information -->
                @if($result->rejection_reason || $result->rejected_date_time)
                <div class="col-lg-6">
                    <div class="card record-card">
                        <div class="card-header">
                            <h5 class="section-title mb-0">
                                <i class="fas fa-times-circle text-danger me-2"></i>Rejection Information
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($result->rejection_reason)
                            <div class="mb-3">
                                <h6>Rejection Reason</h6>
                                <p class="text-danger">{{ $result->rejection_reason }}</p>
                            </div>
                            @endif
                            @if($result->rejected_date_time)
                            <div>
                                <h6>Rejected Date/Time</h6>
                                <p class="text-muted">{{ \Carbon\Carbon::parse($result->rejected_date_time)->format('M d, Y H:i') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
