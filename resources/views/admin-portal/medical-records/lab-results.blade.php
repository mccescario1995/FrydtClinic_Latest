@extends('admin-portal.layouts.app')

@section('title', 'Lab Results - Admin Portal')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-flask me-2"></i>Lab Results
    </h1>
    <p class="page-subtitle">Manage laboratory test results for all patients</p>
</div>

<!-- Filters -->
<div class="admin-card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin-portal.medical-records.lab-results') }}" class="row g-3">
            <div class="col-md-3">
                <label for="patient_id" class="form-label">Patient</label>
                <select class="form-select" id="patient_id" name="patient_id">
                    <option value="">All Patients</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                            {{ $patient->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="provider_id" class="form-label">Provider</label>
                <select class="form-select" id="provider_id" name="provider_id">
                    <option value="">All Providers</option>
                    @foreach($providers as $provider)
                        <option value="{{ $provider->id }}" {{ request('provider_id') == $provider->id ? 'selected' : '' }}>
                            {{ $provider->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lab Results Table -->
<div class="admin-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Lab Results ({{ $labResults->total() }})</h5>
        <a href="{{ route('admin-portal.patients') }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-users me-1"></i>View Patients
        </a>
    </div>
    <div class="card-body">
        @if($labResults->count() > 0)
            <div class="table-responsive">
                <table class="admin-table table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Test Name</th>
                            <th>Test Date</th>
                            <th>Provider</th>
                            <th>Status</th>
                            <th>Result</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($labResults as $result)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $result->patient->name }}</strong>
                                        <br><small class="text-muted">{{ $result->patient->patientProfile->phone ?? 'No phone' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div>{{ $result->test_name }}</div>
                                        <small class="text-muted">{{ $result->test_category }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div>{{ \Carbon\Carbon::parse($result->test_ordered_date_time)->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($result->test_ordered_date_time)->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $result->orderingProvider->name ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ match($result->test_status) {
                                        'pending' => 'warning',
                                        'in_progress' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'secondary'
                                    } }}">
                                        {{ ucfirst(str_replace('_', ' ', $result->test_status ?? 'pending')) }}
                                    </span>
                                </td>
                                <td>
                                    @if($result->result_value)
                                        <div>
                                            <strong>{{ $result->result_value }}</strong>
                                            @if($result->result_unit)
                                                <small class="text-muted">{{ $result->result_unit }}</small>
                                            @endif
                                        </div>
                                        @if($result->reference_range)
                                            <small class="text-muted">Ref: {{ $result->reference_range }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin-portal.patients.show', $result->patient_id) }}" class="btn btn-sm btn-outline-info" title="View Patient">
                                        <i class="fas fa-eye"></i> View Patient
                                    </a>
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
                <h5>No Lab Results Found</h5>
                <p class="text-muted">There are no lab results matching your criteria.</p>
                <a href="{{ route('admin-portal.patients') }}" class="btn btn-primary">
                    <i class="fas fa-users me-1"></i>View Patients
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
