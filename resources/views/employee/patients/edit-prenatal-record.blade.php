@extends('employee.layouts.app')

@section('title', 'Edit Prenatal Record - ' . $patient->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Prenatal Record for {{ $patient->name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('employee.patients.prenatal-records', $patient->id) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Records
                        </a>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('employee.patients.update-prenatal-record', [$patient->id, $record->id]) }}">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-md text-primary me-2"></i>Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="attending_physician_id" class="form-label">Attending Physician <span class="text-danger">*</span></label>
                                    <select id="attending_physician_id" name="attending_physician_id" class="form-select" required>
                                        <option value="">Select Physician</option>
                                        @foreach($providers as $provider)
                                            <option value="{{ $provider->id }}" {{ old('attending_physician_id', $record->attending_physician_id) == $provider->id ? 'selected' : '' }}>
                                                {{ $provider->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('attending_physician_id'))
                                        <div class="text-danger small mt-1">{{ $errors->first('attending_physician_id') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="midwife_id" class="form-label">Midwife</label>
                                    <select id="midwife_id" name="midwife_id" class="form-select">
                                        <option value="">Select Midwife (Optional)</option>
                                        @foreach($providers as $provider)
                                            <option value="{{ $provider->id }}" {{ old('midwife_id', $record->midwife_id) == $provider->id ? 'selected' : '' }}>
                                                {{ $provider->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="visit_date" class="form-label">Visit Date <span class="text-danger">*</span></label>
                                    <input type="date" id="visit_date" name="visit_date" class="form-control" value="{{ old('visit_date', $record->visit_date ? $record->visit_date->format('Y-m-d') : '') }}" required>
                                    @if($errors->has('visit_date'))
                                        <div class="text-danger small mt-1">{{ $errors->first('visit_date') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="visit_time" class="form-label">Visit Time</label>
                                    <input type="time" id="visit_time" name="visit_time" class="form-control" value="{{ old('visit_time', $record->visit_time ? $record->visit_time->format('H:i') : '') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="times_visited" class="form-label">Times Visited</label>
                                    <input type="number" id="times_visited" name="times_visited" class="form-control" value="{{ old('times_visited', $record->times_visited ?? 1) }}" min="1">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="pregnancy_status" class="form-label">Pregnancy Status</label>
                                    <select id="pregnancy_status" name="pregnancy_status" class="form-select">
                                        <option value="active" {{ old('pregnancy_status', $record->pregnancy_status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="completed" {{ old('pregnancy_status', $record->pregnancy_status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="terminated" {{ old('pregnancy_status', $record->pregnancy_status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                                    </select>
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
                                    <label for="last_menstrual_period" class="form-label">Last Menstrual Period</label>
                                    <input type="date" id="last_menstrual_period" name="last_menstrual_period" class="form-control" value="{{ old('last_menstrual_period', $record->last_menstrual_period ? $record->last_menstrual_period->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="estimated_due_date" class="form-label">Estimated Due Date</label>
                                    <input type="date" id="estimated_due_date" name="estimated_due_date" class="form-control" value="{{ old('estimated_due_date', $record->estimated_due_date ? $record->estimated_due_date->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="gravida" class="form-label">Gravida</label>
                                    <input type="number" id="gravida" name="gravida" class="form-control" value="{{ old('gravida', $record->gravida ?? 1) }}" min="1">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="para" class="form-label">Para</label>
                                    <input type="number" id="para" name="para" class="form-control" value="{{ old('para', $record->para ?? 0) }}" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="gestational_age_weeks" class="form-label">Gestational Age (Weeks)</label>
                                    <input type="number" id="gestational_age_weeks" name="gestational_age_weeks" class="form-control" value="{{ old('gestational_age_weeks', $record->gestational_age_weeks) }}" min="0" max="42">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="gestational_age_days" class="form-label">Gestational Age (Days)</label>
                                    <input type="number" id="gestational_age_days" name="gestational_age_days" class="form-control" value="{{ old('gestational_age_days', $record->gestational_age_days) }}" min="0" max="6">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="living_children" class="form-label">Living Children</label>
                                    <input type="number" id="living_children" name="living_children" class="form-control" value="{{ old('living_children', $record->living_children ?? 0) }}" min="0">
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
                                    <label for="blood_pressure_systolic" class="form-label">BP Systolic</label>
                                    <input type="number" id="blood_pressure_systolic" name="blood_pressure_systolic" class="form-control" value="{{ old('blood_pressure_systolic', $record->blood_pressure_systolic) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="blood_pressure_diastolic" class="form-label">BP Diastolic</label>
                                    <input type="number" id="blood_pressure_diastolic" name="blood_pressure_diastolic" class="form-control" value="{{ old('blood_pressure_diastolic', $record->blood_pressure_diastolic) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="weight_kg" class="form-label">Weight (kg)</label>
                                    <input type="number" id="weight_kg" name="weight_kg" class="form-control" value="{{ old('weight_kg', $record->weight_kg) }}" step="0.1" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="height_cm" class="form-label">Height (cm)</label>
                                    <input type="number" id="height_cm" name="height_cm" class="form-control" value="{{ old('height_cm', $record->height_cm) }}" step="0.1" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="pulse_rate" class="form-label">Pulse Rate</label>
                                    <input type="number" id="pulse_rate" name="pulse_rate" class="form-control" value="{{ old('pulse_rate', $record->pulse_rate) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="respiratory_rate" class="form-label">Respiratory Rate</label>
                                    <input type="number" id="respiratory_rate" name="respiratory_rate" class="form-control" value="{{ old('respiratory_rate', $record->respiratory_rate) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="temperature_celsius" class="form-label">Temperature (°C)</label>
                                    <input type="number" id="temperature_celsius" name="temperature_celsius" class="form-control" value="{{ old('temperature_celsius', $record->temperature_celsius) }}" step="0.1" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bmi" class="form-label">BMI</label>
                                    <input type="number" id="bmi" name="bmi" class="form-control" value="{{ old('bmi', $record->bmi) }}" step="0.1" min="0">
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
                                    <label for="fetal_heart_rate" class="form-label">Fetal Heart Rate</label>
                                    <input type="number" id="fetal_heart_rate" name="fetal_heart_rate" class="form-control" value="{{ old('fetal_heart_rate', $record->fetal_heart_rate) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="fetal_position" class="form-label">Fetal Position</label>
                                    <input type="text" id="fetal_position" name="fetal_position" class="form-control" value="{{ old('fetal_position', $record->fetal_position) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="fetal_presentation" class="form-label">Fetal Presentation</label>
                                    <input type="text" id="fetal_presentation" name="fetal_presentation" class="form-control" value="{{ old('fetal_presentation', $record->fetal_presentation) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="fundal_height_cm" class="form-label">Fundal Height (cm)</label>
                                    <input type="number" id="fundal_height_cm" name="fundal_height_cm" class="form-control" value="{{ old('fundal_height_cm', $record->fundal_height_cm) }}" step="0.1" min="0">
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
                                    <label for="blood_type" class="form-label">Blood Type</label>
                                    <select id="blood_type" name="blood_type" class="form-select">
                                        <option value="">Select Blood Type</option>
                                        <option value="A+" {{ old('blood_type', $record->blood_type) == 'A+' ? 'selected' : '' }}>A+</option>
                                        <option value="A-" {{ old('blood_type', $record->blood_type) == 'A-' ? 'selected' : '' }}>A-</option>
                                        <option value="B+" {{ old('blood_type', $record->blood_type) == 'B+' ? 'selected' : '' }}>B+</option>
                                        <option value="B-" {{ old('blood_type', $record->blood_type) == 'B-' ? 'selected' : '' }}>B-</option>
                                        <option value="AB+" {{ old('blood_type', $record->blood_type) == 'AB+' ? 'selected' : '' }}>AB+</option>
                                        <option value="AB-" {{ old('blood_type', $record->blood_type) == 'AB-' ? 'selected' : '' }}>AB-</option>
                                        <option value="O+" {{ old('blood_type', $record->blood_type) == 'O+' ? 'selected' : '' }}>O+</option>
                                        <option value="O-" {{ old('blood_type', $record->blood_type) == 'O-' ? 'selected' : '' }}>O-</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="hemoglobin_level" class="form-label">Hemoglobin</label>
                                    <input type="text" id="hemoglobin_level" name="hemoglobin_level" class="form-control" value="{{ old('hemoglobin_level', $record->hemoglobin_level) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="hematocrit_level" class="form-label">Hematocrit</label>
                                    <input type="text" id="hematocrit_level" name="hematocrit_level" class="form-control" value="{{ old('hematocrit_level', $record->hematocrit_level) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="urinalysis" class="form-label">Urinalysis</label>
                                    <input type="text" id="urinalysis" name="urinalysis" class="form-control" value="{{ old('urinalysis', $record->urinalysis) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="vdrl_test" class="form-label">VDRL Test</label>
                                    <input type="text" id="vdrl_test" name="vdrl_test" class="form-control" value="{{ old('vdrl_test', $record->vdrl_test) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="hbsag_test" class="form-label">HBsAg Test</label>
                                    <input type="text" id="hbsag_test" name="hbsag_test" class="form-control" value="{{ old('hbsag_test', $record->hbsag_test) }}">
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
                                    <label for="risk_level" class="form-label">Risk Level</label>
                                    <select id="risk_level" name="risk_level" class="form-select">
                                        <option value="low" {{ old('risk_level', $record->risk_level ?? 'low') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="moderate" {{ old('risk_level', $record->risk_level) == 'moderate' ? 'selected' : '' }}>Moderate</option>
                                        <option value="high" {{ old('risk_level', $record->risk_level) == 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="risk_factors" class="form-label">Risk Factors</label>
                                    <textarea id="risk_factors" name="risk_factors" class="form-control" rows="3">{{ old('risk_factors', $record->risk_factors) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="complications" class="form-label">Complications</label>
                                    <textarea id="complications" name="complications" class="form-control" rows="3">{{ old('complications', $record->complications) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Immunization & Supplements -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-syringe text-pink me-2"></i>Immunization & Supplements</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" id="td_vaccine_given" name="td_vaccine_given" value="1" class="form-check-input" {{ old('td_vaccine_given', $record->td_vaccine_given) ? 'checked' : '' }}>
                                        <label for="td_vaccine_given" class="form-check-label">TD Vaccine Given</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="td_vaccine_date" class="form-label">TD Vaccine Date</label>
                                    <input type="date" id="td_vaccine_date" name="td_vaccine_date" class="form-control" value="{{ old('td_vaccine_date', $record->td_vaccine_date ? $record->td_vaccine_date->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="td_vaccine_dose" class="form-label">TD Vaccine Dose</label>
                                    <input type="number" id="td_vaccine_dose" name="td_vaccine_dose" class="form-control" value="{{ old('td_vaccine_dose', $record->td_vaccine_dose) }}" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" id="iron_supplements" name="iron_supplements" value="1" class="form-check-input" {{ old('iron_supplements', $record->iron_supplements) ? 'checked' : '' }}>
                                        <label for="iron_supplements" class="form-check-label">Iron Supplements</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" id="calcium_supplements" name="calcium_supplements" value="1" class="form-check-input" {{ old('calcium_supplements', $record->calcium_supplements) ? 'checked' : '' }}>
                                        <label for="calcium_supplements" class="form-check-label">Calcium Supplements</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" id="vitamin_supplements" name="vitamin_supplements" value="1" class="form-check-input" {{ old('vitamin_supplements', $record->vitamin_supplements) ? 'checked' : '' }}>
                                        <label for="vitamin_supplements" class="form-check-label">Vitamin Supplements</label>
                                    </div>
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
                                    <label for="medications" class="form-label">Medications</label>
                                    <textarea id="medications" name="medications" class="form-control" rows="3">{{ old('medications', $record->medications) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="counseling_topics" class="form-label">Counseling Topics</label>
                                    <textarea id="counseling_topics" name="counseling_topics" class="form-control" rows="3">{{ old('counseling_topics', $record->counseling_topics) }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="patient_education" class="form-label">Patient Education</label>
                                    <textarea id="patient_education" name="patient_education" class="form-control" rows="3">{{ old('patient_education', $record->patient_education) }}</textarea>
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
                                    <label for="next_visit_date" class="form-label">Next Visit Date</label>
                                    <input type="date" id="next_visit_date" name="next_visit_date" class="form-control" value="{{ old('next_visit_date', $record->next_visit_date ? $record->next_visit_date->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="next_visit_notes" class="form-label">Next Visit Notes</label>
                                    <input type="text" id="next_visit_notes" name="next_visit_notes" class="form-control" value="{{ old('next_visit_notes', $record->next_visit_notes) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="general_notes" class="form-label">General Notes</label>
                                    <textarea id="general_notes" name="general_notes" class="form-control" rows="3">{{ old('general_notes', $record->general_notes) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="physician_notes" class="form-label">Physician Notes</label>
                                    <textarea id="physician_notes" name="physician_notes" class="form-control" rows="3">{{ old('physician_notes', $record->physician_notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="{{ route('employee.patients.prenatal-records', $patient->id) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Prenatal Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
