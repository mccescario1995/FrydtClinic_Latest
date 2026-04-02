@extends('patient.layouts.app')

@section('title', 'Lab Result Details - FRYDT Patient Portal')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-flask me-2"></i>Laboratory Result Details
                    </h5>
                    <a href="{{ route('patient.laboratory-results') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Results
                    </a>
                </div>
                <div class="card-body">
                    <!-- Test Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Test Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">Test Name:</td>
                                    <td>{{ $labResult->test_name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Test Code:</td>
                                    <td>{{ $labResult->test_code ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Category:</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $labResult->test_category)) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Sample Type:</td>
                                    <td>{{ $labResult->sample_type }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Priority:</td>
                                    <td>
                                        @if($labResult->urgent)
                                            <span class="badge bg-danger">URGENT</span>
                                        @elseif($labResult->stat)
                                            <span class="badge bg-warning">STAT</span>
                                        @else
                                            <span class="badge bg-secondary">Routine</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Test Timeline</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">Ordered:</td>
                                    <td>{{ $labResult->test_ordered_date_time->format('M j, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Sample Collected:</td>
                                    <td>{{ $labResult->sample_collection_date_time?->format('M j, Y g:i A') ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Performed:</td>
                                    <td>{{ $labResult->test_performed_date_time?->format('M j, Y g:i A') ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Result Available:</td>
                                    <td>{{ $labResult->result_available_date_time?->format('M j, Y g:i A') ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Reviewed:</td>
                                    <td>{{ $labResult->result_reviewed_date_time?->format('M j, Y g:i A') ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Result Information -->
                    @if($labResult->isCompleted())
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Result Information</h6>
                            <div class="card border-{{ $labResult->isAbnormal() ? 'danger' : 'success' }}">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h4 class="mb-1 {{ $labResult->isAbnormal() ? 'text-danger' : 'text-success' }}">
                                                {{ $labResult->result_value }} {{ $labResult->result_unit }}
                                                @if($labResult->isCritical())
                                                    <i class="fas fa-exclamation-triangle text-danger ms-2" title="Critical Value"></i>
                                                @elseif($labResult->isAbnormal())
                                                    <i class="fas fa-exclamation-circle text-warning ms-2" title="Abnormal Value"></i>
                                                @endif
                                            </h4>
                                            <p class="text-muted mb-0">Result Value</p>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="mb-1">{{ $labResult->reference_range ?? 'N/A' }}</h6>
                                            <p class="text-muted mb-0">Reference Range</p>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="mb-1">
                                                <span class="badge bg-{{ $labResult->getResultStatusBadgeClass() }} fs-6">
                                                    {{ ucfirst(str_replace('_', ' ', $labResult->result_status)) }}
                                                </span>
                                            </h6>
                                            <p class="text-muted mb-0">Result Status</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Clinical Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Clinical Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">Indication:</td>
                                    <td>{{ $labResult->clinical_indication ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Interpretation:</td>
                                    <td>{{ $labResult->interpretation ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Comments:</td>
                                    <td>{{ $labResult->comments ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Personnel Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">Ordered By:</td>
                                    <td>{{ $labResult->orderingProvider->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Performed By:</td>
                                    <td>{{ $labResult->performingTechnician->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Reviewed By:</td>
                                    <td>{{ $labResult->reviewingProvider->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Quality Control -->
                    @if($labResult->qc_passed !== null)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Quality Control</h6>
                            <div class="alert alert-{{ $labResult->qc_passed ? 'success' : 'warning' }}">
                                <i class="fas fa-{{ $labResult->qc_passed ? 'check-circle' : 'exclamation-triangle' }} me-2"></i>
                                Quality Control: {{ $labResult->qc_passed ? 'PASSED' : 'FAILED' }}
                                @if($labResult->qc_notes)
                                    <br><small>{{ $labResult->qc_notes }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Billing Information -->
                    @if($labResult->test_cost)
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Billing Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">Test Cost:</td>
                                    <td>₱{{ number_format($labResult->test_cost, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">PhilHealth Covered:</td>
                                    <td>
                                        @if($labResult->covered_by_philhealth)
                                            <span class="badge bg-success">Yes</span>
                                            @if($labResult->philhealth_coverage_amount)
                                                (₱{{ number_format($labResult->philhealth_coverage_amount, 2) }})
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
