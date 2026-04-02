@extends('admin-portal.layouts.app')

@section('title', 'Create New Patient')

@section('css')
<style>
    /* Glass Card */
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .glass-card:hover {
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
    }

    /* Stepper */
    .stepper {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        position: relative;
    }

    .step {
        text-align: center;
        flex: 1;
        position: relative;
        z-index: 1;
    }

    .step:before {
        content: '';
        position: absolute;
        top: 12px;
        left: 50%;
        width: 100%;
        height: 3px;
        background: #dee2e6;
        z-index: -1;
        transform: translateX(-50%);
    }

    .step:first-child:before {
        left: 50%;
        width: 50%;
    }

    .step:last-child:before {
        width: 50%;
    }

    .step-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #dee2e6;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 5px;
        font-weight: bold;
        color: #6c757d;
        transition: all 0.3s ease;
    }

    .step.active .step-circle {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .step-title {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .step.active .step-title {
        color: #333;
        font-weight: 600;
    }

    /* Fade animation for tab panes */
    .tab-pane {
        display: none;
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
    }

    .tab-pane.active {
        display: block;
        opacity: 1;
    }

    .btn-next, .btn-prev {
        min-width: 120px;
    }

    /* Optional: Gradient header */
    .form-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        text-align: center;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="glass-card">
            <div class="form-header">
                <h3 class="mb-2"><i class="fas fa-user-plus me-2"></i>Create New Patient</h3>
                <p class="mb-0 opacity-75">Add a new patient to the system with complete information</p>
            </div>

            <!-- Stepper -->
            <div class="stepper px-4 py-3">
                <div class="step active" data-step="0">
                    <div class="step-circle">1</div>
                    <div class="step-title">Basic Info</div>
                </div>
                <div class="step" data-step="1">
                    <div class="step-circle">2</div>
                    <div class="step-title">Personal Details</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-circle">3</div>
                    <div class="step-title">Contact Info</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-circle">4</div>
                    <div class="step-title">Medical Info</div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin-portal.patients.store') }}" class="p-4">
                {!! csrf_field() !!}

                <div class="tab-content">
                    <!-- Basic Info -->
                    <div class="tab-pane active" data-step="0">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" id="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                        </div>
                    </div>

                    <!-- Personal Details -->
                    <div class="tab-pane" data-step="1">
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="phone" id="phone" value="{{ old('phone') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="birth_date" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select" name="gender" id="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender')=='male'?'selected':'' }}>Male</option>
                                <option value="female" {{ old('gender')=='female'?'selected':'' }}>Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="civil_status" class="form-label">Civil Status <span class="text-danger">*</span></label>
                            <select class="form-select" name="civil_status" id="civil_status" required>
                                <option value="">Select</option>
                                <option value="single" {{ old('civil_status')=='single'?'selected':'' }}>Single</option>
                                <option value="married" {{ old('civil_status')=='married'?'selected':'' }}>Married</option>
                                <option value="widowed" {{ old('civil_status')=='widowed'?'selected':'' }}>Widowed</option>
                                <option value="separated" {{ old('civil_status')=='separated'?'selected':'' }}>Separated</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Full Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="address" id="address" rows="3" required>{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="tab-pane" data-step="2">
                        <div class="mb-3">
                            <label for="emergency_contact_name" class="form-label">Emergency Contact Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="emergency_contact_relationship" class="form-label">Relationship <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="emergency_contact_relationship" id="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="emergency_contact_phone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" required>
                        </div>
                    </div>

                    <!-- Medical Info -->
                    <div class="tab-pane" data-step="3">
                        <div class="mb-3">
                            <label for="blood_type" class="form-label">Blood Type</label>
                            <select class="form-select" name="blood_type" id="blood_type">
                                <option value="">Select Blood Type</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="occupation" class="form-label">Occupation</label>
                            <input type="text" class="form-control" name="occupation" id="occupation" value="{{ old('occupation') }}">
                        </div>
                        <div class="mb-3">
                            <label for="religion" class="form-label">Religion</label>
                            <input type="text" class="form-control" name="religion" id="religion" value="{{ old('religion') }}">
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary btn-prev" disabled>Previous</button>
                        <button type="button" class="btn btn-primary btn-next">Next</button>
                        <button type="submit" class="btn btn-success btn-submit d-none">Create Patient</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const steps = document.querySelectorAll('.step');
    const panes = document.querySelectorAll('.tab-pane');
    const nextBtn = document.querySelector('.btn-next');
    const prevBtn = document.querySelector('.btn-prev');
    const submitBtn = document.querySelector('.btn-submit');
    let currentStep = 0;

    function showStep(step) {
        panes.forEach((pane, i) => {
            pane.classList.toggle('active', i === step);
        });
        steps.forEach((stepEl, i) => {
            stepEl.classList.toggle('active', i === step);
        });

        prevBtn.disabled = step === 0;
        nextBtn.classList.toggle('d-none', step === panes.length - 1);
        submitBtn.classList.toggle('d-none', step !== panes.length - 1);
    }

    nextBtn.addEventListener('click', () => {
        // Optional: Add simple validation
        const requiredFields = panes[currentStep].querySelectorAll('[required]');
        for (let field of requiredFields) {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                field.focus();
                return;
            } else {
                field.classList.remove('is-invalid');
            }
        }
        currentStep++;
        showStep(currentStep);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    prevBtn.addEventListener('click', () => {
        currentStep--;
        showStep(currentStep);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    showStep(currentStep);
});
</script>
@endsection
