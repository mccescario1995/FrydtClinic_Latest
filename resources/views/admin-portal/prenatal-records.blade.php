@extends('admin-portal.layouts.app')

@section('title', 'Prenatal Records Management')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-baby me-2"></i>Prenatal Records Management
    </h1>
    <p class="page-subtitle">Manage and view all prenatal care records</p>
</div>

<!-- Filters -->
<div class="filter-section">
    <form method="GET" action="{{ route('admin-portal.prenatal-records') }}" class="row g-3">
        <div class="col-md-3">
            <label for="patient_id" class="form-label">Patient</label>
            <select name="patient_id" id="patient_id" class="form-select">
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
            <select name="provider_id" id="provider_id" class="form-select">
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
            <input type="date" name="date_from" id="date_from" class="form-control"
                   value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <label for="date_to" class="form-label">To Date</label>
            <input type="date" name="date_to" id="date_to" class="form-control"
                   value="{{ request('date_to') }}">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-search me-1"></i>Filter
            </button>
            <a href="{{ route('admin-portal.prenatal-records') }}" class="btn btn-outline-secondary" title="Clear all filters">
                <i class="fas fa-times me-1"></i>Clear
            </a>
        </div>
    </form>
</div>

<!-- Prenatal Records Table -->
<div class="content-section">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-list me-2"></i>Prenatal Records</h2>
        <p class="section-subtitle">Detailed prenatal care records</p>
    </div>

    <div class="admin-card">
        <div class="card-body">
            @if($prenatalRecords->count() > 0)
                <div class="table-responsive">
                    <table class="table admin-table table-hover">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Visit Date</th>
                                <th>Visit Number</th>
                                <th>Provider</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prenatalRecords as $record)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                 style="width: 40px; height: 40px; font-weight: bold; font-size: 16px;">
                                                {{ substr($record->patient->name ?? 'N/A', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $record->patient->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $record->patient->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($record->visit_date)->format('l') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $record->visit_number }}</span>
                                    </td>
                                    <td>
                                        {{ $record->attendingPhysician->name ?? $record->midwife->name ?? 'Unknown' }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin-portal.patients.show', $record->patient_id) }}" class="btn btn-sm btn-outline-info" title="View Patient">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $prenatalRecords->appends(request()->query())->links() }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-baby"></i>
                    </div>
                    <h5 class="empty-title">No Prenatal Records Found</h5>
                    <p class="empty-text">No prenatal records match your current filter criteria.</p>
                    <a href="{{ route('admin-portal.prenatal-records') }}" class="btn btn-admin-primary">
                        <i class="fas fa-times me-1"></i>Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
