@extends('patient.layouts.app')

@section('title', 'Laboratory Results - FRYDT Patient Portal')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-flask me-2"></i>My Laboratory Results
                    </h5>
                </div>
                <div class="card-body">
                    @if($labResults->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Test Name</th>
                                        <th>Category</th>
                                        <th>Date Ordered</th>
                                        <th>Status</th>
                                        <th>Result</th>
                                        <th>Reference Range</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($labResults as $result)
                                    <tr>
                                        <td>
                                            <strong>{{ $result->test_name }}</strong>
                                            @if($result->urgent)
                                                <span class="badge bg-danger ms-1">URGENT</span>
                                            @endif
                                            @if($result->stat)
                                                <span class="badge bg-warning ms-1">STAT</span>
                                            @endif
                                        </td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $result->test_category)) }}</td>
                                        <td>{{ $result->test_ordered_date_time->format('M j, Y g:i A') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $result->getTestStatusBadgeClass() }}">
                                                {{ ucfirst(str_replace('_', ' ', $result->test_status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($result->isCompleted())
                                                <span class="fw-bold {{ $result->isAbnormal() ? 'text-danger' : 'text-success' }}">
                                                    {{ $result->result_value }} {{ $result->result_unit }}
                                                </span>
                                                @if($result->isCritical())
                                                    <i class="fas fa-exclamation-triangle text-danger ms-1" title="Critical Value"></i>
                                                @elseif($result->isAbnormal())
                                                    <i class="fas fa-exclamation-circle text-warning ms-1" title="Abnormal Value"></i>
                                                @endif
                                            @else
                                                <span class="text-muted">Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ $result->reference_range ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('patient.lab-result-detail', $result->id) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $labResults->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-flask fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No laboratory results found</h6>
                            <p class="text-muted">Your laboratory test results will appear here once they are available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
