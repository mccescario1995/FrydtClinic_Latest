@extends('admin-portal.layouts.app')

@section('title', 'Create New Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-plus-circle me-2"></i>Create New Service
    </h1>
    <p class="page-subtitle">Add a new service to the clinic offerings</p>
</div>

<div class="admin-card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Service Information</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin-portal.services.store') }}">
            @csrf

            <!-- Basic Information -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="section-title">Basic Information</h6>

                    <div class="mb-3">
                        <label for="name" class="form-label">Service Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">Service Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror"
                               id="code" name="code" value="{{ old('code') }}" required>
                        <div class="form-text">Unique code for billing and identification</div>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="section-title">Service Classification</h6>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="single" {{ old('type') === 'single' ? 'selected' : '' }}>Single Service</option>
                            <option value="package" {{ old('type') === 'package' ? 'selected' : '' }}>Package Service</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="service_type" class="form-label">Service Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('service_type') is-invalid @enderror" id="service_type" name="service_type" required>
                            <option value="">Select Service Type</option>
                            <option value="consultation" {{ old('service_type') === 'consultation' ? 'selected' : '' }}>Consultation</option>
                            <option value="procedure" {{ old('service_type') === 'procedure' ? 'selected' : '' }}>Procedure</option>
                            <option value="laboratory" {{ old('service_type') === 'laboratory' ? 'selected' : '' }}>Laboratory</option>
                            <option value="imaging" {{ old('service_type') === 'imaging' ? 'selected' : '' }}>Imaging</option>
                            <option value="therapy" {{ old('service_type') === 'therapy' ? 'selected' : '' }}>Therapy</option>
                            <option value="vaccination" {{ old('service_type') === 'vaccination' ? 'selected' : '' }}>Vaccination</option>
                            <option value="prenatal_care" {{ old('service_type') === 'prenatal_care' ? 'selected' : '' }}>Prenatal Care</option>
                            <option value="delivery" {{ old('service_type') === 'delivery' ? 'selected' : '' }}>Delivery</option>
                            <option value="postnatal_care" {{ old('service_type') === 'postnatal_care' ? 'selected' : '' }}>Postnatal Care</option>
                            <option value="other" {{ old('service_type') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('service_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="general_practice" {{ old('category') === 'general_practice' ? 'selected' : '' }}>General Practice</option>
                            <option value="obstetrics_gynecology" {{ old('category') === 'obstetrics_gynecology' ? 'selected' : '' }}>Obstetrics & Gynecology</option>
                            <option value="pediatrics" {{ old('category') === 'pediatrics' ? 'selected' : '' }}>Pediatrics</option>
                            <option value="internal_medicine" {{ old('category') === 'internal_medicine' ? 'selected' : '' }}>Internal Medicine</option>
                            <option value="surgery" {{ old('category') === 'surgery' ? 'selected' : '' }}>Surgery</option>
                            <option value="emergency_care" {{ old('category') === 'emergency_care' ? 'selected' : '' }}>Emergency Care</option>
                            <option value="preventive_care" {{ old('category') === 'preventive_care' ? 'selected' : '' }}>Preventive Care</option>
                            <option value="diagnostic" {{ old('category') === 'diagnostic' ? 'selected' : '' }}>Diagnostic</option>
                            <option value="therapeutic" {{ old('category') === 'therapeutic' ? 'selected' : '' }}>Therapeutic</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Pricing Information -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="section-title">Pricing Information</h6>

                    <div class="mb-3">
                        <label for="base_price" class="form-label">Base Price (₱) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('base_price') is-invalid @enderror"
                               id="base_price" name="base_price" value="{{ old('base_price') }}" step="0.01" min="0" required>
                        @error('base_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="philhealth_price" class="form-label">PhilHealth Price (₱)</label>
                        <input type="number" class="form-control @error('philhealth_price') is-invalid @enderror"
                               id="philhealth_price" name="philhealth_price" value="{{ old('philhealth_price') }}" step="0.01" min="0">
                        @error('philhealth_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="discount_percentage" class="form-label">Discount Percentage (%)</label>
                        <input type="number" class="form-control @error('discount_percentage') is-invalid @enderror"
                               id="discount_percentage" name="discount_percentage" value="{{ old('discount_percentage') }}" step="0.01" min="0" max="100">
                        @error('discount_percentage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="section-title">PhilHealth Coverage</h6>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="philhealth_covered" name="philhealth_covered" value="1"
                                   {{ old('philhealth_covered') ? 'checked' : '' }}>
                            <label class="form-check-label" for="philhealth_covered">
                                Covered by PhilHealth
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Details -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="section-title">Service Details</h6>

                    <div class="mb-3">
                        <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                        <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror"
                               id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes') }}" min="1">
                        @error('duration_minutes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="advance_booking_days" class="form-label">Advance Booking Days</label>
                        <input type="number" class="form-control @error('advance_booking_days') is-invalid @enderror"
                               id="advance_booking_days" name="advance_booking_days" value="{{ old('advance_booking_days') }}" min="0">
                        @error('advance_booking_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                       id="start_time" name="start_time" value="{{ old('start_time') }}">
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                       id="end_time" name="end_time" value="{{ old('end_time') }}">
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="section-title">Service Requirements</h6>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="requires_appointment" name="requires_appointment" value="1"
                                   {{ old('requires_appointment', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_appointment">
                                Requires Appointment
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="available_emergency" name="available_emergency" value="1"
                                   {{ old('available_emergency') ? 'checked' : '' }}>
                            <label class="form-check-label" for="available_emergency">
                                Available for Emergency
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="requires_lab_results" name="requires_lab_results" value="1"
                                   {{ old('requires_lab_results') ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_lab_results">
                                Requires Lab Results
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions and Notes -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="section-title">Patient Instructions</h6>

                    <div class="mb-3">
                        <label for="preparation_instructions" class="form-label">Preparation Instructions</label>
                        <textarea class="form-control @error('preparation_instructions') is-invalid @enderror"
                                  id="preparation_instructions" name="preparation_instructions" rows="3">{{ old('preparation_instructions') }}</textarea>
                        @error('preparation_instructions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="post_service_instructions" class="form-label">Post-Service Instructions</label>
                        <textarea class="form-control @error('post_service_instructions') is-invalid @enderror"
                                  id="post_service_instructions" name="post_service_instructions" rows="3">{{ old('post_service_instructions') }}</textarea>
                        @error('post_service_instructions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="contraindications" class="form-label">Contraindications</label>
                        <textarea class="form-control @error('contraindications') is-invalid @enderror"
                                  id="contraindications" name="contraindications" rows="3">{{ old('contraindications') }}</textarea>
                        @error('contraindications')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="section-title">Resource Requirements</h6>

                    <div class="mb-3">
                        <label for="required_equipment" class="form-label">Required Equipment</label>
                        <textarea class="form-control @error('required_equipment') is-invalid @enderror"
                                  id="required_equipment" name="required_equipment" rows="2">{{ old('required_equipment') }}</textarea>
                        @error('required_equipment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="required_supplies" class="form-label">Required Supplies</label>
                        <textarea class="form-control @error('required_supplies') is-invalid @enderror"
                                  id="required_supplies" name="required_supplies" rows="2">{{ old('required_supplies') }}</textarea>
                        @error('required_supplies')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="staff_requirements" class="form-label">Staff Requirements</label>
                        <textarea class="form-control @error('staff_requirements') is-invalid @enderror"
                                  id="staff_requirements" name="staff_requirements" rows="2">{{ old('staff_requirements') }}</textarea>
                        @error('staff_requirements')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Quality and Compliance -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="section-title">Quality & Compliance</h6>

                    <div class="mb-3">
                        <label for="quality_indicators" class="form-label">Quality Indicators</label>
                        <textarea class="form-control @error('quality_indicators') is-invalid @enderror"
                                  id="quality_indicators" name="quality_indicators" rows="2">{{ old('quality_indicators') }}</textarea>
                        @error('quality_indicators')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="regulatory_requirements" class="form-label">Regulatory Requirements</label>
                        <textarea class="form-control @error('regulatory_requirements') is-invalid @enderror"
                                  id="regulatory_requirements" name="regulatory_requirements" rows="2">{{ old('regulatory_requirements') }}</textarea>
                        @error('regulatory_requirements')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="section-title">Documentation</h6>

                    <div class="mb-3">
                        <label for="consent_form_required" class="form-label">Consent Form Required</label>
                        <textarea class="form-control @error('consent_form_required') is-invalid @enderror"
                                  id="consent_form_required" name="consent_form_required" rows="2">{{ old('consent_form_required') }}</textarea>
                        @error('consent_form_required')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="documentation_requirements" class="form-label">Documentation Requirements</label>
                        <textarea class="form-control @error('documentation_requirements') is-invalid @enderror"
                                  id="documentation_requirements" name="documentation_requirements" rows="2">{{ old('documentation_requirements') }}</textarea>
                        @error('documentation_requirements')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="internal_notes" class="form-label">Internal Notes</label>
                        <textarea class="form-control @error('internal_notes') is-invalid @enderror"
                                  id="internal_notes" name="internal_notes" rows="2">{{ old('internal_notes') }}</textarea>
                        @error('internal_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin-portal.services') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Services
                </a>
                <button type="submit" class="btn btn-admin-primary">
                    <i class="fas fa-save me-1"></i>Create Service
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
