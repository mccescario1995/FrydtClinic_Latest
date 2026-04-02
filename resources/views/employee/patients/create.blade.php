@extends('employee.layouts.app')

@section('title', 'Add New Patient')

@section('css')
<style>
    .patient-form-wizard {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .wizard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        text-align: center;
    }

    .wizard-steps {
        display: flex;
        justify-content: space-between;
        padding: 20px;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .step {
        flex: 1;
        text-align: center;
        position: relative;
    }

    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .step.active .step-circle {
        background: #007bff;
        color: white;
    }

    .step.completed .step-circle {
        background: #28a745;
        color: white;
    }

    .step-title {
        font-size: 12px;
        font-weight: 500;
        color: #6c757d;
    }

    .step.active .step-title {
        color: #007bff;
        font-weight: 600;
    }

    .wizard-content {
        padding: 30px;
    }

    .form-section {
        margin-bottom: 30px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #007bff;
    }

    .section-header {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }

    .section-header i {
        margin-right: 10px;
        color: #007bff;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #333;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #e9ecef;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .required-asterisk {
        color: #dc3545;
        font-weight: bold;
    }

    .error-message {
        color: #dc3545;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }

    .wizard-navigation {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 30px;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }

    .btn-wizard {
        padding: 12px 24px;
        border-radius: 6px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .btn-wizard:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .progress-indicator {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        display: none;
    }

    @media (max-width: 768px) {
        .wizard-steps {
            flex-direction: column;
            gap: 10px;
        }

        .step {
            display: flex;
            align-items: center;
            text-align: left;
        }

        .step-circle {
            margin: 0 15px 0 0;
            flex-shrink: 0;
        }

        .wizard-navigation {
            flex-direction: column;
            gap: 10px;
        }

        .progress-indicator {
            position: static;
            margin-bottom: 20px;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <!-- Progress Indicator -->
            <div class="progress-indicator" id="progressIndicator">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-tasks me-2 text-primary"></i>
                    <span class="fw-bold">Form Progress</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 33%" id="progressBar"></div>
                </div>
                <small class="text-muted mt-1" id="progressText">Step 1 of 3</small>
            </div>

            <div class="patient-form-wizard">
                <!-- Wizard Header -->
                <div class="wizard-header">
                    <h2 class="mb-2"><i class="fas fa-user-plus me-2"></i>Add New Patient</h2>
                    <p class="mb-0 opacity-75">Complete the patient registration process</p>
                </div>

                <!-- Step Indicator -->
                <div class="wizard-steps">
                    <div class="step active" data-step="1">
                        <div class="step-circle">1</div>
                        <div class="step-title">Basic Info</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-circle">2</div>
                        <div class="step-title">Personal Details</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-circle">3</div>
                        <div class="step-title">Emergency Contact</div>
                    </div>
                </div>

                <!-- Form Content -->
                <form id="patientForm" method="POST" action="{{ route('employee.patients.store') }}">
                    @csrf

                    <!-- Step 1: Basic Information -->
                    <div class="wizard-content" id="step1">
                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-id-card"></i>
                                Basic Information
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="name">Full Name <span class="required-asterisk">*</span></label>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                           class="{{ $errors->has('name') ? 'is-invalid' : '' }}">
                                    @if($errors->has('name'))
                                        <span class="error-message">{{ $errors->first('name') }}</span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="email">Email Address <span class="required-asterisk">*</span></label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                           class="{{ $errors->has('email') ? 'is-invalid' : '' }}">
                                    @if($errors->has('email'))
                                        <span class="error-message">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="password">Password <span class="required-asterisk">*</span></label>
                                    <input type="password" id="password" name="password" required
                                           class="{{ $errors->has('password') ? 'is-invalid' : '' }}">
                                    @if($errors->has('password'))
                                        <span class="error-message">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password <span class="required-asterisk">*</span></label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Personal Information -->
                    <div class="wizard-content" id="step2" style="display: none;">
                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-user"></i>
                                Personal Information
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                                           class="{{ $errors->has('phone') ? 'is-invalid' : '' }}">
                                    @if($errors->has('phone'))
                                        <span class="error-message">{{ $errors->first('phone') }}</span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="birth_date">Birth Date</label>
                                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}"
                                           class="{{ $errors->has('birth_date') ? 'is-invalid' : '' }}">
                                    @if($errors->has('birth_date'))
                                        <span class="error-message">{{ $errors->first('birth_date') }}</span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select id="gender" name="gender" class="{{ $errors->has('gender') ? 'is-invalid' : '' }}">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @if($errors->has('gender'))
                                        <span class="error-message">{{ $errors->first('gender') }}</span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="civil_status">Civil Status</label>
                                    <select id="civil_status" name="civil_status" class="{{ $errors->has('civil_status') ? 'is-invalid' : '' }}">
                                        <option value="">Select Civil Status</option>
                                        <option value="single" {{ old('civil_status') === 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="married" {{ old('civil_status') === 'married' ? 'selected' : '' }}>Married</option>
                                        <option value="widowed" {{ old('civil_status') === 'widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="separated" {{ old('civil_status') === 'separated' ? 'selected' : '' }}>Separated</option>
                                    </select>
                                    @if($errors->has('civil_status'))
                                        <span class="error-message">{{ $errors->first('civil_status') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea id="address" name="address" rows="3"
                                          class="{{ $errors->has('address') ? 'is-invalid' : '' }}">{{ old('address') }}</textarea>
                                @if($errors->has('address'))
                                    <span class="error-message">{{ $errors->first('address') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Emergency Contact -->
                    <div class="wizard-content" id="step3" style="display: none;">
                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-phone"></i>
                                Emergency Contact Information
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Optional:</strong> Emergency contact information helps us reach someone in case of an emergency.
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="emergency_contact_name">Emergency Contact Name</label>
                                    <input type="text" id="emergency_contact_name" name="emergency_contact_name"
                                           value="{{ old('emergency_contact_name') }}"
                                           class="{{ $errors->has('emergency_contact_name') ? 'is-invalid' : '' }}">
                                    @if($errors->has('emergency_contact_name'))
                                        <span class="error-message">{{ $errors->first('emergency_contact_name') }}</span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="emergency_contact_relationship">Relationship</label>
                                    <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship"
                                           value="{{ old('emergency_contact_relationship') }}"
                                           placeholder="e.g., Spouse, Parent, Sibling"
                                           class="{{ $errors->has('emergency_contact_relationship') ? 'is-invalid' : '' }}">
                                    @if($errors->has('emergency_contact_relationship'))
                                        <span class="error-message">{{ $errors->first('emergency_contact_relationship') }}</span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="emergency_contact_phone">Emergency Contact Phone</label>
                                    <input type="text" id="emergency_contact_phone" name="emergency_contact_phone"
                                           value="{{ old('emergency_contact_phone') }}"
                                           class="{{ $errors->has('emergency_contact_phone') ? 'is-invalid' : '' }}">
                                    @if($errors->has('emergency_contact_phone'))
                                        <span class="error-message">{{ $errors->first('emergency_contact_phone') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="wizard-navigation">
                        <a href="{{ route('employee.patients') }}" class="btn btn-outline-secondary btn-wizard">
                            <i class="fas fa-arrow-left"></i>
                            Back to Patients
                        </a>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary btn-wizard" id="prevBtn" style="display: none;">
                                <i class="fas fa-chevron-left"></i>
                                Previous
                            </button>
                            <button type="button" class="btn btn-primary btn-wizard" id="nextBtn">
                                Next
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <button type="submit" class="btn btn-success btn-wizard" id="submitBtn" style="display: none;">
                                <i class="fas fa-save"></i>
                                Create Patient
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const steps = document.querySelectorAll('.wizard-content');
    const stepIndicators = document.querySelectorAll('.step');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const progressIndicator = document.getElementById('progressIndicator');

    let currentStep = 1;
    const totalSteps = steps.length;

    // Show progress indicator on larger screens only
    if (window.innerWidth > 768) {
        progressIndicator.style.display = 'block';
    }

    function updateUI() {
        steps.forEach((step, index) => {
            step.style.display = (index + 1 === currentStep) ? 'block' : 'none';
        });

        stepIndicators.forEach((indicator, index) => {
            indicator.classList.remove('active', 'completed');
            if (index + 1 === currentStep) {
                indicator.classList.add('active');
            } else if (index + 1 < currentStep) {
                indicator.classList.add('completed');
            }
        });

        prevBtn.style.display = currentStep === 1 ? 'none' : 'inline-flex';
        nextBtn.style.display = currentStep === totalSteps ? 'none' : 'inline-flex';
        submitBtn.style.display = currentStep === totalSteps ? 'inline-flex' : 'none';

        const progress = (currentStep / totalSteps) * 100;
        progressBar.style.width = `${progress}%`;
        progressText.textContent = `Step ${currentStep} of ${totalSteps}`;

        // Scroll into view smoothly
        document.querySelector('.patient-form-wizard')
            .scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function validateStep(stepNumber) {
        const currentStepEl = document.getElementById(`step${stepNumber}`);
        const requiredFields = currentStepEl.querySelectorAll('[required]');
        let valid = true;

        requiredFields.forEach(field => {
            const value = field.value.trim();
            if (!value) {
                field.classList.add('is-invalid');
                valid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        return valid;
    }

    nextBtn.addEventListener('click', function() {
        console.log("Next button clicked");
    console.log("Current Step:", currentStep);
        if (!validateStep(currentStep)) {
            console.log("Step validation failed");
            const firstInvalid = document.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }
            return;
        }

        if (currentStep < totalSteps) {
            currentStep++;
             console.log("Moving to Step:", currentStep);
            updateUI();
        }
    });

    prevBtn.addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            updateUI();
        }
    });

    // Password confirmation validation
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');

    function validatePasswordMatch() {
        if (password.value && passwordConfirm.value && password.value !== passwordConfirm.value) {
            passwordConfirm.setCustomValidity('Passwords do not match');
            passwordConfirm.classList.add('is-invalid');
        } else {
            passwordConfirm.setCustomValidity('');
            passwordConfirm.classList.remove('is-invalid');
        }
    }

    password.addEventListener('input', validatePasswordMatch);
    passwordConfirm.addEventListener('input', validatePasswordMatch);

    // Initialize
    updateUI();
});
</script>
@endsection

