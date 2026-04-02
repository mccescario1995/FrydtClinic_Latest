@extends('admin-portal.layouts.app')

@section('title', 'Create New User')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-user-plus me-2"></i>Create New User
    </h1>
    <p class="page-subtitle">Add a new user to the system</p>
</div>

<div class="admin-card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>User Information</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin-portal.users.store') }}">
            @csrf

            <div class="row">
                <!-- Basic Information -->
                <div class="col-md-6">
                    <h6 class="section-title">Basic Information</h6>

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="user_type" class="form-label">User Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('user_type') is-invalid @enderror" id="user_type" name="user_type" required>
                            <option value="">Select User Type</option>
                            <option value="admin" {{ old('user_type') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="employee" {{ old('user_type') === 'employee' ? 'selected' : '' }}>Employee</option>
                            <option value="patient" {{ old('user_type') === 'patient' ? 'selected' : '' }}>Patient</option>
                        </select>
                        @error('user_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="position_field" style="display: none;">
                        <label for="position" class="form-label">Position</label>
                        <input type="text" class="form-control @error('position') is-invalid @enderror"
                               id="position" name="position" value="{{ old('position') }}"
                               placeholder="e.g., Nurse, Doctor, Receptionist">
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- <div class="mb-3" id="specialty_field" style="display: none;">
                        <label for="specialty" class="form-label">Specialty</label>
                        <input type="text" class="form-control @error('specialty') is-invalid @enderror"
                               id="specialty" name="specialty" value="{{ old('specialty') }}"
                               placeholder="e.g., OB-GYN, Pediatrics, Internal Medicine">
                        @error('specialty')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}

                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="col-md-6" id="personal_info_section">
                    <h6 class="section-title">Personal Information</h6>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                               id="phone" name="phone" value="{{ old('phone') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="birth_date" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                               id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                        @error('birth_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="civil_status" class="form-label">Civil Status</label>
                        <select class="form-select @error('civil_status') is-invalid @enderror" id="civil_status" name="civil_status">
                            <option value="">Select Civil Status</option>
                            <option value="single" {{ old('civil_status') === 'single' ? 'selected' : '' }}>Single</option>
                            <option value="married" {{ old('civil_status') === 'married' ? 'selected' : '' }}>Married</option>
                            <option value="widowed" {{ old('civil_status') === 'widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="separated" {{ old('civil_status') === 'separated' ? 'selected' : '' }}>Separated</option>
                        </select>
                        @error('civil_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Address and Emergency Contact -->
            <div class="row" id="additional_info_section" style="display: none;">
                <div class="col-md-6">
                    <h6 class="section-title">Address</h6>
                    <div class="mb-3">
                        <label for="address" class="form-label">Full Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror"
                                  id="address" name="address" rows="3">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="section-title">Emergency Contact</h6>

                    <div class="mb-3">
                        <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                        <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror"
                               id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}">
                        @error('emergency_contact_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
                        <input type="tel" class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                               id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}">
                        @error('emergency_contact_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="emergency_contact_relationship" class="form-label">Relationship</label>
                        <input type="text" class="form-control @error('emergency_contact_relationship') is-invalid @enderror"
                               id="emergency_contact_relationship" name="emergency_contact_relationship"
                               value="{{ old('emergency_contact_relationship') }}" placeholder="e.g., Spouse, Parent, Sibling">
                        @error('emergency_contact_relationship')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin-portal.users') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Users
                </a>
                <button type="submit" class="btn btn-admin-primary">
                    <i class="fas fa-save me-1"></i>Create User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userTypeSelect = document.getElementById('user_type');
    const positionField = document.getElementById('position_field');
    const specialtyField = document.getElementById('specialty_field');
    const personalInfoSection = document.getElementById('personal_info_section');
    const additionalInfoSection = document.getElementById('additional_info_section');

    function toggleFields() {
        const userType = userTypeSelect.value;

        // Hide all optional fields first
        positionField.style.display = 'none';
        specialtyField.style.display = 'none';
        personalInfoSection.style.display = 'none';
        additionalInfoSection.style.display = 'none';

        // Clear all optional fields
        document.getElementById('position').value = '';
        document.getElementById('specialty').value = '';
        document.getElementById('phone').value = '';
        document.getElementById('birth_date').value = '';
        document.getElementById('gender').value = '';
        document.getElementById('civil_status').value = '';
        document.getElementById('address').value = '';
        document.getElementById('emergency_contact_name').value = '';
        document.getElementById('emergency_contact_phone').value = '';
        document.getElementById('emergency_contact_relationship').value = '';

        // Show fields based on user type
        if (userType === 'employee') {
            positionField.style.display = 'block';
            specialtyField.style.display = 'block';
            personalInfoSection.style.display = 'block';
            additionalInfoSection.style.display = 'block';
        } else if (userType === 'patient') {
            personalInfoSection.style.display = 'block';
            additionalInfoSection.style.display = 'block';
        }
        // Admin type shows only basic fields (no additional sections)
    }

    // Initial check
    toggleFields();

    // Listen for changes
    userTypeSelect.addEventListener('change', toggleFields);
});
</script>
@endsection
