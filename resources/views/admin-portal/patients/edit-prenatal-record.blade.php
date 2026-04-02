@extends('admin-portal.layouts.app')

@section('title', 'Edit Prenatal Record')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0"><i class="fas fa-baby me-2"></i>Edit Prenatal Record</h1>
                <p class="text-muted mb-0">Update prenatal care record for {{ $patient->name }}</p>
            </div>
            <div>
                <a href="{{ route('admin-portal.patients.prenatal-records', $patient->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Records
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Patient Information Card -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Patient Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Patient Name</small>
                            <strong>{{ $patient->name }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Email</small>
                            <strong>{{ $patient->email }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Phone</small>
                            <strong>{{ $patient->patientProfile->phone ?? 'Not provided' }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Birth Date</small>
                            <strong>{{ $patient->patientProfile->birth_date ? $patient->patientProfile->birth_date->format('M d, Y') : 'Not provided' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Prenatal Record Form -->
<div class="row">
    <div class="col-md-12">
        <form method="POST" action="{{ route('admin-portal.patients.update-prenatal-record', [$patient->id, $record->id]) }}">
            @csrf
            @method('PUT')

            <!-- Visit Information -->
            <div class="admin-card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Visit Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="attending_physician_id" class="form-label">Attending Physician <span class="text-danger">*</span></label>
                            <select class="form-select" id="attending_physician_id" name="attending_physician_id" required>
                                <option value="">Select Physician</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}" {{ $record->attending_physician_id == $provider->id ? 'selected' : '' }}>{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="midwife_id" class="form-label">Midwife</label>
                            <select class="form-select" id="midwife_id" name="midwife_id">
                                <option value="">Select Midwife (Optional)</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}" {{ $record->midwife_id == $provider->id ? 'selected' : '' }}>{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="pregnancy_status" class="form-label">Pregnancy Status</label>
                            <select class="form-select" id="pregnancy_status" name="pregnancy_status">
                                <option value="active" {{ $record->pregnancy_status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ $record->pregnancy_status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="terminated" {{ $record->pregnancy_status == 'terminated' ? 'selected' : '' }}>Terminated</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="visit_date" class="form-label">Visit Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="visit_date" name="visit_date" value="{{ $record->visit_date }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="visit_time" class="form-label">Visit Time</label>
                            <input type="time" class="form-control" id="visit_time" name="visit_time" value="{{ $record->visit_time }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gestational Information -->
            <div class="admin-card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-baby me-2"></i>Gestational Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="last_menstrual_period" class="form-label">Last Menstrual Period (LMP)</label>
                            <input type="date" class="form-control" id="last_menstrual_period" name="last_menstrual_period" value="{{ $record->last_menstrual_period }}">
                        </div>
                        <div class="col-md-6">
                            <label for="estimated_due_date" class="form-label">Estimated Due Date (EDD)</label>
                            <input type="date" class="form-control" id="estimated_due_date" name="estimated_due_date" value="{{ $record->estimated_due_date }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="gestational_age_weeks" class="form-label">Gestational Age (Weeks)</label>
                            <input type="number" class="form-control" id="gestational_age_weeks" name="gestational_age_weeks" min="0" max="42" value="{{ $record->gestational_age_weeks }}">
                        </div>
                        <div class="col-md-3">
                            <label for="gestational_age_days" class="form-label">Gestational Age (Days)</label>
                            <input type="number" class="form-control" id="gestational_age_days" name="gestational_age_days" min="0" max="6" value="{{ $record->gestational_age_days }}">
                        </div>
                        <div class="col-md-3">
                            <label for="gravida" class="form-label">Gravida (G)</label>
                            <input type="number" class="form-control" id="gravida" name="gravida" min="0" value="{{ $record->gravida }}">
                        </div>
                        <div class="col-md-3">
                            <label for="para" class="form-label">Para (P)</label>
                            <input type="number" class="form-control" id="para" name="para" min="0" value="{{ $record->para }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="abortion" class="form-label">Abortion (A)</label>
                            <input type="number" class="form-control" id="abortion" name="abortion" min="0" value="{{ $record->abortion }}">
                        </div>
                        <div class="col-md-4">
                            <label for="living_children" class="form-label">Living Children</label>
                            <input type="number" class="form-control" id="living_children" name="living_children" min="0" value="{{ $record->living_children }}">
                        </div>
                        <div class="col-md-4">
                            <label for="risk_level" class="form-label">Risk Level</label>
                            <select class="form-select" id="risk_level" name="risk_level">
                                <option value="">Select Risk Level</option>
                                <option value="low" {{ $record->risk_level == 'low' ? 'selected' : '' }}>Low Risk</option>
                                <option value="moderate" {{ $record->risk_level == 'moderate' ? 'selected' : '' }}>Moderate Risk</option>
                                <option value="high" {{ $record->risk_level == 'high' ? 'selected' : '' }}>High Risk</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vital Signs -->
            <div class="admin-card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-heartbeat me-2"></i>Vital Signs</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="blood_pressure_systolic" class="form-label">Blood Pressure Systolic</label>
                            <input type="number" class="form-control" id="blood_pressure_systolic" name="blood_pressure_systolic" min="0" value="{{ $record->blood_pressure_systolic }}">
                        </div>
                        <div class="col-md-3">
                            <label for="blood_pressure_diastolic" class="form-label">Blood Pressure Diastolic</label>
                            <input type="number" class="form-control" id="blood_pressure_diastolic" name="blood_pressure_diastolic" min="0" value="{{ $record->blood_pressure_diastolic }}">
                        </div>
                        <div class="col-md-3">
                            <label for="weight_kg" class="form-label">Weight (kg)</label>
                            <input type="number" step="0.1" class="form-control" id="weight_kg" name="weight_kg" min="0" value="{{ $record->weight_kg }}">
                        </div>
                        <div class="col-md-3">
                            <label for="height_cm" class="form-label">Height (cm)</label>
                            <input type="number" step="0.1" class="form-control" id="height_cm" name="height_cm" min="0" value="{{ $record->height_cm }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="bmi" class="form-label">BMI</label>
                            <input type="number" step="0.1" class="form-control" id="bmi" name="bmi" min="0" value="{{ $record->bmi }}">
                        </div>
                        <div class="col-md-3">
                            <label for="pulse_rate" class="form-label">Pulse Rate (bpm)</label>
                            <input type="number" class="form-control" id="pulse_rate" name="pulse_rate" min="0" value="{{ $record->pulse_rate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="respiratory_rate" class="form-label">Respiratory Rate</label>
                            <input type="number" class="form-control" id="respiratory_rate" name="respiratory_rate" min="0" value="{{ $record->respiratory_rate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="temperature_celsius" class="form-label">Temperature (°C)</label>
                            <input type="number" step="0.1" class="form-control" id="temperature_celsius" name="temperature_celsius" min="0" value="{{ $record->temperature_celsius }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fetal_heart_rate" class="form-label">Fetal Heart Rate (bpm)</label>
                            <input type="number" class="form-control" id="fetal_heart_rate" name="fetal_heart_rate" min="0" value="{{ $record->fetal_heart_rate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="fetal_position" class="form-label">Fetal Position</label>
                            <input type="text" class="form-control" id="fetal_position" name="fetal_position" maxlength="100" value="{{ $record->fetal_position }}">
                        </div>
                        <div class="col-md-3">
                            <label for="fetal_presentation" class="form-label">Fetal Presentation</label>
                            <input type="text" class="form-control" id="fetal_presentation" name="fetal_presentation" maxlength="100" value="{{ $record->fetal_presentation }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Laboratory Results -->
            <div class="admin-card mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-flask me-2"></i>Laboratory Results</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="blood_type" class="form-label">Blood Type</label>
                            <select class="form-select" id="blood_type" name="blood_type">
                                <option value="">Select Blood Type</option>
                                <option value="A+" {{ $record->blood_type == 'A+' ? 'selected' : '' }}>A+</option>
                                <option value="A-" {{ $record->blood_type == 'A-' ? 'selected' : '' }}>A-</option>
                                <option value="B+" {{ $record->blood_type == 'B+' ? 'selected' : '' }}>B+</option>
                                <option value="B-" {{ $record->blood_type == 'B-' ? 'selected' : '' }}>B-</option>
                                <option value="AB+" {{ $record->blood_type == 'AB+' ? 'selected' : '' }}>AB+</option>
                                <option value="AB-" {{ $record->blood_type == 'AB-' ? 'selected' : '' }}>AB-</option>
                                <option value="O+" {{ $record->blood_type == 'O+' ? 'selected' : '' }}>O+</option>
                                <option value="O-" {{ $record->blood_type == 'O-' ? 'selected' : '' }}>O-</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="hemoglobin_level" class="form-label">Hemoglobin Level</label>
                            <input type="text" class="form-control" id="hemoglobin_level" name="hemoglobin_level" maxlength="50" value="{{ $record->hemoglobin_level }}">
                        </div>
                        <div class="col-md-4">
                            <label for="hematocrit_level" class="form-label">Hematocrit Level</label>
                            <input type="text" class="form-control" id="hematocrit_level" name="hematocrit_level" maxlength="50" value="{{ $record->hematocrit_level }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="urinalysis" class="form-label">Urinalysis</label>
                            <textarea class="form-control" id="urinalysis" name="urinalysis" rows="2" maxlength="500">{{ $record->urinalysis }}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label for="vdrl_test" class="form-label">VDRL Test</label>
                            <input type="text" class="form-control" id="vdrl_test" name="vdrl_test" maxlength="50" value="{{ $record->vdrl_test }}">
                        </div>
                        <div class="col-md-3">
                            <label for="hbsag_test" class="form-label">HBsAg Test</label>
                            <input type="text" class="form-control" id="hbsag_test" name="hbsag_test" maxlength="50" value="{{ $record->hbsag_test }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="admin-card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-notes-medical me-2"></i>Additional Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="risk_factors" class="form-label">Risk Factors</label>
                            <textarea class="form-control" id="risk_factors" name="risk_factors" rows="3">{{ $record->risk_factors }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="complications" class="form-label">Complications</label>
                            <textarea class="form-control" id="complications" name="complications" rows="3">{{ $record->complications }}</textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="general_notes" class="form-label">General Notes</label>
                            <textarea class="form-control" id="general_notes" name="general_notes" rows="3">{{ $record->general_notes }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="physician_notes" class="form-label">Physician Notes</label>
                            <textarea class="form-control" id="physician_notes" name="physician_notes" rows="3">{{ $record->physician_notes }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supplements and Vaccines -->
            <div class="admin-card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-pills me-2"></i>Supplements & Vaccines</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="iron_supplements" name="iron_supplements" value="1" {{ $record->iron_supplements ? 'checked' : '' }}>
                                <label class="form-check-label" for="iron_supplements">
                                    Iron Supplements
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="calcium_supplements" name="calcium_supplements" value="1" {{ $record->calcium_supplements ? 'checked' : '' }}>
                                <label class="form-check-label" for="calcium_supplements">
                                    Calcium Supplements
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="vitamin_supplements" name="vitamin_supplements" value="1" {{ $record->vitamin_supplements ? 'checked' : '' }}>
                                <label class="form-check-label" for="vitamin_supplements">
                                    Vitamin Supplements
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="td_vaccine_given" name="td_vaccine_given" value="1" {{ $record->td_vaccine_given ? 'checked' : '' }}>
                                <label class="form-check-label" for="td_vaccine_given">
                                    TD Vaccine Given
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="td_vaccine_date" class="form-label">TD Vaccine Date</label>
                            <input type="date" class="form-control" id="td_vaccine_date" name="td_vaccine_date" value="{{ $record->td_vaccine_date }}">
                        </div>
                        <div class="col-md-4">
                            <label for="td_vaccine_dose" class="form-label">TD Vaccine Dose</label>
                            <input type="number" class="form-control" id="td_vaccine_dose" name="td_vaccine_dose" min="0" value="{{ $record->td_vaccine_dose }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="medications" class="form-label">Medications</label>
                            <textarea class="form-control" id="medications" name="medications" rows="2">{{ $record->medications }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="counseling_topics" class="form-label">Counseling Topics</label>
                            <textarea class="form-control" id="counseling_topics" name="counseling_topics" rows="2">{{ $record->counseling_topics }}</textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="patient_education" class="form-label">Patient Education</label>
                            <textarea class="form-control" id="patient_education" name="patient_education" rows="2">{{ $record->patient_education }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="next_visit_date" class="form-label">Next Visit Date</label>
                            <input type="date" class="form-control" id="next_visit_date" name="next_visit_date" value="{{ $record->next_visit_date }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="next_visit_notes" class="form-label">Next Visit Notes</label>
                        <textarea class="form-control" id="next_visit_notes" name="next_visit_notes" rows="2">{{ $record->next_visit_notes }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="admin-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Update Prenatal Record
                            </button>
                            <a href="{{ route('admin-portal.patients.prenatal-records', $patient->id) }}" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
