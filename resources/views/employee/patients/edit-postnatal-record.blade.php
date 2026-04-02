@extends('employee.layouts.app')

@section('title', 'Edit Postnatal Record - ' . $patient->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Postnatal Record for {{ $patient->name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('employee.patients.show-postnatal-record', [$patient->id, $record->id]) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-eye"></i> View Record
                        </a>
                        <a href="{{ route('employee.patients.postnatal-records', $patient->id) }}" class="btn btn-outline-secondary btn-sm">
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

            <form method="POST" action="{{ route('employee.patients.update-postnatal-record', [$patient->id, $record->id]) }}">
                @csrf
                @method('PUT')

                <!-- Provider and Visit Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-md text-primary me-2"></i>Provider & Visit Information</h5>
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
                                    <label for="days_postpartum" class="form-label">Days Postpartum</label>
                                    <input type="number" id="days_postpartum" name="days_postpartum"
                                           class="form-control" value="{{ old('days_postpartum', $record->days_postpartum) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="weeks_postpartum" class="form-label">Weeks Postpartum</label>
                                    <input type="number" id="weeks_postpartum" name="weeks_postpartum"
                                           class="form-control" value="{{ old('weeks_postpartum', $record->weeks_postpartum) }}" min="0">
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="temperature" class="form-label">Temperature (°C)</label>
                                    <input type="number" id="temperature" name="temperature" step="0.1"
                                           class="form-control" value="{{ old('temperature', $record->temperature) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="respiratory_rate" class="form-label">Respiratory Rate</label>
                                    <input type="number" id="respiratory_rate" name="respiratory_rate"
                                           class="form-control" value="{{ old('respiratory_rate', $record->respiratory_rate) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="oxygen_saturation" class="form-label">Oxygen Saturation (%)</label>
                                    <input type="number" id="oxygen_saturation" name="oxygen_saturation"
                                           class="form-control" value="{{ old('oxygen_saturation', $record->oxygen_saturation) }}" min="0" max="100">
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

                <!-- Breastfeeding & Newborn -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-baby text-warning me-2"></i>Breastfeeding & Newborn</h5>
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
                                    <label for="latch_assessment" class="form-label">Latch Assessment</label>
                                    <select id="latch_assessment" name="latch_assessment" class="form-select">
                                        <option value="">Select Assessment</option>
                                        <option value="Good" {{ $record->latch_assessment == 'Good' ? 'selected' : '' }}>Good</option>
                                        <option value="Fair" {{ $record->latch_assessment == 'Fair' ? 'selected' : '' }}>Fair</option>
                                        <option value="Poor" {{ $record->latch_assessment == 'Poor' ? 'selected' : '' }}>Poor</option>
                                        <option value="N/A" {{ $record->latch_assessment == 'N/A' ? 'selected' : '' }}>N/A</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="newborn_check" name="newborn_check" value="1"
                                               {{ old('newborn_check', $record->newborn_check) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="newborn_check">
                                            Newborn check performed
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="newborn_weight" class="form-label">Newborn Weight (kg)</label>
                                    <input type="number" id="newborn_weight" name="newborn_weight" step="0.01"
                                           class="form-control" value="{{ old('newborn_weight', $record->newborn_weight) }}" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes and Assessment -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-notes-medical text-secondary me-2"></i>Notes and Assessment</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="chief_complaint" class="form-label">Chief Complaint</label>
                                    <textarea id="chief_complaint" name="chief_complaint"
                                              class="form-control" rows="3">{{ old('chief_complaint', $record->chief_complaint) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="breastfeeding_notes" class="form-label">Breastfeeding Notes</label>
                                    <textarea id="breastfeeding_notes" name="breastfeeding_notes"
                                              class="form-control" rows="3">{{ old('breastfeeding_notes', $record->breastfeeding_notes) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="newborn_notes" class="form-label">Newborn Notes</label>
                                    <textarea id="newborn_notes" name="newborn_notes"
                                              class="form-control" rows="3">{{ old('newborn_notes', $record->newborn_notes) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="assessment" class="form-label">Assessment</label>
                                    <textarea id="assessment" name="assessment"
                                              class="form-control" rows="3">{{ old('assessment', $record->assessment) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="plan" class="form-label">Plan</label>
                                    <textarea id="plan" name="plan"
                                              class="form-control" rows="3">{{ old('plan', $record->plan) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="instructions_given" class="form-label">Instructions Given</label>
                                    <textarea id="instructions_given" name="instructions_given"
                                              class="form-control" rows="3">{{ old('instructions_given', $record->instructions_given) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Family Planning and Follow-up -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-calendar-check text-success me-2"></i>Family Planning & Follow-up</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="family_planning_method" class="form-label">Family Planning Method</label>
                                    <input type="text" id="family_planning_method" name="family_planning_method"
                                           class="form-control" value="{{ old('family_planning_method', $record->family_planning_method) }}" maxlength="100">
                                </div>
                                <div class="mb-3">
                                    <label for="family_planning_counseling" class="form-label">Family Planning Counseling</label>
                                    <textarea id="family_planning_counseling" name="family_planning_counseling"
                                              class="form-control" rows="2">{{ old('family_planning_counseling', $record->family_planning_counseling) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="follow_up_date" class="form-label">Follow-up Date</label>
                                    <input type="date" id="follow_up_date" name="follow_up_date"
                                           class="form-control" value="{{ old('follow_up_date', $record->follow_up_date ? $record->follow_up_date->format('Y-m-d') : '') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="next_visit_type" class="form-label">Next Visit Type</label>
                                    <input type="text" id="next_visit_type" name="next_visit_type"
                                           class="form-control" value="{{ old('next_visit_type', $record->next_visit_type) }}" maxlength="100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Additional Notes</label>
                                    <textarea id="notes" name="notes"
                                              class="form-control" rows="6">{{ old('notes', $record->notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="{{ route('employee.patients.show-postnatal-record', [$patient->id, $record->id]) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Postnatal Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
