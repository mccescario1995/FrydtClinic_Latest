@extends('employee.layouts.app')

@section('title', 'Medical Records - ' . $patient->name)

@push('styles')
<style>
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        color: #6c757d;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        border-bottom-color: #007bff;
        color: #007bff;
        background-color: transparent;
    }
    .nav-tabs .nav-link:hover {
        border-bottom-color: #007bff;
        color: #007bff;
    }
    .tab-content {
        padding-top: 20px;
    }
    .table-compact th, .table-compact td {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    .record-summary {
        background-color: #f8f9fa;
        border-left: 4px solid #007bff;
        padding: 10px 15px;
        margin-bottom: 15px;
        border-radius: 4px;
    }
    .quick-actions {
        position: sticky;
        top: 20px;
        z-index: 100;
    }
    /* Responsive design */
    @media (max-width: 768px) {
        .nav-tabs .nav-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
        .record-summary {
            padding: 8px 12px;
            margin-bottom: 10px;
        }
        .record-summary h5 {
            font-size: 1rem;
        }
        .table-responsive {
            font-size: 0.8rem;
        }
        .btn-group-sm .btn {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }
        .modal-dialog {
            margin: 0.5rem;
        }
        .card-header .card-tools {
            margin-top: 10px;
        }
        .card-header h3 {
            font-size: 1.25rem;
        }
    }
    @media (max-width: 576px) {
        .nav-tabs {
            flex-direction: column;
        }
        .nav-tabs .nav-link {
            border-radius: 0;
            border-left: none;
            border-right: none;
        }
        .record-summary {
            text-align: center;
        }
        .table-compact th, .table-compact td {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        .btn-group-vertical {
            flex-direction: row;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-file-medical mr-2"></i>
                        Medical Records for {{ $patient->name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('employee.patients.show', $patient->id) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Patient
                        </a>
                        <a href="{{ route('employee.patients') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-list"></i> All Patients
                        </a>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card-body border-bottom">
                    <div class="row text-center">
                        <div class="col-md-2 col-6">
                            <div class="record-summary">
                                <h5 class="mb-0">{{ $prenatalRecords->total() }}</h5>
                                <small class="text-muted">Prenatal</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="record-summary">
                                <h5 class="mb-0">{{ $postnatalRecords->total() }}</h5>
                                <small class="text-muted">Postnatal</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="record-summary">
                                <h5 class="mb-0">{{ $postpartumRecords->total() }}</h5>
                                <small class="text-muted">Postpartum</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="record-summary">
                                <h5 class="mb-0">{{ $deliveryRecords->total() }}</h5>
                                <small class="text-muted">Delivery</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="record-summary">
                                <h5 class="mb-0">{{ $labResults->total() }}</h5>
                                <small class="text-muted">Lab Results</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="record-summary">
                                <h5 class="mb-0">{{ $prenatalRecords->total() + $postnatalRecords->total() + $postpartumRecords->total() + $deliveryRecords->total() + $labResults->total() + $treatments->total() }}</h5>
                                <small class="text-muted">Total Records</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabbed Interface -->
                <div class="card-body">
                    <ul class="nav nav-tabs" id="medicalRecordsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="prenatal-tab" data-bs-toggle="tab" data-bs-target="#prenatal" type="button" role="tab">
                                <i class="fas fa-baby me-1"></i>Prenatal Care
                                <span class="badge bg-primary ms-1">{{ $prenatalRecords->total() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="postnatal-tab" data-bs-toggle="tab" data-bs-target="#postnatal" type="button" role="tab">
                                <i class="fas fa-child me-1"></i>Postnatal Care
                                <span class="badge bg-success ms-1">{{ $postnatalRecords->total() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="postpartum-tab" data-bs-toggle="tab" data-bs-target="#postpartum" type="button" role="tab">
                                <i class="fas fa-female me-1"></i>Postpartum Care
                                <span class="badge bg-warning ms-1">{{ $postpartumRecords->total() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="delivery-tab" data-bs-toggle="tab" data-bs-target="#delivery" type="button" role="tab">
                                <i class="fas fa-hospital me-1"></i>Delivery Records
                                <span class="badge bg-danger ms-1">{{ $deliveryRecords->total() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="lab-tab" data-bs-toggle="tab" data-bs-target="#lab" type="button" role="tab">
                                <i class="fas fa-flask me-1"></i>Lab Results
                                <span class="badge bg-info ms-1">{{ $labResults->total() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="prescriptions-tab" data-bs-toggle="tab" data-bs-target="#prescriptions" type="button" role="tab">
                                <i class="fas fa-prescription-bottle me-1"></i>Prescriptions
                                <span class="badge bg-secondary ms-1">{{ $treatments->total() }}</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="medicalRecordsTabContent">
                        <!-- Prenatal Records Tab -->
                        <div class="tab-pane fade show active" id="prenatal" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">
                                    <i class="fas fa-baby me-2 text-primary"></i>Prenatal Care Records
                                </h6>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createPrenatalModal">
                                        <i class="fas fa-plus"></i> Quick Add
                                    </button>
                                    <a href="{{ route('employee.patients.prenatal-records', $patient->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-list"></i> View All
                                    </a>
                                </div>
                            </div>

                            @if($prenatalRecords->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-compact table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Provider</th>
                                                <th>Gestational Age</th>
                                                <th>Blood Pressure</th>
                                                <th>Weight</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($prenatalRecords as $record)
                                            <tr>
                                                <td>{{ $record->visit_date->format('M j, Y') }}</td>
                                                <td>{{ $record->attendingPhysician->name ?? 'N/A' }}</td>
                                                <td>{{ $record->getGestationalAgeAttribute() }}</td>
                                                <td>{{ $record->getBloodPressureAttribute() }}</td>
                                                <td>{{ $record->weight ? $record->weight . ' kg' : 'N/A' }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-info btn-sm action-btn" data-action="view" data-type="prenatal" data-id="{{ $record->id }}" title="View Record">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                            </svg>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-warning btn-sm action-btn" data-action="edit" data-type="prenatal" data-id="{{ $record->id }}" title="Edit Record">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                                            </svg>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm action-btn" data-action="delete" data-type="prenatal" data-id="{{ $record->id }}" title="Delete Record">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $prenatalRecords->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-baby fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Prenatal Records</h5>
                                    <p class="text-muted mb-3">Start by adding the patient's first prenatal visit.</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPrenatalModal">
                                        <i class="fas fa-plus"></i> Add First Record
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Postnatal Records Tab -->
                        <div class="tab-pane fade" id="postnatal" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">
                                    <i class="fas fa-child me-2 text-success"></i>Postnatal Care Records
                                </h6>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#createPostnatalModal">
                                        <i class="fas fa-plus"></i> Quick Add
                                    </button>
                                    <a href="{{ route('employee.patients.postnatal-records', $patient->id) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-list"></i> View All
                                    </a>
                                </div>
                            </div>

                            @if(isset($postnatalRecords) && $postnatalRecords->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-compact table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Provider</th>
                                                <th>Days Postpartum</th>
                                                <th>Weight</th>
                                                <th>Blood Pressure</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($postnatalRecords as $record)
                                            <tr>
                                                <td>{{ $record->visit_date->format('M j, Y') }}</td>
                                                <td>{{ $record->provider->name ?? 'N/A' }}</td>
                                                <td>{{ $record->days_postpartum ?? 'N/A' }} days</td>
                                                <td>{{ $record->weight ? $record->weight . ' kg' : 'N/A' }}</td>
                                                <td>
                                                    @if($record->blood_pressure_systolic && $record->blood_pressure_diastolic)
                                                        {{ $record->blood_pressure_systolic }}/{{ $record->blood_pressure_diastolic }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-info btn-sm action-btn" data-action="view" data-type="postnatal" data-id="{{ $record->id }}" title="View Record">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                            </svg>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-warning btn-sm action-btn" data-action="edit" data-type="postnatal" data-id="{{ $record->id }}" title="Edit Record">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                                            </svg>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm action-btn" data-action="delete" data-type="postnatal" data-id="{{ $record->id }}" title="Delete Record">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $postnatalRecords->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-child fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Postnatal Records</h5>
                                    <p class="text-muted mb-3">Add postnatal care records after delivery.</p>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createPostnatalModal">
                                        <i class="fas fa-plus"></i> Add First Record
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Postpartum Records Tab -->
                        <div class="tab-pane fade" id="postpartum" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">
                                    <i class="fas fa-female me-2 text-warning"></i>Postpartum Care Records
                                </h6>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#createPostpartumModal">
                                        <i class="fas fa-plus"></i> Quick Add
                                    </button>
                                    <a href="{{ route('employee.patients.postpartum-records', $patient->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-list"></i> View All
                                    </a>
                                </div>
                            </div>

                            @if(isset($postpartumRecords) && $postpartumRecords->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-compact table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Provider</th>
                                                <th>Weeks Postpartum</th>
                                                <th>Weight</th>
                                                <th>Blood Pressure</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($postpartumRecords as $record)
                                            <tr>
                                                <td>{{ $record->visit_date->format('M j, Y') }}</td>
                                                <td>{{ $record->provider->name ?? 'N/A' }}</td>
                                                <td>{{ $record->weeks_postpartum ?? 'N/A' }} weeks</td>
                                                <td>{{ $record->weight ? $record->weight . ' kg' : 'N/A' }}</td>
                                                <td>
                                                    @if($record->blood_pressure_systolic && $record->blood_pressure_diastolic)
                                                        {{ $record->blood_pressure_systolic }}/{{ $record->blood_pressure_diastolic }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-info btn-sm action-btn" data-action="view" data-type="postpartum" data-id="{{ $record->id }}" title="View Record">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                            </svg>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-warning btn-sm action-btn" data-action="edit" data-type="postpartum" data-id="{{ $record->id }}" title="Edit Record">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                                            </svg>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm action-btn" data-action="delete" data-type="postpartum" data-id="{{ $record->id }}" title="Delete Record">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $postpartumRecords->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-female fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Postpartum Records</h5>
                                    <p class="text-muted mb-3">Add postpartum care records for long-term follow-up.</p>
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#createPostpartumModal">
                                        <i class="fas fa-plus"></i> Add First Record
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Delivery Records Tab -->
                        <div class="tab-pane fade" id="delivery" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">
                                    <i class="fas fa-hospital me-2 text-danger"></i>Delivery Records
                                </h6>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#createDeliveryModal">
                                        <i class="fas fa-plus"></i> Quick Add
                                    </button>
                                    <a href="{{ route('employee.patients.delivery-records', $patient->id) }}" class="btn btn-sm btn-danger">
                                        <i class="fas fa-list"></i> View All
                                    </a>
                                </div>
                            </div>

                            @if(isset($deliveryRecords) && $deliveryRecords->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-compact table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Delivery Date</th>
                                                <th>Delivery Type</th>
                                                <th>Provider</th>
                                                <th>Newborn Gender</th>
                                                <th>Newborn Weight</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($deliveryRecords as $record)
                                            <tr>
                                                <td>{{ $record->delivery_date_time->format('M j, Y H:i') }}</td>
                                                <td>{{ $record->delivery_type ?? 'N/A' }}</td>
                                                <td>{{ $record->attendingProvider->name ?? 'N/A' }}</td>
                                                <td>{{ $record->newborn_gender ?? 'N/A' }}</td>
                                                <td>{{ $record->newborn_weight ? $record->newborn_weight . ' kg' : 'N/A' }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-info btn-sm action-btn" data-action="view" data-type="delivery" data-id="{{ $record->id }}" title="View Record">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                            </svg>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-warning btn-sm action-btn" data-action="edit" data-type="delivery" data-id="{{ $record->id }}" title="Edit Record">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                                            </svg>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm action-btn" data-action="delete" data-type="delivery" data-id="{{ $record->id }}" title="Delete Record">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $deliveryRecords->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-hospital fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Delivery Records</h5>
                                    <p class="text-muted mb-3">Add delivery records when the patient gives birth.</p>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#createDeliveryModal">
                                        <i class="fas fa-plus"></i> Add Delivery Record
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Prescriptions Tab -->
                        <div class="tab-pane fade" id="prescriptions" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">
                                    <i class="fas fa-prescription-bottle me-2 text-secondary"></i>Prescriptions
                                </h6>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#createPrescriptionModal">
                                        <i class="fas fa-plus"></i> Quick Add
                                    </button>
                                    <a href="{{ route('employee.patients.treatments', $patient->id) }}" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-list"></i> View All
                                    </a>
                                </div>
                            </div>

                            @if($treatments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-compact table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Prescribed Date</th>
                                                <th>Treatment</th>
                                                <th>Medication</th>
                                                <th>Dosage</th>
                                                <th>Provider</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($treatments as $treatment)
                                            <tr>
                                                <td>{{ $treatment->prescribed_date->format('M j, Y') }}</td>
                                                <td>
                                                    <strong>{{ $treatment->treatment_name }}</strong>
                                                    @if($treatment->treatment_type)
                                                        <br><small class="text-muted">{{ ucfirst($treatment->treatment_type) }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $treatment->generic_name ?? 'N/A' }}</td>
                                                <td>{{ $treatment->dosage ?? 'N/A' }}</td>
                                                <td>{{ $treatment->prescriber->name ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $treatment->priority === 'urgent' ? 'danger' : ($treatment->priority === 'stat' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst($treatment->priority ?? 'routine') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-info btn-sm action-btn" data-action="view" data-type="prescription" data-id="{{ $treatment->id }}" title="View Prescription">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                            </svg>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-warning btn-sm action-btn" data-action="edit" data-type="prescription" data-id="{{ $treatment->id }}" title="Edit Prescription">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                                            </svg>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm action-btn" data-action="delete" data-type="prescription" data-id="{{ $treatment->id }}" title="Delete Prescription">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $treatments->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-prescription-bottle fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Prescriptions</h5>
                                    <p class="text-muted mb-3">Start by adding the patient's first medication prescription.</p>
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#createPrescriptionModal">
                                        <i class="fas fa-plus"></i> Add First Prescription
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Laboratory Results Tab -->
                        <div class="tab-pane fade" id="lab" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">
                                    <i class="fas fa-flask me-2 text-info"></i>Laboratory Results
                                </h6>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#createLabModal">
                                        <i class="fas fa-plus"></i> Quick Add
                                    </button>
                                    <a href="{{ route('employee.patients.lab-results', $patient->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-list"></i> View All
                                    </a>
                                </div>
                            </div>

                            @if($labResults->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-compact table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Test Name</th>
                                                <th>Date Ordered</th>
                                                <th>Status</th>
                                                <th>Result</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($labResults as $result)
                                            <tr>
                                                <td>{{ $result->test_name }}</td>
                                                <td>{{ $result->test_ordered_date_time->format('M j, Y') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $result->getTestStatusBadgeClass() }}">
                                                        {{ ucfirst(str_replace('_', ' ', $result->test_status)) }}
                                                    </span>
                                                </td>
                                                <td>{{ $result->result_display ?? 'Pending' }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        @if($result->isCompleted())
                                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewRecord('lab', {{ $result->id }})" title="View Result">
                                                                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                                </svg>
                                                            </button>
                                                        @endif
                                                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="editRecord('lab', {{ $result->id }})" title="Edit Result">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                                            </svg>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteRecord('lab', {{ $result->id }})" title="Delete Result">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                            </svg>
                                                        </button>
                                                    </div>
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
                                    <i class="fas fa-flask fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Laboratory Results</h5>
                                    <p class="text-muted mb-3">Add laboratory test results and track patient diagnostics.</p>
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#createLabModal">
                                        <i class="fas fa-plus"></i> Add Lab Result
                                    </button>
                                </div>
                            @endif
                        </div>




                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals for Quick Actions -->
<!-- Prenatal Modal -->
<div class="modal fade" id="createPrenatalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Add Prenatal Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('employee.patients.store-prenatal-record', $patient->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Visit Date *</label>
                                <input type="date" name="visit_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Visit Time</label>
                                <input type="time" name="visit_time" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Attending Physician *</label>
                                <select name="attending_physician_id" class="form-select" required>
                                    <option value="">Select Physician</option>
                                    @foreach(\App\Models\User::where('user_type', 'employee')->get() as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Midwife</label>
                                <select name="midwife_id" class="form-select">
                                    <option value="">Select Midwife</option>
                                    @foreach(\App\Models\User::where('user_type', 'employee')->get() as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Blood Pressure</label>
                                <div class="input-group">
                                    <input type="number" name="blood_pressure_systolic" class="form-control" placeholder="Systolic">
                                    <span class="input-group-text">/</span>
                                    <input type="number" name="blood_pressure_diastolic" class="form-control" placeholder="Diastolic">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Weight (kg)</label>
                                <input type="number" step="0.1" name="weight_kg" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">General Notes</label>
                        <textarea name="general_notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Postnatal Modal -->
<div class="modal fade" id="createPostnatalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Add Postnatal Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('employee.patients.store-postnatal-record', $patient->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Visit Date *</label>
                                <input type="date" name="visit_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Provider *</label>
                                <select name="provider_id" class="form-select" required>
                                    <option value="">Select Provider</option>
                                    @foreach(\App\Models\User::where('user_type', 'employee')->get() as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Visit Number</label>
                                <input type="number" name="visit_number" class="form-control" value="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Days Postpartum</label>
                                <input type="number" name="days_postpartum" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Blood Pressure</label>
                                <div class="input-group">
                                    <input type="number" name="blood_pressure_systolic" class="form-control" placeholder="Systolic">
                                    <span class="input-group-text">/</span>
                                    <input type="number" name="blood_pressure_diastolic" class="form-control" placeholder="Diastolic">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Weight (kg)</label>
                                <input type="number" step="0.1" name="weight" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">General Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Postpartum Modal -->
<div class="modal fade" id="createPostpartumModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Add Postpartum Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('employee.patients.store-postpartum-record', $patient->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Visit Date *</label>
                                <input type="date" name="visit_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Provider *</label>
                                <select name="provider_id" class="form-select" required>
                                    <option value="">Select Provider</option>
                                    @foreach(\App\Models\User::where('user_type', 'employee')->get() as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Visit Number</label>
                                <input type="number" name="visit_number" class="form-control" value="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Weeks Postpartum</label>
                                <input type="number" name="weeks_postpartum" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Blood Pressure</label>
                                <div class="input-group">
                                    <input type="number" name="blood_pressure_systolic" class="form-control" placeholder="Systolic">
                                    <span class="input-group-text">/</span>
                                    <input type="number" name="blood_pressure_diastolic" class="form-control" placeholder="Diastolic">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Weight (kg)</label>
                                <input type="number" step="0.1" name="weight" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assessment Notes</label>
                        <textarea name="assessment_notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Save Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delivery Modal -->
<div class="modal fade" id="createDeliveryModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Add Delivery Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('employee.patients.store-delivery-record', $patient->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Admission Date & Time *</label>
                                <input type="datetime-local" name="admission_date_time" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Delivery Date & Time *</label>
                                <input type="datetime-local" name="delivery_date_time" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Attending Provider *</label>
                                <select name="attending_provider_id" class="form-select" required>
                                    <option value="">Select Provider</option>
                                    @foreach(\App\Models\User::where('user_type', 'employee')->get() as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Delivering Provider</label>
                                <select name="delivering_provider_id" class="form-select">
                                    <option value="">Select Provider</option>
                                    @foreach(\App\Models\User::where('user_type', 'employee')->get() as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Delivery Type</label>
                                <select name="delivery_type" class="form-select">
                                    <option value="">Select Type</option>
                                    <option value="vaginal">Vaginal</option>
                                    <option value="cesarean">Cesarean</option>
                                    <option value="forceps">Forceps</option>
                                    <option value="vacuum">Vacuum</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Delivery Place</label>
                                <input type="text" name="delivery_place" class="form-control" placeholder="Hospital/Clinic name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Newborn Gender</label>
                                <select name="newborn_gender" class="form-select">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Newborn Weight (kg)</label>
                                <input type="number" step="0.01" name="newborn_weight" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Delivery Summary</label>
                        <textarea name="delivery_summary" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Save Delivery Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Prescription Modal -->
<div class="modal fade" id="createPrescriptionModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Prescription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('employee.patients.store-treatment', $patient->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Treatment Type *</label>
                                <select name="treatment_type" class="form-select" required>
                                    <option value="">Select Type</option>
                                    <option value="medication">Medication</option>
                                    <option value="procedure">Procedure</option>
                                    <option value="therapy">Therapy</option>
                                    <option value="vaccination">Vaccination</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Priority *</label>
                                <select name="priority" class="form-select" required>
                                    <option value="routine">Routine</option>
                                    <option value="urgent">Urgent</option>
                                    <option value="stat">STAT</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Medicine from Inventory *</label>
                                <select name="inventory_id" class="form-select" required>
                                    <option value="">Select Medicine</option>
                                    {{-- ->where('requires_prescription', true) --}}
                                    @foreach(\App\Models\Inventory::active()->where('current_quantity', '>', 0)->get() as $medicine)
                                        <option value="{{ $medicine->id }}" data-stock="{{ $medicine->current_quantity }}" data-price="{{ $medicine->selling_price }}" data-generic="{{ $medicine->description }}">
                                            {{ $medicine->name }} (Stock: {{ $medicine->current_quantity }} {{ $medicine->unit_of_measure }})
                                            @if($medicine->selling_price)
                                                - ₱{{ number_format($medicine->selling_price, 2) }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Generic Name</label>
                                <input type="text" name="generic_name" class="form-control" placeholder="e.g., Acetaminophen" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Dosage *</label>
                                <input type="text" name="dosage" class="form-control" required placeholder="e.g., 500mg">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Frequency *</label>
                                <input type="text" name="frequency" class="form-control" required placeholder="e.g., 3 times daily">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Route *</label>
                                <select name="route" class="form-select" required>
                                    <option value="">Select Route</option>
                                    <option value="oral">Oral</option>
                                    <option value="intravenous">Intravenous (IV)</option>
                                    <option value="intramuscular">Intramuscular (IM)</option>
                                    <option value="subcutaneous">Subcutaneous</option>
                                    <option value="topical">Topical</option>
                                    <option value="inhalation">Inhalation</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Duration (Days)</label>
                                <input type="number" name="duration_days" class="form-control" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Quantity to Prescribe *</label>
                                <input type="number" name="quantity_dispensed" class="form-control" min="1" required>
                                <small class="form-text text-muted">Available stock will be shown when medicine is selected</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Prescriber *</label>
                                <select name="prescribed_by" class="form-select" required>
                                    <option value="">Select Provider</option>
                                    @foreach(\App\Models\User::where('user_type', 'employee')->get() as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Prescribed Date *</label>
                                <input type="date" name="prescribed_date" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Indication/Purpose *</label>
                        <textarea name="indication" class="form-control" rows="2" required placeholder="Why is this medication prescribed?"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosage Instructions</label>
                        <textarea name="special_instructions" class="form-control" rows="2" placeholder="Special instructions for taking this medication"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-secondary">Create Prescription</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lab Modal -->
<div class="modal fade" id="createLabModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Add Lab Result</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('employee.patients.store-lab-result', $patient->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Test Name *</label>
                                <input type="text" name="test_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Test Category *</label>
                                <select name="test_category" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <option value="hematology">Hematology</option>
                                    <option value="chemistry">Chemistry</option>
                                    <option value="microbiology">Microbiology</option>
                                    <option value="immunology">Immunology</option>
                                    <option value="urinalysis">Urinalysis</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ordered By *</label>
                                <select name="ordered_by" class="form-select" required>
                                    <option value="">Select Provider</option>
                                    @foreach(\App\Models\User::where('user_type', 'employee')->get() as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sample Collection Date *</label>
                                <input type="datetime-local" name="sample_collection_date_time" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Test Status *</label>
                                <select name="test_status" class="form-select" required>
                                    <option value="">Select Status</option>
                                    <option value="ordered">Ordered</option>
                                    <option value="collected">Collected</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Result Status</label>
                                <select name="result_status" class="form-select">
                                    <option value="">Select Result Status</option>
                                    <option value="normal">Normal</option>
                                    <option value="abnormal_high">Abnormal High</option>
                                    <option value="abnormal_low">Abnormal Low</option>
                                    <option value="critical_high">Critical High</option>
                                    <option value="critical_low">Critical Low</option>
                                    <option value="pending">Pending</option>
                                    <option value="inconclusive">Inconclusive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Clinical Indication</label>
                        <textarea name="clinical_indication" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Save Lab Result</button>
                </div>
            </form>
        </div>
    </div>
</div>


@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// Handle action button clicks using event delegation
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tabs
    var triggerTabList = [].slice.call(document.querySelectorAll('#medicalRecordsTabs button'))
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)

        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            tabTrigger.show()
        })
    })

    // Handle action button clicks
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('action-btn') || e.target.closest('.action-btn')) {
            const button = e.target.classList.contains('action-btn') ? e.target : e.target.closest('.action-btn');
            const action = button.getAttribute('data-action');
            const type = button.getAttribute('data-type');
            const id = button.getAttribute('data-id');

            if (action && type && id) {
                handleAction(action, type, id);
            }
        }
    });

    function handleAction(action, type, id) {
        const patientId = {{ $patient->id }};

        switch(action) {
            case 'view':
                viewRecord(type, id);
                break;
            case 'edit':
                editRecord(type, id);
                break;
            case 'delete':
                deleteRecord(type, id);
                break;
        }
    }

    // Handle medicine selection change
    document.querySelector('select[name="inventory_id"]').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const stock = selectedOption.getAttribute('data-stock');
        const genericName = selectedOption.getAttribute('data-generic') || '';

        // Update generic name field
        document.querySelector('input[name="generic_name"]').value = genericName;

        // Update quantity field max and placeholder
        const quantityField = document.querySelector('input[name="quantity_dispensed"]');
        if (stock) {
            quantityField.max = stock;
            quantityField.placeholder = `Max: ${stock} available`;
        }
    });

});

function viewRecord(type, id) {
    const patientId = {{ $patient->id }};
    switch(type) {
        case 'prenatal':
            window.location.href = `/employee/patients/${patientId}/prenatal-records/${id}`;
            break;
        case 'postnatal':
            window.location.href = `/employee/patients/${patientId}/postnatal-records/${id}`;
            break;
        case 'postpartum':
            window.location.href = `/employee/patients/${patientId}/postpartum-records/${id}`;
            break;
        case 'delivery':
            window.location.href = `/employee/patients/${patientId}/delivery-records/${id}`;
            break;
        case 'lab':
            window.location.href = `/employee/patients/${patientId}/lab-results/${id}`;
            break;
        case 'prescription':
            window.location.href = `/employee/patients/${patientId}/treatments/${id}`;
            break;
    }
}

function editRecord(type, id) {
    const patientId = {{ $patient->id }};
    switch(type) {
        case 'prenatal':
            window.location.href = `/employee/patients/${patientId}/prenatal-records/${id}/edit`;
            break;
        case 'postnatal':
            window.location.href = `/employee/patients/${patientId}/postnatal-records/${id}/edit`;
            break;
        case 'postpartum':
            window.location.href = `/employee/patients/${patientId}/postpartum-records/${id}/edit`;
            break;
        case 'delivery':
            window.location.href = `/employee/patients/${patientId}/delivery-records/${id}/edit`;
            break;
        case 'lab':
            window.location.href = `/employee/patients/${patientId}/lab-results/${id}/edit`;
            break;
        case 'prescription':
            window.location.href = `/employee/patients/${patientId}/treatments/${id}/edit`;
            break;
    }
}

function deleteRecord(type, id) {
    const patientId = {{ $patient->id }};
    let route = '';
    let message = '';

    switch(type) {
        case 'prenatal':
            route = `/employee/patients/${patientId}/prenatal-records/${id}`;
            message = 'Are you sure you want to delete this prenatal record?';
            break;
        case 'postnatal':
            route = `/employee/patients/${patientId}/postnatal-records/${id}`;
            message = 'Are you sure you want to delete this postnatal record?';
            break;
        case 'postpartum':
            route = `/employee/patients/${patientId}/postpartum-records/${id}`;
            message = 'Are you sure you want to delete this postpartum record?';
            break;
        case 'delivery':
            route = `/employee/patients/${patientId}/delivery-records/${id}`;
            message = 'Are you sure you want to delete this delivery record?';
            break;
        case 'lab':
            route = `/employee/patients/${patientId}/lab-results/${id}`;
            message = 'Are you sure you want to delete this lab result?';
            break;
        case 'prescription':
            route = `/employee/patients/${patientId}/treatments/${id}`;
            message = 'Are you sure you want to delete this prescription?';
            break;
    }

    if (confirm(message)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = route;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection

@endsection
