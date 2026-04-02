@extends('employee.layouts.app')

@section('title', 'Create Postpartum Record - ' . $patient->name)

@section('content')
<style>
.section-header {
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.section-header:hover {
    background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.08) 100%);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
    border-bottom: none;
}

.card-body {
    padding: 2rem;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    border-radius: 8px;
    padding: 12px 24px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background: linear-gradient(135deg, #218838 0%, #1aa085 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.btn-outline-secondary:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(108, 117, 125, 0.3);
}
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-baby-carriage mr-2"></i>
                            Create Postpartum Record for {{ $patient->name }}
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('employee.patients.medical-records', $patient->id) }}" onclick="sessionStorage.setItem('lastMedicalRecordsTab', 'postpartum');" class="btn btn-light btn-sm mr-2">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                            <a href="{{ route('employee.patients.postpartum-records', $patient->id) }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-list mr-1"></i> View Records
                            </a>
                        </div>
                    </div>
                </div>

                <form action="{{ route('employee.patients.store-postpartum-record', $patient->id) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="section-header border-bottom border-warning pb-2 mb-3">
                                    <h5 class="text-warning mb-0 d-flex align-items-center">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Basic Information
                                        <small class="text-muted ml-2">Visit details and provider information</small>
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="provider_id" class="form-label">Provider <span class="text-danger">*</span></label>
                                <select name="provider_id" id="provider_id" class="form-control @error('provider_id') is-invalid @enderror" required>
                                    <option value="">Select Provider</option>
                                    @foreach($providers as $provider)
                                        <option value="{{ $provider->id }}" {{ old('provider_id') == $provider->id ? 'selected' : '' }}>
                                            {{ $provider->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('provider_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="visit_number" class="form-label">Visit Number <span class="text-danger">*</span></label>
                                <input type="number" name="visit_number" id="visit_number" class="form-control @error('visit_number') is-invalid @enderror"
                                       value="{{ old('visit_number') }}" min="1" required>
                                @error('visit_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="visit_date" class="form-label">Visit Date <span class="text-danger">*</span></label>
                                <input type="date" name="visit_date" id="visit_date" class="form-control @error('visit_date') is-invalid @enderror"
                                       value="{{ old('visit_date', date('Y-m-d')) }}" required>
                                @error('visit_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="weeks_postpartum" class="form-label">Weeks Postpartum</label>
                                <input type="number" name="weeks_postpartum" id="weeks_postpartum" class="form-control @error('weeks_postpartum') is-invalid @enderror"
                                       value="{{ old('weeks_postpartum') }}" min="0">
                                @error('weeks_postpartum')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="days_postpartum" class="form-label">Days Postpartum</label>
                                <input type="number" name="days_postpartum" id="days_postpartum" class="form-control @error('days_postpartum') is-invalid @enderror"
                                       value="{{ old('days_postpartum') }}" min="0">
                                @error('days_postpartum')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Vital Signs -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="section-header border-bottom border-primary pb-2 mb-3">
                                    <h5 class="text-primary mb-0 d-flex align-items-center">
                                        <i class="fas fa-heartbeat mr-2"></i>
                                        Vital Signs
                                        <small class="text-muted ml-2">Blood pressure, temperature, and other measurements</small>
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="weight" class="form-label">Weight (kg)</label>
                                <input type="number" step="0.1" name="weight" id="weight" class="form-control @error('weight') is-invalid @enderror"
                                       value="{{ old('weight') }}" min="0">
                                @error('weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="blood_pressure_systolic" class="form-label">Blood Pressure Systolic</label>
                                <input type="number" name="blood_pressure_systolic" id="blood_pressure_systolic" class="form-control @error('blood_pressure_systolic') is-invalid @enderror"
                                       value="{{ old('blood_pressure_systolic') }}" min="0">
                                @error('blood_pressure_systolic')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="blood_pressure_diastolic" class="form-label">Blood Pressure Diastolic</label>
                                <input type="number" name="blood_pressure_diastolic" id="blood_pressure_diastolic" class="form-control @error('blood_pressure_diastolic') is-invalid @enderror"
                                       value="{{ old('blood_pressure_diastolic') }}" min="0">
                                @error('blood_pressure_diastolic')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="heart_rate" class="form-label">Heart Rate (bpm)</label>
                                <input type="number" name="heart_rate" id="heart_rate" class="form-control @error('heart_rate') is-invalid @enderror"
                                       value="{{ old('heart_rate') }}" min="0">
                                @error('heart_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="temperature" class="form-label">Temperature (°C)</label>
                                <input type="number" step="0.1" name="temperature" id="temperature" class="form-control @error('temperature') is-invalid @enderror"
                                       value="{{ old('temperature') }}" min="0">
                                @error('temperature')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="general_condition" class="form-label">General Condition</label>
                                <select name="general_condition" id="general_condition" class="form-control @error('general_condition') is-invalid @enderror">
                                    <option value="">Select Condition</option>
                                    <option value="excellent" {{ old('general_condition') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="good" {{ old('general_condition') == 'good' ? 'selected' : '' }}>Good</option>
                                    <option value="fair" {{ old('general_condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                                    <option value="poor" {{ old('general_condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                                </select>
                                @error('general_condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Physical Examination -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="section-header border-bottom border-success pb-2 mb-3">
                                    <h5 class="text-success mb-0 d-flex align-items-center">
                                        <i class="fas fa-stethoscope mr-2"></i>
                                        Physical Examination
                                        <small class="text-muted ml-2">Body systems assessment</small>
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="breast_condition" class="form-label">Breast Condition</label>
                                <select name="breast_condition" id="breast_condition" class="form-control @error('breast_condition') is-invalid @enderror">
                                    <option value="">Select Condition</option>
                                    <option value="normal" {{ old('breast_condition') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="engorged" {{ old('breast_condition') == 'engorged' ? 'selected' : '' }}>Engorged</option>
                                    <option value="mastitis" {{ old('breast_condition') == 'mastitis' ? 'selected' : '' }}>Mastitis</option>
                                    <option value="cracked_nipples" {{ old('breast_condition') == 'cracked_nipples' ? 'selected' : '' }}>Cracked Nipples</option>
                                    <option value="other" {{ old('breast_condition') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('breast_condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="uterus_condition" class="form-label">Uterus Condition</label>
                                <select name="uterus_condition" id="uterus_condition" class="form-control @error('uterus_condition') is-invalid @enderror">
                                    <option value="">Select Condition</option>
                                    <option value="normal" {{ old('uterus_condition') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="subinvoluted" {{ old('uterus_condition') == 'subinvoluted' ? 'selected' : '' }}>Subinvoluted</option>
                                    <option value="tender" {{ old('uterus_condition') == 'tender' ? 'selected' : '' }}>Tender</option>
                                    <option value="other" {{ old('uterus_condition') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('uterus_condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="perineum_condition" class="form-label">Perineum Condition</label>
                                <select name="perineum_condition" id="perineum_condition" class="form-control @error('perineum_condition') is-invalid @enderror">
                                    <option value="">Select Condition</option>
                                    <option value="healed" {{ old('perineum_condition') == 'healed' ? 'selected' : '' }}>Healed</option>
                                    <option value="healing" {{ old('perineum_condition') == 'healing' ? 'selected' : '' }}>Healing</option>
                                    <option value="infected" {{ old('perineum_condition') == 'infected' ? 'selected' : '' }}>Infected</option>
                                    <option value="other" {{ old('perineum_condition') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('perineum_condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="lochia_condition" class="form-label">Lochia Condition</label>
                                <select name="lochia_condition" id="lochia_condition" class="form-control @error('lochia_condition') is-invalid @enderror">
                                    <option value="">Select Condition</option>
                                    <option value="normal" {{ old('lochia_condition') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="excessive" {{ old('lochia_condition') == 'excessive' ? 'selected' : '' }}>Excessive</option>
                                    <option value="foul_odor" {{ old('lochia_condition') == 'foul_odor' ? 'selected' : '' }}>Foul Odor</option>
                                    <option value="other" {{ old('lochia_condition') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('lochia_condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="episiotomy_condition" class="form-label">Episiotomy Condition</label>
                                <select name="episiotomy_condition" id="episiotomy_condition" class="form-control @error('episiotomy_condition') is-invalid @enderror">
                                    <option value="">Select Condition</option>
                                    <option value="healed" {{ old('episiotomy_condition') == 'healed' ? 'selected' : '' }}>Healed</option>
                                    <option value="healing" {{ old('episiotomy_condition') == 'healing' ? 'selected' : '' }}>Healing</option>
                                    <option value="infected" {{ old('episiotomy_condition') == 'infected' ? 'selected' : '' }}>Infected</option>
                                    <option value="other" {{ old('episiotomy_condition') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('episiotomy_condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Mental Health Assessment -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="section-header border-bottom border-info pb-2 mb-3">
                                    <h5 class="text-info mb-0 d-flex align-items-center">
                                        <i class="fas fa-brain mr-2"></i>
                                        Mental Health Assessment
                                        <small class="text-muted ml-2">Emotional well-being and postpartum depression screening</small>
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="mood_assessment" class="form-label">Mood Assessment</label>
                                <select name="mood_assessment" id="mood_assessment" class="form-control @error('mood_assessment') is-invalid @enderror">
                                    <option value="">Select Assessment</option>
                                    <option value="stable" {{ old('mood_assessment') == 'stable' ? 'selected' : '' }}>Stable</option>
                                    <option value="anxious" {{ old('mood_assessment') == 'anxious' ? 'selected' : '' }}>Anxious</option>
                                    <option value="depressed" {{ old('mood_assessment') == 'depressed' ? 'selected' : '' }}>Depressed</option>
                                    <option value="irritable" {{ old('mood_assessment') == 'irritable' ? 'selected' : '' }}>Irritable</option>
                                    <option value="other" {{ old('mood_assessment') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('mood_assessment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="postpartum_depression_screening" class="form-label">Postpartum Depression Screening</label>
                                <div class="form-check">
                                    <input type="checkbox" name="postpartum_depression_screening" id="postpartum_depression_screening"
                                           class="form-check-input @error('postpartum_depression_screening') is-invalid @enderror"
                                           value="1" {{ old('postpartum_depression_screening') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="postpartum_depression_screening">
                                        Positive screening result
                                    </label>
                                </div>
                                @error('postpartum_depression_screening')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="emotional_support_needs" class="form-label">Emotional Support Needs</label>
                                <textarea name="emotional_support_needs" id="emotional_support_needs" class="form-control @error('emotional_support_needs') is-invalid @enderror"
                                          rows="3">{{ old('emotional_support_needs') }}</textarea>
                                @error('emotional_support_needs')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="mental_health_notes" class="form-label">Mental Health Notes</label>
                                <textarea name="mental_health_notes" id="mental_health_notes" class="form-control @error('mental_health_notes') is-invalid @enderror"
                                          rows="3">{{ old('mental_health_notes') }}</textarea>
                                @error('mental_health_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Breastfeeding & Infant Care -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="section-header border-bottom border-primary pb-2 mb-3">
                                    <h5 class="text-primary mb-0 d-flex align-items-center">
                                        <i class="fas fa-baby mr-2"></i>
                                        Breastfeeding & Infant Care
                                        <small class="text-muted ml-2">Lactation support and newborn care</small>
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="breastfeeding_status" class="form-label">Breastfeeding Status</label>
                                <select name="breastfeeding_status" id="breastfeeding_status" class="form-control @error('breastfeeding_status') is-invalid @enderror">
                                    <option value="">Select Status</option>
                                    <option value="exclusive_breastfeeding" {{ old('breastfeeding_status') == 'exclusive_breastfeeding' ? 'selected' : '' }}>Exclusive Breastfeeding</option>
                                    <option value="mixed_feeding" {{ old('breastfeeding_status') == 'mixed_feeding' ? 'selected' : '' }}>Mixed Feeding</option>
                                    <option value="formula_feeding" {{ old('breastfeeding_status') == 'formula_feeding' ? 'selected' : '' }}>Formula Feeding</option>
                                    <option value="not_breastfeeding" {{ old('breastfeeding_status') == 'not_breastfeeding' ? 'selected' : '' }}>Not Breastfeeding</option>
                                </select>
                                @error('breastfeeding_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="infant_feeding_assessment" class="form-label">Infant Feeding Assessment</label>
                                <div class="form-check">
                                    <input type="checkbox" name="infant_feeding_assessment" id="infant_feeding_assessment"
                                           class="form-check-input @error('infant_feeding_assessment') is-invalid @enderror"
                                           value="1" {{ old('infant_feeding_assessment') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="infant_feeding_assessment">
                                        Assessment completed
                                    </label>
                                </div>
                                @error('infant_feeding_assessment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="breastfeeding_challenges" class="form-label">Breastfeeding Challenges</label>
                                <textarea name="breastfeeding_challenges" id="breastfeeding_challenges" class="form-control @error('breastfeeding_challenges') is-invalid @enderror"
                                          rows="3">{{ old('breastfeeding_challenges') }}</textarea>
                                @error('breastfeeding_challenges')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="lactation_support" class="form-label">Lactation Support Provided</label>
                                <textarea name="lactation_support" id="lactation_support" class="form-control @error('lactation_support') is-invalid @enderror"
                                          rows="3">{{ old('lactation_support') }}</textarea>
                                @error('lactation_support')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="infant_care_education" class="form-label">Infant Care Education Provided</label>
                                <textarea name="infant_care_education" id="infant_care_education" class="form-control @error('infant_care_education') is-invalid @enderror"
                                          rows="3">{{ old('infant_care_education') }}</textarea>
                                @error('infant_care_education')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Family Planning -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="section-header border-bottom border-success pb-2 mb-3">
                                    <h5 class="text-success mb-0 d-flex align-items-center">
                                        <i class="fas fa-users mr-2"></i>
                                        Family Planning
                                        <small class="text-muted ml-2">Contraception and reproductive health counseling</small>
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contraceptive_method" class="form-label">Contraceptive Method</label>
                                <select name="contraceptive_method" id="contraceptive_method" class="form-control @error('contraceptive_method') is-invalid @enderror">
                                    <option value="">Select Method</option>
                                    <option value="none" {{ old('contraceptive_method') == 'none' ? 'selected' : '' }}>None</option>
                                    <option value="condoms" {{ old('contraceptive_method') == 'condoms' ? 'selected' : '' }}>Condoms</option>
                                    <option value="oral_contraceptives" {{ old('contraceptive_method') == 'oral_contraceptives' ? 'selected' : '' }}>Oral Contraceptives</option>
                                    <option value="injectables" {{ old('contraceptive_method') == 'injectables' ? 'selected' : '' }}>Injectables</option>
                                    <option value="iud" {{ old('contraceptive_method') == 'iud' ? 'selected' : '' }}>IUD</option>
                                    <option value="implant" {{ old('contraceptive_method') == 'implant' ? 'selected' : '' }}>Implant</option>
                                    <option value="natural_methods" {{ old('contraceptive_method') == 'natural_methods' ? 'selected' : '' }}>Natural Methods</option>
                                    <option value="other" {{ old('contraceptive_method') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('contraceptive_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="next_contraceptive_visit" class="form-label">Next Contraceptive Visit</label>
                                <input type="date" name="next_contraceptive_visit" id="next_contraceptive_visit" class="form-control @error('next_contraceptive_visit') is-invalid @enderror"
                                       value="{{ old('next_contraceptive_visit') }}">
                                @error('next_contraceptive_visit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="family_planning_counseling" class="form-label">Family Planning Counseling</label>
                                <textarea name="family_planning_counseling" id="family_planning_counseling" class="form-control @error('family_planning_counseling') is-invalid @enderror"
                                          rows="3">{{ old('family_planning_counseling') }}</textarea>
                                @error('family_planning_counseling')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Complications & Medications -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="section-header border-bottom border-danger pb-2 mb-3">
                                    <h5 class="text-danger mb-0 d-flex align-items-center">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Complications & Medications
                                        <small class="text-muted ml-2">Health issues and prescribed treatments</small>
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="postpartum_complications" class="form-label">Postpartum Complications</label>
                                <textarea name="postpartum_complications" id="postpartum_complications" class="form-control @error('postpartum_complications') is-invalid @enderror"
                                          rows="3">{{ old('postpartum_complications') }}</textarea>
                                @error('postpartum_complications')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="medications_prescribed" class="form-label">Medications Prescribed</label>
                                <textarea name="medications_prescribed" id="medications_prescribed" class="form-control @error('medications_prescribed') is-invalid @enderror"
                                          rows="3">{{ old('medications_prescribed') }}</textarea>
                                @error('medications_prescribed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Care Instructions -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="section-header border-bottom border-info pb-2 mb-3">
                                    <h5 class="text-info mb-0 d-flex align-items-center">
                                        <i class="fas fa-clipboard-list mr-2"></i>
                                        Care Instructions
                                        <small class="text-muted ml-2">Wound care and activity guidelines</small>
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="wound_care_instructions" class="form-label">Wound Care Instructions</label>
                                <textarea name="wound_care_instructions" id="wound_care_instructions" class="form-control @error('wound_care_instructions') is-invalid @enderror"
                                          rows="3">{{ old('wound_care_instructions') }}</textarea>
                                @error('wound_care_instructions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="activity_restrictions" class="form-label">Activity Restrictions</label>
                                <textarea name="activity_restrictions" id="activity_restrictions" class="form-control @error('activity_restrictions') is-invalid @enderror"
                                          rows="3">{{ old('activity_restrictions') }}</textarea>
                                @error('activity_restrictions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Follow-up -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="section-header border-bottom border-warning pb-2 mb-3">
                                    <h5 class="text-warning mb-0 d-flex align-items-center">
                                        <i class="fas fa-calendar-check mr-2"></i>
                                        Follow-up
                                        <small class="text-muted ml-2">Next appointment scheduling</small>
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="follow_up_date" class="form-label">Follow-up Date</label>
                                <input type="date" name="follow_up_date" id="follow_up_date" class="form-control @error('follow_up_date') is-invalid @enderror"
                                       value="{{ old('follow_up_date') }}">
                                @error('follow_up_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="follow_up_reason" class="form-label">Follow-up Reason</label>
                                <input type="text" name="follow_up_reason" id="follow_up_reason" class="form-control @error('follow_up_reason') is-invalid @enderror"
                                       value="{{ old('follow_up_reason') }}" maxlength="200">
                                @error('follow_up_reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Education Provided -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="section-header border-bottom border-success pb-2 mb-3">
                                    <h5 class="text-success mb-0 d-flex align-items-center">
                                        <i class="fas fa-graduation-cap mr-2"></i>
                                        Education Provided
                                        <small class="text-muted ml-2">Nutrition, exercise, and warning signs</small>
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nutrition_counseling" class="form-label">Nutrition Counseling</label>
                                <textarea name="nutrition_counseling" id="nutrition_counseling" class="form-control @error('nutrition_counseling') is-invalid @enderror"
                                          rows="3">{{ old('nutrition_counseling') }}</textarea>
                                @error('nutrition_counseling')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="exercise_guidance" class="form-label">Exercise Guidance</label>
                                <textarea name="exercise_guidance" id="exercise_guidance" class="form-control @error('exercise_guidance') is-invalid @enderror"
                                          rows="3">{{ old('exercise_guidance') }}</textarea>
                                @error('exercise_guidance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="warning_signs_education" class="form-label">Warning Signs Education</label>
                                <textarea name="warning_signs_education" id="warning_signs_education" class="form-control @error('warning_signs_education') is-invalid @enderror"
                                          rows="3">{{ old('warning_signs_education') }}</textarea>
                                @error('warning_signs_education')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Assessment & Plan -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="section-header border-bottom border-primary pb-2 mb-3">
                                    <h5 class="text-primary mb-0 d-flex align-items-center">
                                        <i class="fas fa-clipboard-check mr-2"></i>
                                        Assessment & Plan
                                        <small class="text-muted ml-2">Clinical evaluation and care planning</small>
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="assessment_notes" class="form-label">Assessment Notes</label>
                                <textarea name="assessment_notes" id="assessment_notes" class="form-control @error('assessment_notes') is-invalid @enderror"
                                          rows="4">{{ old('assessment_notes') }}</textarea>
                                @error('assessment_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="plan_notes" class="form-label">Plan Notes</label>
                                <textarea name="plan_notes" id="plan_notes" class="form-control @error('plan_notes') is-invalid @enderror"
                                          rows="4">{{ old('plan_notes') }}</textarea>
                                @error('plan_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                <i class="fas fa-info-circle mr-1"></i>
                                Please ensure all required fields are completed before submitting.
                            </div>
                            <div>
                                <a href="{{ route('employee.patients.postpartum-records', $patient->id) }}" class="btn btn-outline-secondary mr-2">
                                    <i class="fas fa-times mr-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save mr-1"></i> Create Postpartum Record
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
