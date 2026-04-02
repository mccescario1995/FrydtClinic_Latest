@extends('employee.layouts.app')

@section('title', 'Prenatal Record Details - ' . $patient->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-baby mr-2"></i>
                        Prenatal Record Details for {{ $patient->name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('employee.patients.prenatal-records', $patient->id) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Records
                        </a>
                        <a href="{{ route('employee.patients.edit-prenatal-record', [$patient->id, $record->id]) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit Record
                        </a>
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-md text-primary me-2"></i>Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Attending Physician</label>
                                <p class="form-control-plaintext">{{ $record->attendingPhysician ? $record->attendingPhysician->name : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Midwife</label>
                                <p class="form-control-plaintext">{{ $record->midwife ? $record->midwife->name : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Visit Date</label>
                                <p class="form-control-plaintext">{{ $record->visit_date ? $record->visit_date->format('M d, Y') : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Visit Time</label>
                                <p class="form-control-plaintext">{{ $record->visit_time ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Times Visited</label>
                                <p class="form-control-plaintext">{{ $record->times_visited ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pregnancy Status</label>
                                <p class="form-control-plaintext"><span class="badge bg-success">{{ ucfirst($record->pregnancy_status ?? 'active') }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vital Signs -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-heartbeat text-danger me-2"></i>Vital Signs</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">BP Systolic</label>
                                <p class="form-control-plaintext">{{ $record->blood_pressure_systolic ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">BP Diastolic</label>
                                <p class="form-control-plaintext">{{ $record->blood_pressure_diastolic ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Weight (kg)</label>
                                <p class="form-control-plaintext">{{ $record->weight_kg ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Height (cm)</label>
                                <p class="form-control-plaintext">{{ $record->height_cm ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pulse Rate</label>
                                <p class="form-control-plaintext">{{ $record->pulse_rate ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Respiratory Rate</label>
                                <p class="form-control-plaintext">{{ $record->respiratory_rate ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Temperature (°C)</label>
                                <p class="form-control-plaintext">{{ $record->temperature_celsius ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">BMI</label>
                                <p class="form-control-plaintext">{{ $record->bmi ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fetal Assessment -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-stethoscope text-purple me-2"></i>Fetal Assessment</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Fetal Heart Rate</label>
                                <p class="form-control-plaintext">{{ $record->fetal_heart_rate ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Fetal Position</label>
                                <p class="form-control-plaintext">{{ $record->fetal_position ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Fetal Presentation</label>
                                <p class="form-control-plaintext">{{ $record->fetal_presentation ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Fundal Height (cm)</label>
                                <p class="form-control-plaintext">{{ $record->fundal_height_cm ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Laboratory Results -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-flask text-teal me-2"></i>Laboratory Results</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Blood Type</label>
                                <p class="form-control-plaintext">{{ $record->blood_type ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Hemoglobin</label>
                                <p class="form-control-plaintext">{{ $record->hemoglobin_level ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Hematocrit</label>
                                <p class="form-control-plaintext">{{ $record->hematocrit_level ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Urinalysis</label>
                                <p class="form-control-plaintext">{{ $record->urinalysis ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">VDRL Test</label>
                                <p class="form-control-plaintext">{{ $record->vdrl_test ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">HBsAg Test</label>
                                <p class="form-control-plaintext">{{ $record->hbsag_test ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pregnancy Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-baby text-success me-2"></i>Pregnancy Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Last Menstrual Period</label>
                                <p class="form-control-plaintext">{{ $record->last_menstrual_period ? $record->last_menstrual_period->format('M d, Y') : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Estimated Due Date</label>
                                <p class="form-control-plaintext">{{ $record->estimated_due_date ? $record->estimated_due_date->format('M d, Y') : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Gravida</label>
                                <p class="form-control-plaintext">{{ $record->gravida ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Para</label>
                                <p class="form-control-plaintext">{{ $record->para ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Gestational Age (Weeks)</label>
                                <p class="form-control-plaintext">{{ $record->gestational_age_weeks ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Gestational Age (Days)</label>
                                <p class="form-control-plaintext">{{ $record->gestational_age_days ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Living Children</label>
                                <p class="form-control-plaintext">{{ $record->living_children ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Risk Assessment -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Risk Assessment</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Risk Level</label>
                                <p class="form-control-plaintext">
                                    @if($record->risk_level)
                                        <span class="badge bg-{{ $record->risk_level == 'high' ? 'danger' : ($record->risk_level == 'moderate' ? 'warning' : 'success') }}">{{ ucfirst($record->risk_level) }}</span>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Risk Factors</label>
                                <p class="form-control-plaintext">{{ $record->risk_factors ?? 'None' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Complications</label>
                                <p class="form-control-plaintext">{{ $record->complications ?? 'None' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pregnancy Timeline -->
            @if($record->last_menstrual_period || $record->estimated_due_date || $record->gestational_age_weeks || $record->gestational_age_days)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-check text-primary me-2"></i>Pregnancy Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Last Menstrual Period</h6>
                                <p class="h5 text-primary">{{ $record->last_menstrual_period ? \Carbon\Carbon::parse($record->last_menstrual_period)->format('M d, Y') : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Estimated Due Date</h6>
                                <p class="h5 text-success">{{ $record->estimated_due_date ? \Carbon\Carbon::parse($record->estimated_due_date)->format('M d, Y') : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Gestational Age</h6>
                                <p class="h5 text-info">
                                    {{ $record->gestational_age_weeks ? $record->gestational_age_weeks . ' weeks' : 'N/A' }}
                                    {{ $record->gestational_age_days ? $record->gestational_age_days . ' days' : '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Immunization & Supplements -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-syringe text-pink me-2"></i>Immunization & Supplements</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">TD Vaccine Given</label>
                                <p class="form-control-plaintext">
                                    @if($record->td_vaccine_given)
                                        <i class="fas fa-check text-success me-1"></i>Yes
                                        @if($record->td_vaccine_date)
                                            ({{ $record->td_vaccine_date->format('M d, Y') }})
                                        @endif
                                        @if($record->td_vaccine_dose)
                                            - Dose: {{ $record->td_vaccine_dose }}
                                        @endif
                                    @else
                                        No
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Iron Supplements</label>
                                <p class="form-control-plaintext">
                                    @if($record->iron_supplements)
                                        <i class="fas fa-check text-success me-1"></i>Yes
                                    @else
                                        No
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Calcium Supplements</label>
                                <p class="form-control-plaintext">
                                    @if($record->calcium_supplements)
                                        <i class="fas fa-check text-success me-1"></i>Yes
                                    @else
                                        No
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Vitamin Supplements</label>
                                <p class="form-control-plaintext">
                                    @if($record->vitamin_supplements)
                                        <i class="fas fa-check text-success me-1"></i>Yes
                                    @else
                                        No
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medications & Counseling -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-pills text-info me-2"></i>Medications & Counseling</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Medications</label>
                                <p class="form-control-plaintext">{{ $record->medications ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Counseling Topics</label>
                                <p class="form-control-plaintext">{{ $record->counseling_topics ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Patient Education</label>
                                <p class="form-control-plaintext">{{ $record->patient_education ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Next Visit & Notes -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-check text-secondary me-2"></i>Next Visit & Notes</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Next Visit Date</label>
                                <p class="form-control-plaintext">{{ $record->next_visit_date ? $record->next_visit_date->format('M d, Y') : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Next Visit Notes</label>
                                <p class="form-control-plaintext">{{ $record->next_visit_notes ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">General Notes</label>
                                <p class="form-control-plaintext">{{ $record->general_notes ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Physician Notes</label>
                                <p class="form-control-plaintext">{{ $record->physician_notes ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
