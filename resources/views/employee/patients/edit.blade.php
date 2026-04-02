@extends('employee.layouts.app')

@section('title', 'Edit Patient - ' . $patient->name)

@section('content')
<div style="max-width: 1000px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="color: #333; margin: 0; font-size: 28px;">Edit Patient: {{ $patient->name }}</h1>
            <p style="color: #666; margin: 5px 0 0 0;">Update patient information</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('employee.patients.show', $patient->id) }}" style="background: #17a2b8; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">View Patient</a>
            <a href="{{ route('employee.patients') }}" style="background: #6c757d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">← Back to Patients</a>
        </div>
    </div>

    <!-- Form -->
    <div style="background: white; padding: 30px; border-radius: 8px; border: 1px solid #ddd;">
        <form method="POST" action="{{ route('employee.patients.update', $patient->id) }}">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #007bff; padding-bottom: 10px;">Basic Information</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="name" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Full Name <span style="color: #dc3545;">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $patient->name) }}" required
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('name') ? 'border-color: #dc3545;' : '' }}">
                        @if($errors->has('name'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('name') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="email" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Email Address <span style="color: #dc3545;">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $patient->email) }}" required
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('email') ? 'border-color: #dc3545;' : '' }}">
                        @if($errors->has('email'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('email') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #28a745; padding-bottom: 10px;">Personal Information</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="phone" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $patient->patientProfile->phone ?? '') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('phone') ? 'border-color: #dc3545;' : '' }}">
                        @if($errors->has('phone'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('phone') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="birth_date" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Birth Date</label>
                        <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $patient->patientProfile->birth_date ?? '') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('birth_date') ? 'border-color: #dc3545;' : '' }}">
                        @if($errors->has('birth_date'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('birth_date') }}</div>
                        @endif
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="gender" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Gender</label>
                        <select id="gender" name="gender"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('gender') ? 'border-color: #dc3545;' : '' }}">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $patient->patientProfile->gender ?? '') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $patient->patientProfile->gender ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @if($errors->has('gender'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('gender') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="civil_status" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Civil Status</label>
                        <select id="civil_status" name="civil_status"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('civil_status') ? 'border-color: #dc3545;' : '' }}">
                            <option value="">Select Civil Status</option>
                            <option value="single" {{ old('civil_status', $patient->patientProfile->civil_status ?? '') === 'single' ? 'selected' : '' }}>Single</option>
                            <option value="married" {{ old('civil_status', $patient->patientProfile->civil_status ?? '') === 'married' ? 'selected' : '' }}>Married</option>
                            <option value="widowed" {{ old('civil_status', $patient->patientProfile->civil_status ?? '') === 'widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="separated" {{ old('civil_status', $patient->patientProfile->civil_status ?? '') === 'separated' ? 'selected' : '' }}>Separated</option>
                        </select>
                        @if($errors->has('civil_status'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('civil_status') }}</div>
                        @endif
                    </div>
                </div>

                <div>
                    <label for="address" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Address</label>
                    <textarea id="address" name="address" rows="3"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical; {{ $errors->has('address') ? 'border-color: #dc3545;' : '' }}">{{ old('address', $patient->patientProfile->address ?? '') }}</textarea>
                    @if($errors->has('address'))
                        <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('address') }}</div>
                    @endif
                </div>
            </div>

            <!-- Emergency Contact -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #ffc107; padding-bottom: 10px;">Emergency Contact Information</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="emergency_contact_name" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Emergency Contact Name</label>
                        <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $patient->patientProfile->emergency_contact_name ?? '') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('emergency_contact_name') ? 'border-color: #dc3545;' : '' }}">
                        @if($errors->has('emergency_contact_name'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('emergency_contact_name') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="emergency_contact_relationship" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Relationship</label>
                        <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $patient->patientProfile->emergency_contact_relationship ?? '') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('emergency_contact_relationship') ? 'border-color: #dc3545;' : '' }}">
                        @if($errors->has('emergency_contact_relationship'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('emergency_contact_relationship') }}</div>
                        @endif
                    </div>
                </div>

                <div>
                    <label for="emergency_contact_phone" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Emergency Contact Phone</label>
                    <input type="text" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $patient->patientProfile->emergency_contact_phone ?? '') }}"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('emergency_contact_phone') ? 'border-color: #dc3545;' : '' }}">
                    @if($errors->has('emergency_contact_phone'))
                        <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('emergency_contact_phone') }}</div>
                    @endif
                </div>
            </div>

            <!-- Form Actions -->
            <div style="border-top: 1px solid #eee; padding-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                <a href="{{ route('employee.patients.show', $patient->id) }}" style="background: #6c757d; color: white; text-decoration: none; padding: 12px 24px; border-radius: 5px; font-size: 14px;">Cancel</a>
                <button type="submit" style="background: #007bff; color: white; border: none; padding: 12px 24px; border-radius: 5px; font-size: 14px; cursor: pointer;">Update Patient</button>
            </div>
        </form>
    </div>
</div>
@endsection
