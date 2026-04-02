@extends('employee.layouts.app')

@section('title', 'Postnatal Record Details - ' . $patient->name)

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
        border-left: 4px solid #28a745;
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
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Card -->
            <div class="card record-card mb-4">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-child me-2"></i>
                                Postnatal Record Details
                            </h4>
                            <small class="text-white-50">Patient: {{ $patient->name }}</small>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('employee.patients.postnatal-records', $patient->id) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Back to Records
                            </a>
                            <a href="{{ route('employee.patients.edit-postnatal-record', [$patient->id, $record->id]) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>Edit Record
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Info Grid -->
            <div class="info-grid">
                <div class="info-item">
                    <h6><i class="fas fa-calendar-alt text-success me-1"></i>Visit Date</h6>
                    <p>{{ $record->visit_date->format('M d, Y') }}</p>
                </div>
                <div class="info-item">
                    <h6><i class="fas fa-hashtag text-primary me-1"></i>Visit Number</h6>
                    <p>{{ $record->visit_number }}</p>
                </div>
                <div class="info-item">
                    <h6><i class="fas fa-clock text-warning me-1"></i>Days Postpartum</h6>
                    <p>{{ $record->days_postpartum ?? 'N/A' }} days</p>
                </div>
                <div class="info-item">
                    <h6><i class="fas fa-calendar-week text-info me-1"></i>Weeks Postpartum</h6>
                    <p>{{ $record->weeks_postpartum ?? 'N/A' }} weeks</p>
                </div>
                <div class="info-item">
                    <h6><i class="fas fa-user-md text-secondary me-1"></i>Provider</h6>
                    <p>{{ $record->provider ? $record->provider->name : 'N/A' }}</p>
                </div>
            </div>

            <div class="row">
                <!-- Vital Signs -->
                <div class="col-lg-6">
                    <div class="card record-card">
                        <div class="card-header">
                            <h5 class="section-title mb-0">
                                <i class="fas fa-heartbeat text-primary me-2"></i>Vital Signs
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table compact-table table-borderless">
                                <tr>
                                    <th width="45%">Weight:</th>
                                    <td><strong>{{ $record->weight ? $record->weight . ' kg' : 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Blood Pressure:</th>
                                    <td>
                                        @if($record->blood_pressure_systolic && $record->blood_pressure_diastolic)
                                            <strong>{{ $record->blood_pressure_systolic }}/{{ $record->blood_pressure_diastolic }}</strong> mmHg
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Heart Rate:</th>
                                    <td><strong>{{ $record->heart_rate ? $record->heart_rate . ' bpm' : 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Temperature:</th>
                                    <td><strong>{{ $record->temperature ? $record->temperature . ' °C' : 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Respiratory Rate:</th>
                                    <td><strong>{{ $record->respiratory_rate ? $record->respiratory_rate . ' breaths/min' : 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Oxygen Saturation:</th>
                                    <td><strong>{{ $record->oxygen_saturation ? $record->oxygen_saturation . '%' : 'N/A' }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Physical Assessment -->
                <div class="col-lg-6">
                    <div class="card record-card">
                        <div class="card-header">
                            <h5 class="section-title mb-0">
                                <i class="fas fa-female text-info me-2"></i>Physical Assessment
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table compact-table table-borderless">
                                <tr>
                                    <th width="45%">General Condition:</th>
                                    <td><strong>{{ $record->general_condition ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Breast Condition:</th>
                                    <td><strong>{{ $record->breast_condition ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Uterus Condition:</th>
                                    <td><strong>{{ $record->uterus_condition ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Perineum Condition:</th>
                                    <td><strong>{{ $record->perineum_condition ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Lochia Condition:</th>
                                    <td><strong>{{ $record->lochia_condition ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Episiotomy Condition:</th>
                                    <td><strong>{{ $record->episiotomy_condition ?? 'N/A' }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Breastfeeding & Newborn -->
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="card record-card">
                        <div class="card-header">
                            <h5 class="section-title mb-0">
                                <i class="fas fa-baby text-warning me-2"></i>Breastfeeding & Newborn
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table compact-table table-borderless">
                                <tr>
                                    <th width="45%">Breastfeeding Status:</th>
                                    <td><strong>{{ $record->breastfeeding_status ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Latch Assessment:</th>
                                    <td><strong>{{ $record->latch_assessment ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Newborn Check:</th>
                                    <td>
                                        @if($record->newborn_check)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Newborn Weight:</th>
                                    <td><strong>{{ $record->newborn_weight ? $record->newborn_weight . ' kg' : 'N/A' }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Follow-up & Planning -->
                <div class="col-lg-6">
                    <div class="card record-card">
                        <div class="card-header">
                            <h5 class="section-title mb-0">
                                <i class="fas fa-calendar-check text-secondary me-2"></i>Follow-up & Planning
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table compact-table table-borderless">
                                <tr>
                                    <th width="45%">Family Planning Method:</th>
                                    <td><strong>{{ $record->family_planning_method ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Follow-up Date:</th>
                                    <td><strong>{{ $record->follow_up_date ? $record->follow_up_date->format('M d, Y') : 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Next Visit Type:</th>
                                    <td><strong>{{ $record->next_visit_type ?? 'N/A' }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Notes -->
            @if($record->chief_complaint || $record->breastfeeding_notes || $record->newborn_notes || $record->assessment || $record->plan || $record->instructions_given || $record->notes)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card record-card">
                        <div class="card-header">
                            <h5 class="section-title mb-0">
                                <i class="fas fa-file-alt text-muted me-2"></i>Detailed Notes
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($record->chief_complaint)
                                <div class="col-md-6">
                                    <h6>Chief Complaint</h6>
                                    <p class="text-muted">{{ $record->chief_complaint }}</p>
                                </div>
                                @endif
                                @if($record->breastfeeding_notes)
                                <div class="col-md-6">
                                    <h6>Breastfeeding Notes</h6>
                                    <p class="text-muted">{{ $record->breastfeeding_notes }}</p>
                                </div>
                                @endif
                                @if($record->newborn_notes)
                                <div class="col-md-6">
                                    <h6>Newborn Notes</h6>
                                    <p class="text-muted">{{ $record->newborn_notes }}</p>
                                </div>
                                @endif
                                @if($record->assessment)
                                <div class="col-md-6">
                                    <h6>Assessment</h6>
                                    <p class="text-muted">{{ $record->assessment }}</p>
                                </div>
                                @endif
                                @if($record->plan)
                                <div class="col-md-6">
                                    <h6>Plan</h6>
                                    <p class="text-muted">{{ $record->plan }}</p>
                                </div>
                                @endif
                                @if($record->instructions_given)
                                <div class="col-md-6">
                                    <h6>Instructions Given</h6>
                                    <p class="text-muted">{{ $record->instructions_given }}</p>
                                </div>
                                @endif
                                @if($record->notes)
                                <div class="col-md-12">
                                    <h6>Additional Notes</h6>
                                    <div class="bg-light p-3 rounded">
                                        {{ $record->notes }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
