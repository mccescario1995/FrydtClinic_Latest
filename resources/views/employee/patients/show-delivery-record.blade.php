@extends('employee.layouts.app')

@section('title', 'Delivery Record Details - ' . $patient->name)

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
        border-left: 4px solid #dc3545;
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
    .timeline-item {
        position: relative;
        padding-left: 2rem;
        margin-bottom: 1rem;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 0.5rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #007bff;
    }
    .timeline-item::after {
        content: '';
        position: absolute;
        left: 0.9rem;
        top: 1.5rem;
        width: 2px;
        height: calc(100% - 1rem);
        background: #e9ecef;
    }
    .timeline-item:last-child::after {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-hospital mr-2"></i>
                        Delivery Record Details for {{ $patient->name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('employee.patients.delivery-records', $patient->id) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Records
                        </a>
                        <a href="{{ route('employee.patients.edit-delivery-record', [$patient->id, $record->id]) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-eye"></i> Edit Record
                        </a>
                    </div>
                </div>
            </div>

            <!-- Providers -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-md text-primary me-2"></i>Providers</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Attending Provider</label>
                                <p class="mb-0">{{ $record->attendingProvider ? $record->attendingProvider->name : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Delivering Provider</label>
                                <p class="mb-0">{{ $record->deliveringProvider ? $record->deliveringProvider->name : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Anesthesiologist</label>
                                <p class="mb-0">{{ $record->anesthesiologist ? $record->anesthesiologist->name : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt text-danger me-2"></i>Delivery Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Admission Date & Time</label>
                                <p class="mb-0">{{ $record->admission_date_time ? \Carbon\Carbon::parse($record->admission_date_time)->format('M d, Y H:i') : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Labor Onset Date & Time</label>
                                <p class="mb-0">{{ $record->labor_onset_date_time ? \Carbon\Carbon::parse($record->labor_onset_date_time)->format('M d, Y H:i') : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Rupture of Membranes Date & Time</label>
                                <p class="mb-0">{{ $record->rupture_of_membranes_date_time ? \Carbon\Carbon::parse($record->rupture_of_membranes_date_time)->format('M d, Y H:i') : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Rupture of Membranes Type</label>
                                <p class="mb-0">{{ $record->rupture_of_membranes_type ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Delivery Date & Time</label>
                                <p class="mb-0">{{ $record->delivery_date_time ? \Carbon\Carbon::parse($record->delivery_date_time)->format('M d, Y H:i') : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Delivery Type</label>
                                <p class="mb-0">{{ $record->delivery_type ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Delivery Place</label>
                                <p class="mb-0">{{ $record->delivery_place ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prenatal History -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history text-warning me-2"></i>Prenatal History</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Gravida</label>
                                <p class="mb-0">{{ $record->gravida ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Para</label>
                                <p class="mb-0">{{ $record->para ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Living Children</label>
                                <p class="mb-0">{{ $record->living_children ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Prenatal History</label>
                                <p class="mb-0">{{ $record->prenatal_history ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Risk Factors</label>
                                <p class="mb-0">{{ $record->risk_factors ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Labor Progress -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clock text-info me-2"></i>Labor Progress</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Labor Duration Hours</label>
                                <p class="mb-0">{{ $record->labor_duration_hours ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Labor Duration Minutes</label>
                                <p class="mb-0">{{ $record->labor_duration_minutes ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Presentation</label>
                                <p class="mb-0">{{ $record->presentation ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Position</label>
                                <p class="mb-0">{{ $record->position ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Labor Progress</label>
                                <p class="mb-0">{{ $record->labor_progress ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Labor Complications</label>
                                <p class="mb-0">{{ $record->labor_complications ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Details -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-baby text-success me-2"></i>Delivery Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Episiotomy Performed</label>
                                <p class="mb-0">{{ $record->episiotomy_performed ? 'Yes' : 'No' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Episiotomy Degree</label>
                                <p class="mb-0">{{ $record->episiotomy_degree ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Anesthesia Type</label>
                                <p class="mb-0">{{ $record->anesthesia_type ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Perineal Tear</label>
                                <p class="mb-0">{{ $record->perineal_tear ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Delivery Complications</label>
                                <p class="mb-0">{{ $record->delivery_complications ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Anesthesia Notes</label>
                                <p class="mb-0">{{ $record->anesthesia_notes ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Newborn Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-baby-carriage text-primary me-2"></i>Newborn Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Newborn Gender</label>
                                <p class="mb-0">{{ $record->newborn_gender ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Newborn Weight (kg)</label>
                                <p class="mb-0">{{ $record->newborn_weight ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Newborn Length (cm)</label>
                                <p class="mb-0">{{ $record->newborn_length ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">APGAR Score 1min</label>
                                <p class="mb-0">{{ $record->newborn_apgar_1min ?? 'N/A' }}/10</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">APGAR Score 5min</label>
                                <p class="mb-0">{{ $record->newborn_apgar_5min ?? 'N/A' }}/10</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">APGAR Score 10min</label>
                                <p class="mb-0">{{ $record->newborn_apgar_10min ?? 'N/A' }}/10</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Newborn Condition</label>
                                <p class="mb-0">{{ $record->newborn_condition ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Newborn Complications</label>
                                <p class="mb-0">{{ $record->newborn_complications ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-alt text-muted me-2"></i>Additional Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Estimated Blood Loss (mL)</label>
                                <p class="mb-0">{{ $record->estimated_blood_loss ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Blood Pressure Systolic</label>
                                <p class="mb-0">{{ $record->blood_pressure_systolic ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Blood Pressure Diastolic</label>
                                <p class="mb-0">{{ $record->blood_pressure_diastolic ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Heart Rate (bpm)</label>
                                <p class="mb-0">{{ $record->heart_rate ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Temperature (°C)</label>
                                <p class="mb-0">{{ $record->temperature ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Placenta Delivery</label>
                                <p class="mb-0">{{ $record->placenta_delivery ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Placenta Complete</label>
                                <p class="mb-0">{{ $record->placenta_complete ? 'Yes' : 'No' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Placenta Notes</label>
                                <p class="mb-0">{{ $record->placenta_notes ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-notes-medical text-secondary me-2"></i>Notes & Instructions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Postpartum Care</label>
                                <p class="mb-0">{{ $record->postpartum_care ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Medications Administered</label>
                                <p class="mb-0">{{ $record->medications_administered ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Breastfeeding Initiation</label>
                                <p class="mb-0">{{ $record->breastfeeding_initiation ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Expected Discharge Date</label>
                                <p class="mb-0">{{ $record->expected_discharge_date ? \Carbon\Carbon::parse($record->expected_discharge_date)->format('M d, Y') : 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Discharge Instructions</label>
                                <p class="mb-0">{{ $record->discharge_instructions ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Follow-up Instructions</label>
                                <p class="mb-0">{{ $record->follow_up_instructions ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Delivery Summary</label>
                                <p class="mb-0">{{ $record->delivery_summary ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Additional Notes</label>
                                <p class="mb-0">{{ $record->additional_notes ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-3 mt-4">
                <a href="{{ route('employee.patients.delivery-records', $patient->id) }}" class="btn btn-secondary">Back to Records</a>
                <a href="{{ route('employee.patients.edit-delivery-record', [$patient->id, $record->id]) }}" class="btn btn-primary">Edit Record</a>
            </div>
        </div>
    </div>
</div>
@endsection
