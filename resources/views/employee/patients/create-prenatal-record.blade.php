@extends('employee.layouts.app')

@section('title', 'Add Prenatal Record - ' . $patient->name)

@section('content')
<div style="max-width: 1000px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="color: #333; margin: 0; font-size: 28px;">Add Prenatal Record</h1>
            <p style="color: #666; margin: 5px 0 0 0;">Patient: {{ $patient->name }}</p>
        </div>
        <a href="{{ route('employee.patients.prenatal-records', $patient->id) }}" style="background: #6c757d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">← Back to Records</a>
    </div>

    <!-- Form -->
    <div style="background: white; padding: 30px; border-radius: 8px; border: 1px solid #ddd;">
        <form method="POST" action="{{ route('employee.patients.store-prenatal-record', $patient->id) }}">
            @csrf

            <!-- Basic Information -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #007bff; padding-bottom: 10px;">Basic Information</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="attending_physician_id" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Attending Physician <span style="color: #dc3545;">*</span></label>
                        <select id="attending_physician_id" name="attending_physician_id" required
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('attending_physician_id') ? 'border-color: #dc3545;' : '' }}">
                            <option value="">Select Physician</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" {{ old('attending_physician_id') == $provider->id ? 'selected' : '' }}>
                                    {{ $provider->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('attending_physician_id'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('attending_physician_id') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="midwife_id" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Midwife</label>
                        <select id="midwife_id" name="midwife_id"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Midwife (Optional)</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" {{ old('midwife_id') == $provider->id ? 'selected' : '' }}>
                                    {{ $provider->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="visit_date" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Visit Date <span style="color: #dc3545;">*</span></label>
                        <input type="date" id="visit_date" name="visit_date" value="{{ old('visit_date') }}" required
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('visit_date') ? 'border-color: #dc3545;' : '' }}">
                        @if($errors->has('visit_date'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('visit_date') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="visit_time" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Visit Time</label>
                        <input type="time" id="visit_time" name="visit_time" value="{{ old('visit_time') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="times_visited" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Times Visited</label>
                        <input type="number" id="times_visited" name="times_visited" value="{{ old('times_visited', 1) }}" min="1"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="pregnancy_status" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Pregnancy Status</label>
                        <select id="pregnancy_status" name="pregnancy_status"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="active" {{ old('pregnancy_status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ old('pregnancy_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="terminated" {{ old('pregnancy_status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="visit_date" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Visit Date <span style="color: #dc3545;">*</span></label>
                    <input type="date" id="visit_date" name="visit_date" value="{{ old('visit_date') }}" required
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('visit_date') ? 'border-color: #dc3545;' : '' }}">
                    @if($errors->has('visit_date'))
                        <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('visit_date') }}</div>
                    @endif
                </div>
            </div>

            <!-- Pregnancy Information -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #28a745; padding-bottom: 10px;">Pregnancy Information</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="last_menstrual_period" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Last Menstrual Period</label>
                        <input type="date" id="last_menstrual_period" name="last_menstrual_period" value="{{ old('last_menstrual_period') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="estimated_due_date" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Estimated Due Date</label>
                        <input type="date" id="estimated_due_date" name="estimated_due_date" value="{{ old('estimated_due_date') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="gravida" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Gravida</label>
                        <input type="number" id="gravida" name="gravida" value="{{ old('gravida', 1) }}" min="1"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="para" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Para</label>
                        <input type="number" id="para" name="para" value="{{ old('para', 0) }}" min="0"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="gestational_age_weeks" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Gestational Age (Weeks)</label>
                        <input type="number" id="gestational_age_weeks" name="gestational_age_weeks" value="{{ old('gestational_age_weeks') }}" min="0" max="42"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="gestational_age_days" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Gestational Age (Days)</label>
                        <input type="number" id="gestational_age_days" name="gestational_age_days" value="{{ old('gestational_age_days') }}" min="0" max="6"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="abortion" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Abortion</label>
                        <input type="number" id="abortion" name="abortion" value="{{ old('abortion', 0) }}" min="0"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>
            </div>

            <!-- Vital Signs -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #dc3545; padding-bottom: 10px;">Vital Signs</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="blood_pressure_systolic" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">BP Systolic</label>
                        <input type="number" id="blood_pressure_systolic" name="blood_pressure_systolic" value="{{ old('blood_pressure_systolic') }}" min="0"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="blood_pressure_diastolic" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">BP Diastolic</label>
                        <input type="number" id="blood_pressure_diastolic" name="blood_pressure_diastolic" value="{{ old('blood_pressure_diastolic') }}" min="0"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="weight_kg" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Weight (kg)</label>
                        <input type="number" id="weight_kg" name="weight_kg" value="{{ old('weight_kg') }}" step="0.1" min="0"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="height_cm" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Height (cm)</label>
                        <input type="number" id="height_cm" name="height_cm" value="{{ old('height_cm') }}" step="0.1" min="0"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="pulse_rate" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Pulse Rate</label>
                        <input type="number" id="pulse_rate" name="pulse_rate" value="{{ old('pulse_rate') }}" min="0"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 20px;">
                    <div>
                        <label for="respiratory_rate" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Respiratory Rate</label>
                        <input type="number" id="respiratory_rate" name="respiratory_rate" value="{{ old('respiratory_rate') }}" min="0"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="temperature_celsius" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Temperature (°C)</label>
                        <input type="number" id="temperature_celsius" name="temperature_celsius" value="{{ old('temperature_celsius') }}" step="0.1" min="0"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="bmi" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">BMI</label>
                        <input type="number" id="bmi" name="bmi" value="{{ old('bmi') }}" step="0.1" min="0"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>
            </div>

            <!-- Fetal Assessment -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #6f42c1; padding-bottom: 10px;">Fetal Assessment</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="fetal_heart_rate" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Fetal Heart Rate</label>
                        <input type="number" id="fetal_heart_rate" name="fetal_heart_rate" value="{{ old('fetal_heart_rate') }}" min="0"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="fetal_position" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Fetal Position</label>
                        <input type="text" id="fetal_position" name="fetal_position" value="{{ old('fetal_position') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="fetal_presentation" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Fetal Presentation</label>
                        <input type="text" id="fetal_presentation" name="fetal_presentation" value="{{ old('fetal_presentation') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="fundal_height_cm" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Fundal Height (cm)</label>
                        <input type="number" id="fundal_height_cm" name="fundal_height_cm" value="{{ old('fundal_height_cm') }}" step="0.1" min="0"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>
            </div>

            <!-- Laboratory Results -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #20c997; padding-bottom: 10px;">Laboratory Results</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="blood_type" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Blood Type</label>
                        <select id="blood_type" name="blood_type"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Blood Type</option>
                            <option value="A+" {{ old('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ old('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ old('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ old('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="AB+" {{ old('blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ old('blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
                            <option value="O+" {{ old('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ old('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
                        </select>
                    </div>
                    <div>
                        <label for="hemoglobin_level" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Hemoglobin</label>
                        <input type="text" id="hemoglobin_level" name="hemoglobin_level" value="{{ old('hemoglobin_level') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="hematocrit_level" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Hematocrit</label>
                        <input type="text" id="hematocrit_level" name="hematocrit_level" value="{{ old('hematocrit_level') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="urinalysis" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Urinalysis</label>
                        <input type="text" id="urinalysis" name="urinalysis" value="{{ old('urinalysis') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                    <div>
                        <label for="vdrl_test" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">VDRL Test</label>
                        <input type="text" id="vdrl_test" name="vdrl_test" value="{{ old('vdrl_test') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="hbsag_test" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">HBsAg Test</label>
                        <input type="text" id="hbsag_test" name="hbsag_test" value="{{ old('hbsag_test') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>
            </div>

            <!-- Risk Assessment -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #fd7e14; padding-bottom: 10px;">Risk Assessment</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="risk_level" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Risk Level</label>
                        <select id="risk_level" name="risk_level"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="low" {{ old('risk_level', 'low') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="moderate" {{ old('risk_level') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                            <option value="high" {{ old('risk_level') == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                    <div>
                        <label for="living_children" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Living Children</label>
                        <input type="number" id="living_children" name="living_children" value="{{ old('living_children', 0) }}" min="0"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="risk_factors" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Risk Factors</label>
                    <textarea id="risk_factors" name="risk_factors" rows="3"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">{{ old('risk_factors') }}</textarea>
                </div>

                <div>
                    <label for="complications" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Complications</label>
                    <textarea id="complications" name="complications" rows="3"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">{{ old('complications') }}</textarea>
                </div>
            </div>

            <!-- Immunization & Supplements -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #e83e8c; padding-bottom: 10px;">Immunization & Supplements</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="td_vaccine_given" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">TD Vaccine Given</label>
                        <input type="checkbox" id="td_vaccine_given" name="td_vaccine_given" value="1" {{ old('td_vaccine_given') ? 'checked' : '' }}
                               style="width: 20px; height: 20px;">
                    </div>
                    <div>
                        <label for="td_vaccine_date" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">TD Vaccine Date</label>
                        <input type="date" id="td_vaccine_date" name="td_vaccine_date" value="{{ old('td_vaccine_date') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="td_vaccine_dose" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">TD Vaccine Dose</label>
                        <input type="number" id="td_vaccine_dose" name="td_vaccine_dose" value="{{ old('td_vaccine_dose') }}" min="0"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="iron_supplements" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Iron Supplements</label>
                        <input type="checkbox" id="iron_supplements" name="iron_supplements" value="1" {{ old('iron_supplements') ? 'checked' : '' }}
                               style="width: 20px; height: 20px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="calcium_supplements" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Calcium Supplements</label>
                        <input type="checkbox" id="calcium_supplements" name="calcium_supplements" value="1" {{ old('calcium_supplements') ? 'checked' : '' }}
                               style="width: 20px; height: 20px;">
                    </div>
                    <div>
                        <label for="vitamin_supplements" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Vitamin Supplements</label>
                        <input type="checkbox" id="vitamin_supplements" name="vitamin_supplements" value="1" {{ old('vitamin_supplements') ? 'checked' : '' }}
                               style="width: 20px; height: 20px;">
                    </div>
                </div>
            </div>

            <!-- Medications & Counseling -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #17a2b8; padding-bottom: 10px;">Medications & Counseling</h3>

                <div style="margin-bottom: 20px;">
                    <label for="medications" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Medications</label>
                    <textarea id="medications" name="medications" rows="3"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">{{ old('medications') }}</textarea>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="counseling_topics" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Counseling Topics</label>
                    <textarea id="counseling_topics" name="counseling_topics" rows="3"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">{{ old('counseling_topics') }}</textarea>
                </div>

                <div>
                    <label for="patient_education" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Patient Education</label>
                    <textarea id="patient_education" name="patient_education" rows="3"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">{{ old('patient_education') }}</textarea>
                </div>
            </div>

            <!-- Next Visit & Notes -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #6c757d; padding-bottom: 10px;">Next Visit & Notes</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="next_visit_date" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Next Visit Date</label>
                        <input type="date" id="next_visit_date" name="next_visit_date" value="{{ old('next_visit_date') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="next_visit_notes" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Next Visit Notes</label>
                        <input type="text" id="next_visit_notes" name="next_visit_notes" value="{{ old('next_visit_notes') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="general_notes" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">General Notes</label>
                    <textarea id="general_notes" name="general_notes" rows="3"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">{{ old('general_notes') }}</textarea>
                </div>

                <div>
                    <label for="physician_notes" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Physician Notes</label>
                    <textarea id="physician_notes" name="physician_notes" rows="3"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">{{ old('physician_notes') }}</textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div style="border-top: 1px solid #eee; padding-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                <a href="{{ route('employee.patients.prenatal-records', $patient->id) }}" style="background: #6c757d; color: white; text-decoration: none; padding: 12px 24px; border-radius: 5px; font-size: 14px;">Cancel</a>
                <button type="submit" style="background: #007bff; color: white; border: none; padding: 12px 24px; border-radius: 5px; font-size: 14px; cursor: pointer;">Save Prenatal Record</button>
            </div>
        </form>
    </div>
</div>
@endsection
