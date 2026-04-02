@extends('employee.layouts.app')

@section('title', 'Edit Delivery Record - ' . $patient->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Delivery Record for {{ $patient->name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('employee.patients.show-delivery-record', [$patient->id, $record->id]) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-eye"></i> View Record
                        </a>
                        <a href="{{ route('employee.patients.delivery-records', $patient->id) }}" class="btn btn-outline-secondary btn-sm">
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

            <form method="POST" action="{{ route('employee.patients.update-delivery-record', [$patient->id, $record->id]) }}">
                @csrf
                @method('PUT')

                <!-- Providers -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-md text-primary me-2"></i>Providers</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="attending_provider_id" class="form-label">Attending Provider <span class="text-danger">*</span></label>
                                    <select id="attending_provider_id" name="attending_provider_id" class="form-select" required>
                                        <option value="">Select Provider</option>
                                        @foreach($providers as $provider)
                                            <option value="{{ $provider->id }}" {{ $record->attending_provider_id == $provider->id ? 'selected' : '' }}>
                                                {{ $provider->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('attending_provider_id'))
                                        <div class="text-danger small mt-1">{{ $errors->first('attending_provider_id') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="delivering_provider_id" class="form-label">Delivering Provider</label>
                                    <select id="delivering_provider_id" name="delivering_provider_id" class="form-select">
                                        <option value="">Select Provider</option>
                                        @foreach($providers as $provider)
                                            <option value="{{ $provider->id }}" {{ $record->delivering_provider_id == $provider->id ? 'selected' : '' }}>
                                                {{ $provider->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="anesthesiologist_id" class="form-label">Anesthesiologist</label>
                                    <select id="anesthesiologist_id" name="anesthesiologist_id" class="form-select">
                                        <option value="">Select Provider</option>
                                        @foreach($providers as $provider)
                                            <option value="{{ $provider->id }}" {{ $record->anesthesiologist_id == $provider->id ? 'selected' : '' }}>
                                                {{ $provider->name }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                    <label for="admission_date_time" class="form-label">Admission Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" id="admission_date_time" name="admission_date_time"
                                           class="form-control" value="{{ old('admission_date_time', $record->admission_date_time ? \Carbon\Carbon::parse($record->admission_date_time)->format('Y-m-d\TH:i') : '') }}" required>
                                    @if($errors->has('admission_date_time'))
                                        <div class="text-danger small mt-1">{{ $errors->first('admission_date_time') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="labor_onset_date_time" class="form-label">Labor Onset Date & Time</label>
                                    <input type="datetime-local" id="labor_onset_date_time" name="labor_onset_date_time"
                                           class="form-control" value="{{ old('labor_onset_date_time', $record->labor_onset_date_time ? \Carbon\Carbon::parse($record->labor_onset_date_time)->format('Y-m-d\TH:i') : '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rupture_of_membranes_date_time" class="form-label">Rupture of Membranes Date & Time</label>
                                    <input type="datetime-local" id="rupture_of_membranes_date_time" name="rupture_of_membranes_date_time"
                                           class="form-control" value="{{ old('rupture_of_membranes_date_time', $record->rupture_of_membranes_date_time ? \Carbon\Carbon::parse($record->rupture_of_membranes_date_time)->format('Y-m-d\TH:i') : '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rupture_of_membranes_type" class="form-label">Rupture of Membranes Type</label>
                                    <select id="rupture_of_membranes_type" name="rupture_of_membranes_type" class="form-select">
                                        <option value="">Select Type</option>
                                        <option value="Spontaneous" {{ $record->rupture_of_membranes_type == 'Spontaneous' ? 'selected' : '' }}>Spontaneous</option>
                                        <option value="Artificial" {{ $record->rupture_of_membranes_type == 'Artificial' ? 'selected' : '' }}>Artificial</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delivery_date_time" class="form-label">Delivery Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" id="delivery_date_time" name="delivery_date_time"
                                           class="form-control" value="{{ old('delivery_date_time', $record->delivery_date_time ? \Carbon\Carbon::parse($record->delivery_date_time)->format('Y-m-d\TH:i') : '') }}" required>
                                    @if($errors->has('delivery_date_time'))
                                        <div class="text-danger small mt-1">{{ $errors->first('delivery_date_time') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="delivery_type" class="form-label">Delivery Type</label>
                                    <select id="delivery_type" name="delivery_type" class="form-select">
                                        <option value="">Select Type</option>
                                        <option value="Vaginal" {{ $record->delivery_type == 'Vaginal' ? 'selected' : '' }}>Vaginal</option>
                                        <option value="Cesarean" {{ $record->delivery_type == 'Cesarean' ? 'selected' : '' }}>Cesarean</option>
                                        <option value="Forceps" {{ $record->delivery_type == 'Forceps' ? 'selected' : '' }}>Forceps</option>
                                        <option value="Vacuum" {{ $record->delivery_type == 'Vacuum' ? 'selected' : '' }}>Vacuum</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="delivery_place" class="form-label">Delivery Place</label>
                                    <input type="text" id="delivery_place" name="delivery_place"
                                           class="form-control" value="{{ old('delivery_place', $record->delivery_place) }}" maxlength="100">
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
                                    <label for="gravida" class="form-label">Gravida</label>
                                    <input type="number" id="gravida" name="gravida"
                                           class="form-control" value="{{ old('gravida', $record->gravida) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="para" class="form-label">Para</label>
                                    <input type="number" id="para" name="para"
                                           class="form-control" value="{{ old('para', $record->para) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="living_children" class="form-label">Living Children</label>
                                    <input type="number" id="living_children" name="living_children"
                                           class="form-control" value="{{ old('living_children', $record->living_children) }}" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prenatal_history" class="form-label">Prenatal History</label>
                                    <textarea id="prenatal_history" name="prenatal_history"
                                              class="form-control" rows="3">{{ old('prenatal_history', $record->prenatal_history) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="risk_factors" class="form-label">Risk Factors</label>
                                    <textarea id="risk_factors" name="risk_factors"
                                              class="form-control" rows="3">{{ old('risk_factors', $record->risk_factors) }}</textarea>
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
                                    <label for="labor_duration_hours" class="form-label">Labor Duration Hours</label>
                                    <input type="number" id="labor_duration_hours" name="labor_duration_hours"
                                           class="form-control" value="{{ old('labor_duration_hours', $record->labor_duration_hours) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="labor_duration_minutes" class="form-label">Labor Duration Minutes</label>
                                    <input type="number" id="labor_duration_minutes" name="labor_duration_minutes"
                                           class="form-control" value="{{ old('labor_duration_minutes', $record->labor_duration_minutes) }}" min="0" max="59">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="presentation" class="form-label">Presentation</label>
                                    <select id="presentation" name="presentation" class="form-select">
                                        <option value="">Select Presentation</option>
                                        <option value="Cephalic" {{ $record->presentation == 'Cephalic' ? 'selected' : '' }}>Cephalic</option>
                                        <option value="Breech" {{ $record->presentation == 'Breech' ? 'selected' : '' }}>Breech</option>
                                        <option value="Transverse" {{ $record->presentation == 'Transverse' ? 'selected' : '' }}>Transverse</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="position" class="form-label">Position</label>
                                    <input type="text" id="position" name="position"
                                           class="form-control" value="{{ old('position', $record->position) }}" maxlength="50">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="labor_progress" class="form-label">Labor Progress</label>
                                    <textarea id="labor_progress" name="labor_progress"
                                              class="form-control" rows="2">{{ old('labor_progress', $record->labor_progress) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="labor_complications" class="form-label">Labor Complications</label>
                                    <textarea id="labor_complications" name="labor_complications"
                                              class="form-control" rows="2">{{ old('labor_complications', $record->labor_complications) }}</textarea>
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
                                    <label for="episiotomy_performed" class="form-label">Episiotomy Performed</label>
                                    <select id="episiotomy_performed" name="episiotomy_performed" class="form-select">
                                        <option value="">Select</option>
                                        <option value="1" {{ old('episiotomy_performed', $record->episiotomy_performed) == '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('episiotomy_performed', $record->episiotomy_performed) === '0' || (!old('episiotomy_performed') && !$record->episiotomy_performed) ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="episiotomy_degree" class="form-label">Episiotomy Degree</label>
                                    <input type="text" id="episiotomy_degree" name="episiotomy_degree"
                                           class="form-control" value="{{ old('episiotomy_degree', $record->episiotomy_degree) }}" maxlength="20">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="anesthesia_type" class="form-label">Anesthesia Type</label>
                                    <select id="anesthesia_type" name="anesthesia_type" class="form-select">
                                        <option value="">Select Type</option>
                                        <option value="None" {{ $record->anesthesia_type == 'None' ? 'selected' : '' }}>None</option>
                                        <option value="Local" {{ $record->anesthesia_type == 'Local' ? 'selected' : '' }}>Local</option>
                                        <option value="Epidural" {{ $record->anesthesia_type == 'Epidural' ? 'selected' : '' }}>Epidural</option>
                                        <option value="Spinal" {{ $record->anesthesia_type == 'Spinal' ? 'selected' : '' }}>Spinal</option>
                                        <option value="General" {{ $record->anesthesia_type == 'General' ? 'selected' : '' }}>General</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="perineal_tear" class="form-label">Perineal Tear</label>
                                    <textarea id="perineal_tear" name="perineal_tear"
                                              class="form-control" rows="2">{{ old('perineal_tear', $record->perineal_tear) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delivery_complications" class="form-label">Delivery Complications</label>
                                    <textarea id="delivery_complications" name="delivery_complications"
                                              class="form-control" rows="2">{{ old('delivery_complications', $record->delivery_complications) }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="anesthesia_notes" class="form-label">Anesthesia Notes</label>
                                    <textarea id="anesthesia_notes" name="anesthesia_notes"
                                              class="form-control" rows="2">{{ old('anesthesia_notes', $record->anesthesia_notes) }}</textarea>
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
                                    <label for="newborn_gender" class="form-label">Newborn Gender</label>
                                    <select id="newborn_gender" name="newborn_gender" class="form-select">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ $record->newborn_gender == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ $record->newborn_gender == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="newborn_weight" class="form-label">Newborn Weight (kg)</label>
                                    <input type="number" id="newborn_weight" name="newborn_weight" step="0.01"
                                           class="form-control" value="{{ old('newborn_weight', $record->newborn_weight) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="newborn_length" class="form-label">Newborn Length (cm)</label>
                                    <input type="number" id="newborn_length" name="newborn_length" step="0.1"
                                           class="form-control" value="{{ old('newborn_length', $record->newborn_length) }}" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="newborn_apgar_1min" class="form-label">APGAR Score 1min</label>
                                    <input type="number" id="newborn_apgar_1min" name="newborn_apgar_1min"
                                           class="form-control" value="{{ old('newborn_apgar_1min', $record->newborn_apgar_1min) }}" min="0" max="10">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="newborn_apgar_5min" class="form-label">APGAR Score 5min</label>
                                    <input type="number" id="newborn_apgar_5min" name="newborn_apgar_5min"
                                           class="form-control" value="{{ old('newborn_apgar_5min', $record->newborn_apgar_5min) }}" min="0" max="10">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="newborn_apgar_10min" class="form-label">APGAR Score 10min</label>
                                    <input type="number" id="newborn_apgar_10min" name="newborn_apgar_10min"
                                           class="form-control" value="{{ old('newborn_apgar_10min', $record->newborn_apgar_10min) }}" min="0" max="10">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="newborn_condition" class="form-label">Newborn Condition</label>
                                    <textarea id="newborn_condition" name="newborn_condition"
                                              class="form-control" rows="2">{{ old('newborn_condition', $record->newborn_condition) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="newborn_complications" class="form-label">Newborn Complications</label>
                                    <textarea id="newborn_complications" name="newborn_complications"
                                              class="form-control" rows="2">{{ old('newborn_complications', $record->newborn_complications) }}</textarea>
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
                                    <label for="estimated_blood_loss" class="form-label">Estimated Blood Loss (mL)</label>
                                    <input type="number" id="estimated_blood_loss" name="estimated_blood_loss"
                                           class="form-control" value="{{ old('estimated_blood_loss', $record->estimated_blood_loss) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="blood_pressure_systolic" class="form-label">Blood Pressure Systolic</label>
                                    <input type="number" id="blood_pressure_systolic" name="blood_pressure_systolic"
                                           class="form-control" value="{{ old('blood_pressure_systolic', $record->blood_pressure_systolic) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="blood_pressure_diastolic" class="form-label">Blood Pressure Diastolic</label>
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
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="temperature" class="form-label">Temperature (°C)</label>
                                    <input type="number" id="temperature" name="temperature" step="0.1"
                                       class="form-control" value="{{ old('temperature', $record->temperature) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="placenta_delivery" class="form-label">Placenta Delivery</label>
                                    <select id="placenta_delivery" name="placenta_delivery" class="form-select">
                                        <option value="">Select</option>
                                        <option value="Spontaneous" {{ $record->placenta_delivery == 'Spontaneous' ? 'selected' : '' }}>Spontaneous</option>
                                        <option value="Manual" {{ $record->placenta_delivery == 'Manual' ? 'selected' : '' }}>Manual</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="placenta_complete" class="form-label">Placenta Complete</label>
                                    <select id="placenta_complete" name="placenta_complete" class="form-select">
                                        <option value="">Select</option>
                                        <option value="1" {{ old('placenta_complete', $record->placenta_complete) == '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('placenta_complete', $record->placenta_complete) === '0' || (!old('placenta_complete') && !$record->placenta_complete) ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="placenta_notes" class="form-label">Placenta Notes</label>
                                    <textarea id="placenta_notes" name="placenta_notes"
                                              class="form-control" rows="2">{{ old('placenta_notes', $record->placenta_notes) }}</textarea>
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
                                    <label for="postpartum_care" class="form-label">Postpartum Care</label>
                                    <textarea id="postpartum_care" name="postpartum_care"
                                              class="form-control" rows="3">{{ old('postpartum_care', $record->postpartum_care) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="medications_administered" class="form-label">Medications Administered</label>
                                    <textarea id="medications_administered" name="medications_administered"
                                              class="form-control" rows="3">{{ old('medications_administered', $record->medications_administered) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="breastfeeding_initiation" class="form-label">Breastfeeding Initiation</label>
                                    <textarea id="breastfeeding_initiation" name="breastfeeding_initiation"
                                              class="form-control" rows="3">{{ old('breastfeeding_initiation', $record->breastfeeding_initiation) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expected_discharge_date" class="form-label">Expected Discharge Date</label>
                                    <input type="date" id="expected_discharge_date" name="expected_discharge_date"
                                           class="form-control" value="{{ old('expected_discharge_date', $record->expected_discharge_date ? \Carbon\Carbon::parse($record->expected_discharge_date)->format('Y-m-d') : '') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="discharge_instructions" class="form-label">Discharge Instructions</label>
                                    <textarea id="discharge_instructions" name="discharge_instructions"
                                              class="form-control" rows="3">{{ old('discharge_instructions', $record->discharge_instructions) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="follow_up_instructions" class="form-label">Follow-up Instructions</label>
                                    <textarea id="follow_up_instructions" name="follow_up_instructions"
                                              class="form-control" rows="3">{{ old('follow_up_instructions', $record->follow_up_instructions) }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delivery_summary" class="form-label">Delivery Summary</label>
                                    <textarea id="delivery_summary" name="delivery_summary"
                                              class="form-control" rows="4">{{ old('delivery_summary', $record->delivery_summary) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="additional_notes" class="form-label">Additional Notes</label>
                                    <textarea id="additional_notes" name="additional_notes"
                                              class="form-control" rows="4">{{ old('additional_notes', $record->additional_notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="{{ route('employee.patients.show-delivery-record', [$patient->id, $record->id]) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Delivery Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
