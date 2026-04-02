@extends('employee.layouts.app')

@section('title', 'Add Postnatal Record - ' . $patient->name)

@section('content')
<div style="max-width: 1000px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="color: #333; margin: 0; font-size: 28px;">Add Postnatal Record</h1>
            <p style="color: #666; margin: 5px 0 0 0;">Patient: {{ $patient->name }}</p>
        </div>
        <a href="{{ route('employee.patients.postnatal-records', $patient->id) }}" style="background: #6c757d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">← Back to Records</a>
    </div>

    <!-- Form -->
    <div style="background: white; padding: 30px; border-radius: 8px; border: 1px solid #ddd;">
        <form method="POST" action="{{ route('employee.patients.store-postnatal-record', $patient->id) }}">
            @csrf
            <!-- Basic Information -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #007bff; padding-bottom: 10px;">Basic Information</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="provider_id" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Provider <span style="color: #dc3545;">*</span></label>
                        <select id="provider_id" name="provider_id" required
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('provider_id') ? 'border-color: #dc3545;' : '' }}">
                            <option value="">Select Provider</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" {{ old('provider_id') == $provider->id ? 'selected' : '' }}>
                                    {{ $provider->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('provider_id'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('provider_id') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="visit_number" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Visit Number <span style="color: #dc3545;">*</span></label>
                        <input type="number" id="visit_number" name="visit_number" value="{{ old('visit_number', 1) }}" required min="1"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('visit_number') ? 'border-color: #dc3545;' : '' }}">
                        @if($errors->has('visit_number'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('visit_number') }}</div>
                        @endif
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="visit_date" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Visit Date <span style="color: #dc3545;">*</span></label>
                        <input type="date" id="visit_date" name="visit_date" value="{{ old('visit_date', date('Y-m-d')) }}" required
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('visit_date') ? 'border-color: #dc3545;' : '' }}">
                        @if($errors->has('visit_date'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('visit_date') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="days_postpartum" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Days Postpartum</label>
                        <input type="number" id="days_postpartum" name="days_postpartum" value="{{ old('days_postpartum') }}" min="0" placeholder="e.g., 7"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="weeks_postpartum" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Weeks Postpartum</label>
                        <input type="number" id="weeks_postpartum" name="weeks_postpartum" value="{{ old('weeks_postpartum') }}" min="0" placeholder="e.g., 2"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>
            </div>

            <!-- Vital Signs -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #dc3545; padding-bottom: 10px;">Vital Signs</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="weight" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Weight (kg)</label>
                        <input type="number" id="weight" name="weight" value="{{ old('weight') }}" step="0.1" min="0" placeholder="65.5"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="blood_pressure_systolic" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">BP Systolic</label>
                        <input type="number" id="blood_pressure_systolic" name="blood_pressure_systolic" value="{{ old('blood_pressure_systolic') }}" min="0" placeholder="120"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="blood_pressure_diastolic" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">BP Diastolic</label>
                        <input type="number" id="blood_pressure_diastolic" name="blood_pressure_diastolic" value="{{ old('blood_pressure_diastolic') }}" min="0" placeholder="80"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="heart_rate" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Heart Rate</label>
                        <input type="number" id="heart_rate" name="heart_rate" value="{{ old('heart_rate') }}" min="0" placeholder="72"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 20px;">
                    <div>
                        <label for="temperature" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Temperature (°C)</label>
                        <input type="number" id="temperature" name="temperature" value="{{ old('temperature') }}" step="0.1" min="0" placeholder="36.5"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="respiratory_rate" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Respiratory Rate</label>
                        <input type="number" id="respiratory_rate" name="respiratory_rate" value="{{ old('respiratory_rate') }}" min="0" placeholder="16"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="oxygen_saturation" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Oxygen Saturation (%)</label>
                        <input type="number" id="oxygen_saturation" name="oxygen_saturation" value="{{ old('oxygen_saturation') }}" min="0" max="100" placeholder="98"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>
            </div>

            <!-- Physical Assessment -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #28a745; padding-bottom: 10px;">Physical Assessment</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="general_condition" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">General Condition</label>
                        <select id="general_condition" name="general_condition"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Condition</option>
                            <option value="good" {{ old('general_condition') == 'good' ? 'selected' : '' }}>Good</option>
                            <option value="fair" {{ old('general_condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                            <option value="poor" {{ old('general_condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                            <option value="critical" {{ old('general_condition') == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                    </div>
                    <div>
                        <label for="breast_condition" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Breast Condition</label>
                        <select id="breast_condition" name="breast_condition"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Condition</option>
                            <option value="normal" {{ old('breast_condition') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="engorged" {{ old('breast_condition') == 'engorged' ? 'selected' : '' }}>Engorged</option>
                            <option value="mastitis" {{ old('breast_condition') == 'mastitis' ? 'selected' : '' }}>Mastitis</option>
                            <option value="cracked_nipples" {{ old('breast_condition') == 'cracked_nipples' ? 'selected' : '' }}>Cracked Nipples</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 20px;">
                    <div>
                        <label for="uterus_condition" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Uterus Condition</label>
                        <select id="uterus_condition" name="uterus_condition"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Condition</option>
                            <option value="firm" {{ old('uterus_condition') == 'firm' ? 'selected' : '' }}>Firm</option>
                            <option value="soft" {{ old('uterus_condition') == 'soft' ? 'selected' : '' }}>Soft</option>
                            <option value="tender" {{ old('uterus_condition') == 'tender' ? 'selected' : '' }}>Tender</option>
                        </select>
                    </div>
                    <div>
                        <label for="perineum_condition" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Perineum Condition</label>
                        <select id="perineum_condition" name="perineum_condition"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Condition</option>
                            <option value="intact" {{ old('perineum_condition') == 'intact' ? 'selected' : '' }}>Intact</option>
                            <option value="healing" {{ old('perineum_condition') == 'healing' ? 'selected' : '' }}>Healing</option>
                            <option value="infected" {{ old('perineum_condition') == 'infected' ? 'selected' : '' }}>Infected</option>
                        </select>
                    </div>
                    <div>
                        <label for="lochia_condition" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Lochia Condition</label>
                        <select id="lochia_condition" name="lochia_condition"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Condition</option>
                            <option value="normal" {{ old('lochia_condition') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="heavy" {{ old('lochia_condition') == 'heavy' ? 'selected' : '' }}>Heavy</option>
                            <option value="foul_smelling" {{ old('lochia_condition') == 'foul_smelling' ? 'selected' : '' }}>Foul Smelling</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <label for="episiotomy_condition" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Episiotomy Condition</label>
                    <select id="episiotomy_condition" name="episiotomy_condition"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        <option value="">Select Condition</option>
                        <option value="healing_well" {{ old('episiotomy_condition') == 'healing_well' ? 'selected' : '' }}>Healing Well</option>
                        <option value="infected" {{ old('episiotomy_condition') == 'infected' ? 'selected' : '' }}>Infected</option>
                        <option value="dehisced" {{ old('episiotomy_condition') == 'dehisced' ? 'selected' : '' }}>Dehisced</option>
                    </select>
                </div>
            </div>

            <!-- Breastfeeding & Newborn -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #17a2b8; padding-bottom: 10px;">Breastfeeding & Newborn</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="breastfeeding_status" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Breastfeeding Status</label>
                        <select id="breastfeeding_status" name="breastfeeding_status"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Status</option>
                            <option value="exclusive_breastfeeding" {{ old('breastfeeding_status') == 'exclusive_breastfeeding' ? 'selected' : '' }}>Exclusive Breastfeeding</option>
                            <option value="mixed_feeding" {{ old('breastfeeding_status') == 'mixed_feeding' ? 'selected' : '' }}>Mixed Feeding</option>
                            <option value="formula_feeding" {{ old('breastfeeding_status') == 'formula_feeding' ? 'selected' : '' }}>Formula Feeding</option>
                            <option value="not_breastfeeding" {{ old('breastfeeding_status') == 'not_breastfeeding' ? 'selected' : '' }}>Not Breastfeeding</option>
                        </select>
                    </div>
                    <div>
                        <label for="latch_assessment" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Latch Assessment</label>
                        <select id="latch_assessment" name="latch_assessment"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Assessment</option>
                            <option value="good" {{ old('latch_assessment') == 'good' ? 'selected' : '' }}>Good</option>
                            <option value="fair" {{ old('latch_assessment') == 'fair' ? 'selected' : '' }}>Fair</option>
                            <option value="poor" {{ old('latch_assessment') == 'poor' ? 'selected' : '' }}>Poor</option>
                            <option value="needs_improvement" {{ old('latch_assessment') == 'needs_improvement' ? 'selected' : '' }}>Needs Improvement</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="newborn_check" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Newborn Check Performed</label>
                        <select id="newborn_check" name="newborn_check"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select</option>
                            <option value="1" {{ old('newborn_check') ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('newborn_check') === '0' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div>
                        <label for="newborn_weight" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Newborn Weight (kg)</label>
                        <input type="number" id="newborn_weight" name="newborn_weight" value="{{ old('newborn_weight') }}" step="0.01" min="0" placeholder="3.2"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div></div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="breastfeeding_notes" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Breastfeeding Notes</label>
                    <textarea id="breastfeeding_notes" name="breastfeeding_notes" rows="3" placeholder="Notes about breastfeeding progress, challenges, etc."
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('breastfeeding_notes') }}</textarea>
                </div>

                <div>
                    <label for="newborn_notes" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Newborn Notes</label>
                    <textarea id="newborn_notes" name="newborn_notes" rows="3" placeholder="Notes about newborn health, feeding, etc."
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('newborn_notes') }}</textarea>
                </div>
            </div>
            <!-- Family Planning & Follow-up -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #6f42c1; padding-bottom: 10px;">Family Planning & Follow-up</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="family_planning_method" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Family Planning Method</label>
                        <select id="family_planning_method" name="family_planning_method"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Method</option>
                            <option value="none" {{ old('family_planning_method') == 'none' ? 'selected' : '' }}>None</option>
                            <option value="condoms" {{ old('family_planning_method') == 'condoms' ? 'selected' : '' }}>Condoms</option>
                            <option value="oral_contraceptives" {{ old('family_planning_method') == 'oral_contraceptives' ? 'selected' : '' }}>Oral Contraceptives</option>
                            <option value="injectables" {{ old('family_planning_method') == 'injectables' ? 'selected' : '' }}>Injectables</option>
                            <option value="iud" {{ old('family_planning_method') == 'iud' ? 'selected' : '' }}>IUD</option>
                            <option value="implant" {{ old('family_planning_method') == 'implant' ? 'selected' : '' }}>Implant</option>
                            <option value="tubal_ligation" {{ old('family_planning_method') == 'tubal_ligation' ? 'selected' : '' }}>Tubal Ligation</option>
                            <option value="other" {{ old('family_planning_method') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="next_visit_type" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Next Visit Type</label>
                        <select id="next_visit_type" name="next_visit_type"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Visit Type</option>
                            <option value="postnatal_check" {{ old('next_visit_type') == 'postnatal_check' ? 'selected' : '' }}>Postnatal Check</option>
                            <option value="family_planning" {{ old('next_visit_type') == 'family_planning' ? 'selected' : '' }}>Family Planning</option>
                            <option value="immunization" {{ old('next_visit_type') == 'immunization' ? 'selected' : '' }}>Immunization</option>
                            <option value="other" {{ old('next_visit_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="follow_up_date" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Follow-up Date</label>
                        <input type="date" id="follow_up_date" name="follow_up_date" value="{{ old('follow_up_date') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div></div>
                </div>

                <div>
                    <label for="family_planning_counseling" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Family Planning Counseling</label>
                    <textarea id="family_planning_counseling" name="family_planning_counseling" rows="3" placeholder="Counseling provided about family planning options"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('family_planning_counseling') }}</textarea>
                </div>
            </div>

            <!-- Assessment & Notes -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #20c997; padding-bottom: 10px;">Assessment & Notes</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="chief_complaint" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Chief Complaint</label>
                        <textarea id="chief_complaint" name="chief_complaint" rows="2" placeholder="Patient's main concern or complaint"
                                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('chief_complaint') }}</textarea>
                    </div>
                    <div>
                        <label for="medications_prescribed" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Medications Prescribed</label>
                        <textarea id="medications_prescribed" name="medications_prescribed" rows="2" placeholder="List of prescribed medications"
                                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('medications_prescribed') }}</textarea>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="assessment" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Assessment</label>
                    <textarea id="assessment" name="assessment" rows="3" placeholder="Clinical assessment and diagnosis"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('assessment') }}</textarea>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="plan" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Plan</label>
                    <textarea id="plan" name="plan" rows="3" placeholder="Treatment plan and recommendations"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('plan') }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="instructions_given" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Instructions Given</label>
                        <textarea id="instructions_given" name="instructions_given" rows="2" placeholder="Instructions given to patient"
                                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('instructions_given') }}</textarea>
                    </div>
                    <div></div>
                </div>

                <div>
                    <label for="notes" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Additional Notes</label>
                    <textarea id="notes" name="notes" rows="3" placeholder="Additional clinical notes"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('notes') }}</textarea>
                </div>
            </div>
                    </div>

            <!-- Form Actions -->
            <div style="border-top: 1px solid #eee; padding-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                <a href="{{ route('employee.patients.postnatal-records', $patient->id) }}" style="background: #6c757d; color: white; text-decoration: none; padding: 12px 24px; border-radius: 5px; font-size: 14px;">Cancel</a>
                <button type="submit" style="background: #007bff; color: white; border: none; padding: 12px 24px; border-radius: 5px; font-size: 14px; cursor: pointer;">Save Postnatal Record</button>
            </div>
        </form>
    </div>
</div>
@endsection
