@extends('admin-portal.layouts.app')

@section('title', 'Delivery Records - Admin Portal')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-baby-carriage me-2"></i>Delivery Records
    </h1>
    <p class="page-subtitle">Manage delivery records for all patients</p>
</div>

<!-- Filters -->
<div class="admin-card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin-portal.medical-records.delivery') }}" class="row g-3">
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
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
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

<!-- Delivery Records Table -->
<div class="admin-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Delivery Records ({{ $deliveryRecords->total() }})</h5>
        <a href="{{ route('admin-portal.patients') }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-users me-1"></i>View Patients
        </a>
    </div>
    <div class="card-body">
        @if($deliveryRecords->count() > 0)
            <div class="table-responsive">
                <table class="admin-table table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Delivery Date</th>
                            <th>Provider</th>
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
                                        <strong>{{ $record->patient->name }}</strong>
                                        <br><small class="text-muted">{{ $record->patient->patientProfile->phone ?? 'No phone' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div>{{ \Carbon\Carbon::parse($record->delivery_date_time)->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($record->delivery_date_time)->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div>{{ $record->attendingProvider->name ?? 'N/A' }}</div>
                                        @if($record->deliveringProvider)
                                            <small class="text-muted">Delivering: {{ $record->deliveringProvider->name }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $record->delivery_type ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($record->newborn_gender)
                                        <span class="badge bg-{{ $record->newborn_gender === 'male' ? 'primary' : 'success' }}">
                                            {{ ucfirst($record->newborn_gender) }}
                                        </span>
                                    @else
                                        <span class="text-muted">Not specified</span>
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
                                    <a href="{{ route('admin-portal.patients.show', $record->patient_id) }}" class="btn btn-sm btn-outline-info" title="View Patient">
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
                {{ $deliveryRecords->appends(request()->query())->links('vendor.pagination.admin-portal') }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-baby-carriage"></i>
                </div>
                <h5>No Delivery Records Found</h5>
                <p class="text-muted">There are no delivery records matching your criteria.</p>
                <a href="{{ route('admin-portal.patients') }}" class="btn btn-primary">
                    <i class="fas fa-users me-1"></i>View Patients
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
