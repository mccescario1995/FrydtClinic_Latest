@extends('admin-portal.layouts.app')

@section('title', 'Edit Patient - ' . $patient->name)

@section('content')
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-user-edit me-2"></i>Edit Patient
        </h1>
        <p class="page-subtitle">{{ $patient->name }} • Patient ID: {{ $patient->id }}</p>
    </div>

    <div class="admin-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Patient Information</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin-portal.patients.update', $patient->id) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <h6 class="section-title">Basic Information</h6>

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $patient->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span
                                    class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $patient->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="col-md-6">
                        <h6 class="section-title">Personal Information</h6>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                name="phone" value="{{ old('phone', $patient->patientProfile->phone ?? '') }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="birth_date" class="form-label">Date of Birth <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                id="birth_date" name="birth_date"
                                value="{{ old('birth_date', $patient->patientProfile->birth_date ? \Carbon\Carbon::parse($patient->patientProfile->birth_date)->format('Y-m-d') : '') }}"
                                required>
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender"
                                required>
                                <option value="">Select Gender</option>
                                <option value="male"
                                    {{ old('gender', $patient->patientProfile->gender ?? '') === 'male' ? 'selected' : '' }}>
                                    Male</option>
                                <option value="female"
                                    {{ old('gender', $patient->patientProfile->gender ?? '') === 'female' ? 'selected' : '' }}>
                                    Female</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="civil_status" class="form-label">Civil Status <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('civil_status') is-invalid @enderror" id="civil_status"
                                name="civil_status" required>
                                <option value="">Select Civil Status</option>
                                <option value="single"
                                    {{ old('civil_status', $patient->patientProfile->civil_status ?? '') === 'single' ? 'selected' : '' }}>
                                    Single</option>
                                <option value="married"
                                    {{ old('civil_status', $patient->patientProfile->civil_status ?? '') === 'married' ? 'selected' : '' }}>
                                    Married</option>
                                <option value="widowed"
                                    {{ old('civil_status', $patient->patientProfile->civil_status ?? '') === 'widowed' ? 'selected' : '' }}>
                                    Widowed</option>
                                <option value="separated"
                                    {{ old('civil_status', $patient->patientProfile->civil_status ?? '') === 'separated' ? 'selected' : '' }}>
                                    Separated</option>
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
                            <label for="address" class="form-label">Full Address <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3"
                                required>{{ old('address', $patient->patientProfile->address ?? '') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="section-title">Emergency Contact</h6>

                        <div class="mb-3">
                            <label for="emergency_contact_name" class="form-label">Emergency Contact Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror"
                                id="emergency_contact_name" name="emergency_contact_name"
                                value="{{ old('emergency_contact_name', $patient->patientProfile->emergency_contact_name ?? '') }}"
                                required>
                            @error('emergency_contact_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone <span
                                    class="text-danger">*</span></label>
                            <input type="tel"
                                class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                                id="emergency_contact_phone" name="emergency_contact_phone"
                                value="{{ old('emergency_contact_phone', $patient->patientProfile->emergency_contact_phone ?? '') }}"
                                required>
                            @error('emergency_contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="emergency_contact_relationship" class="form-label">Relationship <span
                                    class="text-danger">*</span></label>
                            <input type="text"
                                class="form-control @error('emergency_contact_relationship') is-invalid @enderror"
                                id="emergency_contact_relationship" name="emergency_contact_relationship"
                                value="{{ old('emergency_contact_relationship', $patient->patientProfile->emergency_contact_relationship ?? '') }}"
                                placeholder="e.g., Spouse, Parent, Sibling" required>
                            @error('emergency_contact_relationship')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin-portal.patients.show', $patient->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Patient
                    </a>
                    <button type="submit" class="btn btn-admin-primary">
                        <i class="fas fa-save me-1"></i>Update Patient
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
