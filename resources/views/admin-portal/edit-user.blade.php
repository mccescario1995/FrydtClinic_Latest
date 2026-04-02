@extends('admin-portal.layouts.app')

@section('title', 'Edit User - ' . $user->name)

@section('content')
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-user-edit me-2"></i>Edit User
        </h1>
        <p class="page-subtitle">{{ $user->name }} • User ID: {{ $user->id }}</p>
    </div>

    <div class="admin-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>User Information</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin-portal.users.update', $user->id) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <h6 class="section-title">Basic Information</h6>

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span
                                    class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if (backpack_user()->user_type === 'admin')
                            <div class="mb-3">
                                <label for="user_type" class="form-label">User Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('user_type') is-invalid @enderror" id="user_type"
                                    name="user_type" required>
                                    <option value="">Select User Type</option>
                                    <option value="admin"
                                        {{ old('user_type', $user->user_type) === 'admin' ? 'selected' : '' }}>Administrator
                                    </option>
                                    <option value="employee"
                                        {{ old('user_type', $user->user_type) === 'employee' ? 'selected' : '' }}>Employee
                                    </option>
                                    <option value="patient"
                                        {{ old('user_type', $user->user_type) === 'patient' ? 'selected' : '' }}>Patient
                                    </option>
                                </select>
                                @error('user_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" id="position_field" style="display: none;">
                                <label for="position" class="form-label">Position</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror"
                                    id="position" name="position" value="{{ old('position', $user->employeeProfile ? $user->employeeProfile->position : '') }}"
                                    placeholder="e.g., Nurse, Doctor, Receptionist">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" id="hourly_rate_field" style="display: none;">
                                <label for="hourly_rate" class="form-label">Hourly Rate (₱)</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('hourly_rate') is-invalid @enderror"
                                    id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate', $user->employeeProfile ? $user->employeeProfile->hourly_rate : '') }}"
                                    placeholder="e.g., 150.00">
                                @error('hourly_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Hourly wage for payroll calculations</small>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Account Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                    name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="active"
                                        {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive"
                                        {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <div class="mb-3">
                                <label class="form-label">User Type</label>
                                <p class="mb-0">
                                    <span
                                        class="badge bg-{{ match ($user->user_type) {
                                            'admin' => 'warning',
                                            'employee' => 'info',
                                            'patient' => 'success',
                                            default => 'secondary',
                                        } }}">
                                        <i
                                            class="fas fa-{{ match ($user->user_type) {
                                                'admin' => 'user-shield',
                                                'employee' => 'user-md',
                                                'patient' => 'user-injured',
                                                default => 'user',
                                            } }} me-1"></i>
                                        {{ ucfirst($user->user_type) }}
                                    </span>
                                </p>
                                <small class="text-muted">Only administrators can change user types</small>
                            </div>

                            @if ($user->user_type == 'employee')
                                <div class="mb-3">
                                    <label for="position" class="form-label">Position</label>
                                    <input type="text" class="form-control @error('position') is-invalid @enderror" id="position"
                                        name="position" value="{{ old('position', $user->employeeProfile ? $user->employeeProfile->position : '') }}"
                                        placeholder="e.g., Nurse, Doctor, Receptionist">
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Account Status</label>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                        <i
                                            class="fas fa-{{ $user->status === 'active' ? 'check-circle' : 'pause-circle' }} me-1"></i>
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </p>
                                <small class="text-muted">Only administrators can change account status</small>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="pin" class="form-label">PIN</label>
                            <input type="number" class="form-control @error('pin') is-invalid @enderror" id="pin"
                                name="pin" value="{{ old('pin', $user->pin) }}" placeholder="6-digit PIN">
                            @error('pin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Optional 6-digit PIN for attendance/login verification</small>
                        </div>

                        {{-- @if ($user->user_type == 'employee')
                            <div class="mb-3">
                                <label for="specialty" class="form-label">Specialty</label>
                                <input type="text" class="form-control @error('specialty') is-invalid @enderror" id="specialty"
                                    name="specialty" value="{{ old('specialty', $user->employeeProfile ? $user->employeeProfile->specialty : '') }}"
                                    placeholder="e.g., OB GYN Doctor, Cardiologist">
                                @error('specialty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Optional specialty or sub-role for this employee</small>
                            </div>
                        @endif --}}
                    </div>

                    <!-- Personal Information -->
                    <div class="col-md-6">
                        <h6 class="section-title">Personal Information</h6>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                id="phone" name="phone" value="{{ old('phone', $user->patientProfile ? $user->patientProfile->phone : ($user->employeeProfile ? $user->employeeProfile->phone : '')) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="birth_date" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                id="birth_date" name="birth_date" value="{{ old('birth_date', $user->patientProfile ? $user->patientProfile->birth_date : ($user->employeeProfile ? $user->employeeProfile->birth_date : '')) }}">
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $user->patientProfile ? $user->patientProfile->gender : ($user->employeeProfile ? $user->employeeProfile->gender : '')) === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $user->patientProfile ? $user->patientProfile->gender : ($user->employeeProfile ? $user->employeeProfile->gender : '')) === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="civil_status" class="form-label">Civil Status</label>
                            <select class="form-select @error('civil_status') is-invalid @enderror" id="civil_status" name="civil_status">
                                <option value="">Select Civil Status</option>
                                <option value="single" {{ old('civil_status', $user->patientProfile ? $user->patientProfile->civil_status : ($user->employeeProfile ? $user->employeeProfile->civil_status : '')) === 'single' ? 'selected' : '' }}>Single</option>
                                <option value="married" {{ old('civil_status', $user->patientProfile ? $user->patientProfile->civil_status : ($user->employeeProfile ? $user->employeeProfile->civil_status : '')) === 'married' ? 'selected' : '' }}>Married</option>
                                <option value="widowed" {{ old('civil_status', $user->patientProfile ? $user->patientProfile->civil_status : ($user->employeeProfile ? $user->employeeProfile->civil_status : '')) === 'widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="separated" {{ old('civil_status', $user->patientProfile ? $user->patientProfile->civil_status : ($user->employeeProfile ? $user->employeeProfile->civil_status : '')) === 'separated' ? 'selected' : '' }}>Separated</option>
                            </select>
                            @error('civil_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address and Emergency Contact -->
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="section-title">Address</h6>
                        <div class="mb-3">
                            <label for="address" class="form-label">Full Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address" name="address" rows="3">{{ old('address', $user->patientProfile ? $user->patientProfile->address : ($user->employeeProfile ? $user->employeeProfile->address : '')) }}</textarea>
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
                                id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $user->patientProfile ? $user->patientProfile->emergency_contact_name : ($user->employeeProfile ? $user->employeeProfile->emergency_contact_name : '')) }}">
                            @error('emergency_contact_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
                            <input type="tel" class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                                id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $user->patientProfile ? $user->patientProfile->emergency_contact_phone : ($user->employeeProfile ? $user->employeeProfile->emergency_contact_phone : '')) }}">
                            @error('emergency_contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="emergency_contact_relationship" class="form-label">Relationship</label>
                            <input type="text" class="form-control @error('emergency_contact_relationship') is-invalid @enderror"
                                id="emergency_contact_relationship" name="emergency_contact_relationship"
                                value="{{ old('emergency_contact_relationship', $user->patientProfile ? $user->patientProfile->emergency_contact_relationship : ($user->employeeProfile ? $user->employeeProfile->emergency_contact_relationship : '')) }}" placeholder="e.g., Spouse, Parent, Sibling">
                            @error('emergency_contact_relationship')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Registration Info -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Registration Date</label>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($user->created_at)->format('F d, Y \a\t h:i A') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Last Updated</label>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($user->updated_at)->format('F d, Y \a\t h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin-portal.users.show', $user->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to User
                    </a>
                    <button type="submit" class="btn btn-admin-primary">
                        <i class="fas fa-save me-1"></i>Update User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const userTypeSelect = document.getElementById('user_type');
        const positionField = document.getElementById('position_field');
        const hourlyRateField = document.getElementById('hourly_rate_field');

        function toggleEmployeeFields() {
            if (userTypeSelect && userTypeSelect.value === 'employee') {
                positionField.style.display = 'block';
                hourlyRateField.style.display = 'block';
            } else {
                positionField.style.display = 'none';
                hourlyRateField.style.display = 'none';
                // Clear the fields when not employee
                const positionInput = document.getElementById('position');
                const hourlyRateInput = document.getElementById('hourly_rate');
                if (positionInput) positionInput.value = '';
                if (hourlyRateInput) hourlyRateInput.value = '';
            }
        }

        // Initial check
        toggleEmployeeFields();

        // Listen for changes
        if (userTypeSelect) {
            userTypeSelect.addEventListener('change', toggleEmployeeFields);
        }
    });
    </script>
@endsection
