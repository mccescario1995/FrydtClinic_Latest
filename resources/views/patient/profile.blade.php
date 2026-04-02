@extends('patient.layouts.app')

@section('title', 'My Profile')

@section('css')
<style>
    .profile-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6c757d;
        font-weight: 500;
    }
    .profile-tabs .nav-link.active {
        background-color: transparent;
        border-bottom-color: #16a34a !important;
        color: #16a34a !important;
    }
    .profile-tabs .nav-link:hover {
        border-bottom-color: #16a34a !important;
        color: #16a34a !important;
    }
    .form-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .section-header {
        border-bottom: 2px solid #16a34a !important;
        padding-bottom: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        color: #16a34a !important;
        font-weight: 600;
    }
    .section-header i {
        margin-right: 10px;
    }
    .readonly-field {
        background-color: #e9ecef !important;
        cursor: not-allowed;
    }
    .emergency-contact-toggle {
        margin-top: 15px;
    }
    .emergency-contact-fields {
        display: none;
        margin-top: 15px;
        padding: 15px;
        background: white;
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }
    .sticky-actions {
        position: sticky;
        bottom: 0;
        background: white;
        padding: 15px 0;
        border-top: 1px solid #dee2e6;
        margin-top: 30px;
    }
</style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>My Profile</h4>
                    <small class="text-white-50">Manage your personal and medical information</small>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Profile Image Section -->
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <img src="{{ $patientProfile->image_path ? asset('storage/app/public/' . $patientProfile->image_path) : 'https://via.placeholder.com/150x150?text=No+Image' }}"
                                  alt="Profile Image" class="rounded-circle border" style="width: 120px; height: 120px; object-fit: cover;">
                        </div>
                        <div class="mb-3">
                            <label for="profile_image" class="form-label fw-bold">Profile Image</label>
                            <input type="file" name="profile_image" id="profile_image"
                                class="form-control @error('profile_image') is-invalid @enderror"
                                accept="image/*" form="profileForm">
                            <div class="form-text">Upload a profile image (JPG, PNG, GIF). Max size: 2MB.</div>
                            @error('profile_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Tabbed Interface -->
                    <ul class="nav nav-tabs profile-tabs mb-4 text-success" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active " id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">
                                <i class="fas fa-user me-2"></i>Personal Info
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="medical-tab" data-bs-toggle="tab" data-bs-target="#medical" type="button" role="tab">
                                <i class="fas fa-heartbeat me-2"></i>Medical Info
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="philhealth-tab" data-bs-toggle="tab" data-bs-target="#philhealth" type="button" role="tab">
                                <i class="fas fa-shield-alt me-2"></i>PhilHealth
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                                <i class="fas fa-lock me-2"></i>Security
                            </button>
                        </li>
                    </ul>

                    <form id="profileForm" method="POST" action="{{ route('patient.update-profile') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="tab-content" id="profileTabContent">
                            <!-- Personal Information Tab -->
                            <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                <div class="form-section">
                                    <div class="section-header mb-3">
                                        <i class="fas fa-user"></i>
                                        Personal Information
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name', $user->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                            <input type="email" name="email" id="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email', $user->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="text" name="phone" id="phone"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                value="{{ old('phone', $patientProfile->phone ?? '') }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="birth_date" class="form-label">Date of Birth</label>
                                            <input type="date" name="birth_date" id="birth_date"
                                                class="form-control @error('birth_date') is-invalid @enderror"
                                                value="{{ old('birth_date', $patientProfile->birth_date ? \Carbon\Carbon::parse($patientProfile->birth_date)->format('Y-m-d') : '') }}">
                                            @error('birth_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="gender" class="form-label">Gender</label>
                                            <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                                                <option value="">Select Gender</option>
                                                <option value="male" {{ old('gender', $patientProfile->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ old('gender', $patientProfile->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                            @error('gender')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="civil_status" class="form-label">Civil Status</label>
                                            <select name="civil_status" id="civil_status" class="form-select @error('civil_status') is-invalid @enderror">
                                                <option value="">Select Civil Status</option>
                                                <option value="single" {{ old('civil_status', $patientProfile->civil_status ?? '') == 'single' ? 'selected' : '' }}>Single</option>
                                                <option value="married" {{ old('civil_status', $patientProfile->civil_status ?? '') == 'married' ? 'selected' : '' }}>Married</option>
                                                <option value="widowed" {{ old('civil_status', $patientProfile->civil_status ?? '') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                                <option value="separated" {{ old('civil_status', $patientProfile->civil_status ?? '') == 'separated' ? 'selected' : '' }}>Separated</option>
                                            </select>
                                            @error('civil_status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label for="address" class="form-label">Full Address</label>
                                            <textarea name="address" id="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $patientProfile->address ?? '') }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Emergency Contact Fields (Hidden by default) -->
                                    <div class="emergency-contact-fields mb-3" id="emergencyFields">
                                        <h6 class="mb-3 text-success"><i class="fas fa-phone me-2"></i>Emergency Contact Information</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="emergency_contact_name" class="form-label">Contact Name</label>
                                                <input type="text" name="emergency_contact_name" id="emergency_contact_name"
                                                    class="form-control @error('emergency_contact_name') is-invalid @enderror"
                                                    value="{{ old('emergency_contact_name', $patientProfile->emergency_contact_name ?? '') }}">
                                                @error('emergency_contact_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="emergency_contact_relationship" class="form-label">Relationship</label>
                                                <input type="text" name="emergency_contact_relationship" id="emergency_contact_relationship"
                                                    class="form-control @error('emergency_contact_relationship') is-invalid @enderror"
                                                    value="{{ old('emergency_contact_relationship', $patientProfile->emergency_contact_relationship ?? '') }}"
                                                    placeholder="e.g., Spouse, Parent, Sibling">
                                                @error('emergency_contact_relationship')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="emergency_contact_phone" class="form-label">Contact Phone</label>
                                                <input type="text" name="emergency_contact_phone" id="emergency_contact_phone"
                                                    class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                                                    value="{{ old('emergency_contact_phone', $patientProfile->emergency_contact_phone ?? '') }}">
                                                @error('emergency_contact_phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Medical Information Tab -->
                            <div class="tab-pane fade mb-3" id="medical" role="tabpanel">
                                <div class="form-section">
                                    <div class="section-header mb-3">
                                        <i class="fas fa-heartbeat"></i>
                                        Medical Information
                                    </div>

                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Note:</strong> Medical information can only be updated by clinic administrators or staff.
                                        Please contact the clinic if you need to update this information.
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Blood Type</label>
                                            <input type="text" class="form-control readonly-field" readonly
                                                   value="{{ $patientProfile->blood_type ?? 'Not specified' }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Occupation</label>
                                            <input type="text" class="form-control readonly-field" readonly
                                                   value="{{ $patientProfile->occupation ?? 'Not specified' }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Religion</label>
                                            <input type="text" class="form-control readonly-field" readonly
                                                   value="{{ $patientProfile->religion ?? 'Not specified' }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Barangay Captain</label>
                                            <input type="text" class="form-control readonly-field" readonly
                                                   value="{{ $patientProfile->barangay_captain ?? 'Not specified' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PhilHealth Information Tab -->
                            <div class="tab-pane fade mb-3" id="philhealth" role="tabpanel">
                                <div class="form-section ">
                                    <div class="section-header mb-3">
                                        <i class="fas fa-shield-alt"></i>
                                        PhilHealth Information
                                    </div>

                                    <div class="alert alert-warning">
                                        <i class="fas fa-lock me-2"></i>
                                        <strong>Read-Only:</strong> PhilHealth information can only be updated by clinic administrators or staff.
                                        Please contact the clinic if you need to update your PhilHealth details.
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">PhilHealth Membership</label>
                                            <input type="text" class="form-control readonly-field" readonly
                                                   value="@if($patientProfile->philhealth_membership == 'member') Member @elseif($patientProfile->philhealth_membership == 'dependent') Dependent @else Non-Member @endif">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">PhilHealth Number</label>
                                            <input type="text" class="form-control readonly-field" readonly
                                                   value="{{ $patientProfile->philhealth_number ?? 'Not specified' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Security Settings Tab -->
                            <div class="tab-pane fade  mb-3" id="security" role="tabpanel">
                                <div class="form-section ">
                                    <div class="section-header mb-3">
                                        <i class="fas fa-lock"></i>
                                        Security Settings
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="user_pin" class="form-label">PIN (6 digits)</label>
                                            <input type="password" name="user_pin" id="user_pin"
                                                class="form-control @error('user_pin') is-invalid @enderror"
                                                maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                                                placeholder="Enter 6-digit PIN">
                                            <div class="form-text">Leave empty to keep current PIN</div>
                                            @error('user_pin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="user_pin_confirmation" class="form-label">Confirm PIN</label>
                                            <input type="password" name="user_pin_confirmation" id="user_pin_confirmation"
                                                class="form-control @error('user_pin_confirmation') is-invalid @enderror"
                                                maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                                                placeholder="Confirm PIN">
                                            @error('user_pin_confirmation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sticky Action Buttons -->
                        <div class="sticky-actions">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('patient.dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Update Profile
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
    // Emergency contact toggle
    const toggleBtn = document.getElementById('toggleEmergency');
    const emergencyFields = document.getElementById('emergencyFields');

    if (toggleBtn && emergencyFields) {
        toggleBtn.addEventListener('click', function() {
            const isVisible = emergencyFields.style.display !== 'none';
            emergencyFields.style.display = isVisible ? 'none' : 'block';
            toggleBtn.innerHTML = isVisible ?
                '<i class="fas fa-plus me-1"></i>Add Emergency Contact' :
                '<i class="fas fa-minus me-1"></i>Hide Emergency Contact';
        });

        // Show emergency fields if any have values
        const emergencyInputs = emergencyFields.querySelectorAll('input');
        const hasValues = Array.from(emergencyInputs).some(input => input.value.trim() !== '');
        if (hasValues) {
            emergencyFields.style.display = 'block';
            toggleBtn.innerHTML = '<i class="fas fa-minus me-1"></i>Hide Emergency Contact';
        }
    }

    // Auto-submit form when profile image is selected
    const profileImageInput = document.getElementById('profile_image');
    if (profileImageInput) {
        profileImageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                // Optional: Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.querySelector('.rounded-circle');
                    if (img) img.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
});
</script>
@endsection
