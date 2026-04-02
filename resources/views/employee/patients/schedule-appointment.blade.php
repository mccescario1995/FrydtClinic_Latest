@extends('employee.layouts.app')

@section('title', 'Schedule Appointment - ' . $patient->name)

@section('content')
<div style="max-width: 800px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="color: #333; margin: 0; font-size: 28px;">Schedule Appointment</h1>
            <p style="color: #666; margin: 5px 0 0 0;">For patient: {{ $patient->name }}</p>
        </div>
        <a href="{{ route('employee.patients.show', $patient->id) }}" style="background: #6c757d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">Back to Patient</a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Appointment Form -->
    <div style="background: white; padding: 30px; border-radius: 8px; border: 1px solid #ddd;">
        <h2 style="color: #333; margin: 0 0 25px 0; font-size: 22px;">Appointment Details</h2>

        <form method="POST" action="{{ route('employee.patients.store-scheduled-appointment', $patient->id) }}">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <!-- Service Selection -->
                <div>
                    <label for="service_id" style="display: block; margin-bottom: 5px; color: #333; font-weight: bold;">Service *</label>
                    <select name="service_id" id="service_id" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; @error('service_id') border-color: #dc3545; @enderror">
                        <option value="">Select a service</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->name }}
                                @if($service->duration_minutes)
                                    ({{ $service->duration_minutes }} mins)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('service_id')
                        <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Employee Selection -->
                <div>
                    <label for="employee_id" style="display: block; margin-bottom: 5px; color: #333; font-weight: bold;">Healthcare Provider *</label>
                    <select name="employee_id" id="employee_id" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; @error('employee_id') border-color: #dc3545; @enderror">
                        <option value="">Select a provider</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <!-- Appointment Date -->
                <div>
                    <label for="appointment_date" style="display: block; margin-bottom: 5px; color: #333; font-weight: bold;">Appointment Date *</label>
                    <input type="date" name="appointment_date" id="appointment_date" required
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           value="{{ old('appointment_date') }}"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; @error('appointment_date') border-color: #dc3545; @enderror">
                    @error('appointment_date')
                        <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Appointment Time -->
                <div>
                    <label for="appointment_time" style="display: block; margin-bottom: 5px; color: #333; font-weight: bold;">Appointment Time *</label>
                    <input type="time" name="appointment_time" id="appointment_time" required
                           value="{{ old('appointment_time') }}"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; @error('appointment_time') border-color: #dc3545; @enderror">
                    @error('appointment_time')
                        <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Duration -->
                <div>
                    <label for="duration_in_minutes" style="display: block; margin-bottom: 5px; color: #333; font-weight: bold;">Duration (minutes) *</label>
                    <input type="number" name="duration_in_minutes" id="duration_in_minutes" required
                           min="15" max="480" step="15"
                           value="{{ old('duration_in_minutes', 30) }}"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; @error('duration_in_minutes') border-color: #dc3545; @enderror">
                    @error('duration_in_minutes')
                        <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div style="margin-bottom: 30px;">
                <label for="notes" style="display: block; margin-bottom: 5px; color: #333; font-weight: bold;">Notes (Optional)</label>
                <textarea name="notes" id="notes" rows="4"
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical; @error('notes') border-color: #dc3545; @enderror"
                          placeholder="Any additional notes or special instructions">{{ old('notes') }}</textarea>
                @error('notes')
                    <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Form Actions -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 20px; border-top: 1px solid #eee;">
                <a href="{{ route('employee.patients.show', $patient->id) }}" style="color: #6c757d; text-decoration: none; font-size: 14px;">
                    <i class="fas fa-arrow-left" style="margin-right: 5px;"></i>Cancel
                </a>
                <button type="submit" style="background: #007bff; color: white; border: none; padding: 12px 30px; border-radius: 5px; font-size: 14px; cursor: pointer;">
                    <i class="fas fa-calendar-check" style="margin-right: 5px;"></i>Schedule Appointment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
