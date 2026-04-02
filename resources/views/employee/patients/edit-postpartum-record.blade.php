@extends('employee.layouts.app')

@section('title', 'Edit Postpartum Record - ' . $patient->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Postpartum Record for {{ $patient->name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('employee.patients.show-postpartum-record', [$patient->id, $record->id]) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-eye"></i> View Record
                        </a>
                        <a href="{{ route('employee.patients.postpartum-records', $patient->id) }}" class="btn btn-outline-secondary btn-sm">
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

            <form method="POST" action="{{ route('employee.patients.update-postpartum-record', [$patient->id, $record->id]) }}">
                @csrf
                @method('PUT')

                <!-- Provider and Visit Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-md text-warning me-2"></i>Provider & Visit Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="provider_id" class="form-label">Provider <span class="text-danger">*</span></label>
                                    <select id="provider_id" name="provider_id" class="form-select" required>
                                        <option value="">Select Provider</option>
                                        @foreach($providers as $provider)
                                            <option value="{{ $provider->id }}" {{ $record->provider_id == $provider->id ? 'selected' : '' }}>
                                                {{ $provider->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('provider_id'))
                                        <div class="text-danger small mt-1">{{ $errors->first('provider_id') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="visit_number" class="form-label">Visit Number <span class="text-danger">*</span></label>
                                    <input type="number" id="visit_number" name="visit_number"
                                           class="form-control" value="{{ old('visit_number', $record->visit_number) }}" min="1" required>
                                    @if($errors->has('visit_number'))
                                        <div class="text-danger small mt-1">{{ $errors->first('visit_number') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="visit_date" class="form-label">Visit Date <span class="text-danger">*</span></label>
                                    <input type="date" id="visit_date" name="visit_date"
                                           class="form-control" value="{{ old('visit_date', $record->visit_date ? $record->visit_date->format('Y-m-d') : '') }}" required>
                                    @if($errors->has('visit_date'))
                                        <div class="text-danger small mt-1">{{ $errors->first('visit_date') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="weeks_postpartum" class="form-label">Weeks Postpartum</label>
                                    <input type="number" id="weeks_postpartum" name="weeks_postpartum"
                                           class="form-control" value="{{ old('weeks_postpartum', $record->weeks_postpartum) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="days_postpartum" class="form-label">Days Postpartum</label>
                                    <input type="number" id="days_postpartum" name="days_postpartum"
                                           class="form-control" value="{{ old('days_postpartum', $record->days_postpartum) }}" min="0">
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
                                    <label for="weight" class="form-label">Weight (kg)</label>
                                    <input type="number" id="weight" name="weight" step="0.1"
                                           class="form-control" value="{{ old('weight', $record->weight) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="blood_pressure_systolic" class="form-label">BP Systolic</label>
                                    <input type="number" id="blood_pressure_systolic" name="blood_pressure_systolic"
                                           class="form-control" value="{{ old('blood_pressure_systolic', $record->blood_pressure_systolic) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="blood_pressure_diastolic" class="form-label">BP Diastolic</label>
                                    <input type="number" id="blood_pressure_diastolic" name="blood_pressure_diastolic"
                                           class="form-control" value="{{ old('blood_pressure_diastolic', $record->blood_pressure_diastolic) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="heart_rate" class="form-label">Heart Rate (bpm)</label>
                                    <input type="number" id="heart_rate" name="heart_rate"
                                           class="form-control" value="{{ old('heart_rate', $record->heart_rate) }}" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="temperature" class="form-label">Temperature (°C)</label>
                                    <input type="number" id="temperature" name="temperature" step="0.1"
                                           class="form-control" value="{{ old('temperature', $record->temperature) }}" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Physical Assessment -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-female text-info me-2"></i>Physical Assessment</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="general_condition" class="form-label">General Condition</label>
                                    <input type="text" id="general_condition" name="general_condition"
                                           class="form-control" value="{{ old('general_condition', $record->general_condition) }}" maxlength="100">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="breast_condition" class="form-label">Breast Condition</label>
                                    <input type="text" id="breast_condition" name="breast_condition"
                                           class="form-control" value="{{ old('breast_condition', $record->breast_condition) }}" maxlength="100">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="uterus_condition" class="form-label">Uterus Condition</label>
                                    <input type="text" id="uterus_condition" name="uterus_condition"
                                           class="form-control" value="{{ old('uterus_condition', $record->uterus_condition) }}" maxlength="100">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="perineum_condition" class="form-label">Perineum Condition</label>
                                    <input type="text" id="perineum_condition" name="perineum_condition"
                                           class="form-control" value="{{ old('perineum_condition', $record->perineum_condition) }}" maxlength="100">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="lochia_condition" class="form-label">Lochia Condition</label>
                                    <input type="text" id="lochia_condition" name="lochia_condition"
                                           class="form-control" value="{{ old('lochia_condition', $record->lochia_condition) }}" maxlength="100">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="episiotomy_condition" class="form-label">Episiotomy Condition</label>
                                    <input type="text" id="episiotomy_condition" name="episiotomy_condition"
                                           class="form-control" value="{{ old('episiotomy_condition', $record->episiotomy_condition) }}" maxlength="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mental Health & Mood -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-brain text-success me-2"></i>Mental Health & Mood</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="mood_assessment" class="form-label">Mood Assessment</label>
                                    <select id="mood_assessment" name="mood_assessment" class="form-select">
                                        <option value="">Select Assessment</option>
                                        <option value="Good" {{ $record->mood_assessment == 'Good' ? 'selected' : '' }}>Good</option>
                                        <option value="Fair" {{ $record->mood_assessment == 'Fair' ? 'selected' : '' }}>Fair</option>
                                        <option value="Poor" {{ $record->mood_assessment == 'Poor' ? 'selected' : '' }}>Poor</option>
                                        <option value="Concerning" {{ $record->mood_assessment == 'Concerning' ? 'selected' : '' }}>Concerning</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="emotional_support_needs" name="emotional_support_needs" value="1"
                                               {{ old('emotional_support_needs', $record->emotional_support_needs) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="emotional_support_needs">
                                            Emotional support needs identified
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="postpartum_depression_screening" name="postpartum_depression_screening" value="1"
                                               {{ old('postpartum_depression_screening', $record->postpartum_depression_screening) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="postpartum_depression_screening">
                                            Postpartum depression screening performed
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="mental_health_notes" class="form-label">Mental Health Notes</label>
                                    <textarea id="mental_health_notes" name="mental_health_notes"
                                              class="form-control" rows="3">{{ old('mental_health_notes', $record->mental_health_notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Breastfeeding & Infant Care -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-baby text-secondary me-2"></i>Breastfeeding & Infant Care</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="breastfeeding_status" class="form-label">Breastfeeding Status</label>
                                    <select id="breastfeeding_status" name="breastfeeding_status" class="form-select">
                                        <option value="">Select Status</option>
                                        <option value="Exclusive" {{ $record->breastfeeding_status == 'Exclusive' ? 'selected' : '' }}>Exclusive Breastfeeding</option>
                                        <option value="Mixed" {{ $record->breastfeeding_status == 'Mixed' ? 'selected' : '' }}>Mixed Feeding</option>
                                        <option value="Formula" {{ $record->breastfeeding_status == 'Formula' ? 'selected' : '' }}>Formula Only</option>
                                        <option value="None" {{ $record->breastfeeding_status == 'None' ? 'selected' : '' }}>Not Breastfeeding</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="breastfeeding_challenges" class="form-label">Breastfeeding Challenges</label>
                                    <textarea id="breastfeeding_challenges" name="breastfeeding_challenges"
                                              class="form-control" rows="2">{{ old('breastfeeding_challenges', $record->breastfeeding_challenges) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="lactation_support" class="form-label">Lactation Support</label>
                                    <textarea id="lactation_support" name="lactation_support"
                                              class="form-control" rows="2">{{ old('lactation_support', $record->lactation_support) }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="infant_feeding_assessment" name="infant_feeding_assessment" value="1"
                                               {{ old('infant_feeding_assessment', $record->infant_feeding_assessment) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="infant_feeding_assessment">
                                            Infant feeding assessment performed
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="infant_care_education" class="form-label">Infant Care Education</label>
                                    <textarea id="infant_care_education" name="infant_care_education"
                                              class="form-control" rows="2">{{ old('infant_care_education', $record->infant_care_education) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contraception & Family Planning -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-pills text-danger me-2"></i>Contraception & Family Planning</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contraceptive_method" class="form-label">Contraceptive Method</label>
                                    <input type="text" id="contraceptive_method" name="contraceptive_method"
                                           class="form-control" value="{{ old('contraceptive_method', $record->contraceptive_method) }}" maxlength="100">
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="family_planning_counseling" name="family_planning_counseling" value="1"
                                               {{ old('family_planning_counseling', $record->family_planning_counseling) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="family_planning_counseling">
                                            Family planning counseling provided
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="next_contraceptive_visit" class="form-label">Next Contraceptive Visit</label>
                                    <input type="date" id="next_contraceptive_visit" name="next_contraceptive_visit"
                                           class="form-control" value="{{ old('next_contraceptive_visit', $record->next_contraceptive_visit ? $record->next_contraceptive_visit->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Complications and Assessment -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Complications and Assessment</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="postpartum_complications" class="form-label">Postpartum Complications</label>
                                    <textarea id="postpartum_complications" name="postpartum_complications"
                                              class="form-control" rows="3">{{ old('postpartum_complications', $record->postpartum_complications) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="medications_prescribed" class="form-label">Medications Prescribed</label>
                                    <textarea id="medications_prescribed" name="medications_prescribed"
                                              class="form-control" rows="3">{{ old('medications_prescribed', $record->medications_prescribed) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="wound_care_instructions" class="form-label">Wound Care Instructions</label>
                                    <textarea id="wound_care_instructions" name="wound_care_instructions"
                                              class="form-control" rows="3">{{ old('wound_care_instructions', $record->wound_care_instructions) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="activity_restrictions" class="form-label">Activity Restrictions</label>
                                    <textarea id="activity_restrictions" name="activity_restrictions"
                                              class="form-control" rows="3">{{ old('activity_restrictions', $record->activity_restrictions) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="assessment_notes" class="form-label">Assessment Notes</label>
                                    <textarea id="assessment_notes" name="assessment_notes"
                                              class="form-control" rows="3">{{ old('assessment_notes', $record->assessment_notes) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="plan_notes" class="form-label">Plan Notes</label>
                                    <textarea id="plan_notes" name="plan_notes"
                                              class="form-control" rows="3">{{ old('plan_notes', $record->plan_notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Follow-up and Education -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-calendar-check text-success me-2"></i>Follow-up & Education</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="follow_up_date" class="form-label">Follow-up Date</label>
                                    <input type="date" id="follow_up_date" name="follow_up_date"
                                           class="form-control" value="{{ old('follow_up_date', $record->follow_up_date ? $record->follow_up_date->format('Y-m-d') : '') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="follow_up_reason" class="form-label">Follow-up Reason</label>
                                    <input type="text" id="follow_up_reason" name="follow_up_reason"
                                           class="form-control" value="{{ old('follow_up_reason', $record->follow_up_reason) }}" maxlength="200">
                                </div>
                                <div class="mb-3">
                                    <label for="nutrition_counseling" class="form-label">Nutrition Counseling</label>
                                    <textarea id="nutrition_counseling" name="nutrition_counseling"
                                              class="form-control" rows="2">{{ old('nutrition_counseling', $record->nutrition_counseling) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="exercise_guidance" class="form-label">Exercise Guidance</label>
                                    <textarea id="exercise_guidance" name="exercise_guidance"
                                              class="form-control" rows="2">{{ old('exercise_guidance', $record->exercise_guidance) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="warning_signs_education" class="form-label">Warning Signs Education</label>
                                    <textarea id="warning_signs_education" name="warning_signs_education"
                                              class="form-control" rows="2">{{ old('warning_signs_education', $record->warning_signs_education) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="{{ route('employee.patients.show-postpartum-record', [$patient->id, $record->id]) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Postpartum Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
