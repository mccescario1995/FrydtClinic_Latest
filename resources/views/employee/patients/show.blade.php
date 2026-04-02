@extends('employee.layouts.app')

@section('title', 'Patient Details - ' . $patient->name)

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="color: #333; margin: 0; font-size: 28px;">Patient Details: {{ $patient->name }}</h1>
            <p style="color: #666; margin: 5px 0 0 0;">Patient ID: {{ $patient->id }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('employee.patients.edit', $patient->id) }}" style="background: #28a745; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">Edit Patient</a>
            <a href="{{ route('employee.patients') }}" style="background: #6c757d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">Back to Patients</a>
        </div>
    </div>

    <!-- Patient Information -->
    <div style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 30px;">
        <h2 style="color: #333; margin: 0 0 20px 0; font-size: 24px;">Patient Information</h2>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666; font-size: 14px;">FULL NAME</strong><br>
                    <span style="font-size: 16px; color: #333;">{{ $patient->name }}</span>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666; font-size: 14px;">EMAIL</strong><br>
                    <span style="font-size: 16px; color: #333;">{{ $patient->email }}</span>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666; font-size: 14px;">PHONE</strong><br>
                    <span style="font-size: 16px; color: #333;">{{ $patient->patientProfile->phone ?? 'N/A' }}</span>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666; font-size: 14px;">ADDRESS</strong><br>
                    <span style="font-size: 16px; color: #333;">{{ $patient->patientProfile->address ?? 'N/A' }}</span>
                </div>
            </div>

            <div>
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666; font-size: 14px;">BIRTH DATE</strong><br>
                    <span style="font-size: 16px; color: #333;">
                        @if($patient->patientProfile->birth_date)
                            {{ \Carbon\Carbon::parse($patient->patientProfile->birth_date)->format('M d, Y') }}
                            ({{ \Carbon\Carbon::parse($patient->patientProfile->birth_date)->age }} years old)
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666; font-size: 14px;">GENDER</strong><br>
                    <span style="font-size: 16px; color: #333;">{{ $patient->patientProfile->gender ? ucfirst($patient->patientProfile->gender) : 'N/A' }}</span>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666; font-size: 14px;">CIVIL STATUS</strong><br>
                    <span style="font-size: 16px; color: #333;">{{ $patient->patientProfile->civil_status ? ucfirst($patient->patientProfile->civil_status) : 'N/A' }}</span>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong style="color: #666; font-size: 14px;">STATUS</strong><br>
                    <span style="background: {{ $patient->status === 'active' ? '#d4edda' : '#e2e3e5' }}; color: {{ $patient->status === 'active' ? '#155724' : '#383d41' }}; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold;">
                        {{ ucfirst($patient->status) }}
                    </span>
                </div>
            </div>
        </div>

        @if($patient->patientProfile->emergency_contact_name)
            <div style="border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px;">
                <h3 style="color: #333; margin: 0 0 15px 0; font-size: 18px;">Emergency Contact</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <strong style="color: #666; font-size: 14px;">NAME</strong><br>
                        <span style="font-size: 16px; color: #333;">{{ $patient->patientProfile->emergency_contact_name }}</span>
                    </div>
                    <div>
                        <strong style="color: #666; font-size: 14px;">RELATIONSHIP</strong><br>
                        <span style="font-size: 16px; color: #333;">{{ $patient->patientProfile->emergency_contact_relationship ?? 'N/A' }}</span>
                    </div>
                    <div style="grid-column: span 2;">
                        <strong style="color: #666; font-size: 14px;">PHONE</strong><br>
                        <span style="font-size: 16px; color: #333;">{{ $patient->patientProfile->emergency_contact_phone ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <h3 style="margin: 0 0 15px 0; color: #333;">Quick Actions</h3>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('employee.patients.appointments', $patient->id) }}" style="background: #007bff; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">View Appointments</a>
            <a href="{{ route('employee.patients.medical-records', $patient->id) }}" style="background: #17a2b8; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">Medical Records</a>
            {{-- <a href="{{ route('employee.patients.prenatal-records', $patient->id) }}" style="background: #28a745; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">Prenatal Records</a> --}}
            <a href="{{ route('employee.patients.lab-results', $patient->id) }}" style="background: #ffc107; color: #333; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">Lab Results</a>
            <a href="{{ route('employee.patients.payments', $patient->id) }}" style="background: #6f42c1; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">View Payments</a>
            {{-- <a href="{{ route('employee.patients.create-payment', $patient->id) }}" style="background: #e83e8c; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">Create Payment</a> --}}
            <a href="{{ route('employee.patients.schedule-appointment', $patient->id) }}" style="background: #dc3545; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">Schedule Appointment</a>
        </div>
    </div>

    <!-- Recent Appointments -->
    <div style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #333; font-size: 20px;">Recent Appointments</h3>
            <a href="{{ route('employee.patients.appointments', $patient->id) }}" style="color: #007bff; text-decoration: none; font-size: 14px;">View All →</a>
        </div>

        @if($appointments->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;">Date & Time</th>
                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;">Service</th>
                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;">Employee</th>
                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments->take(5) as $appointment)
                            <tr>
                                <td style="padding: 12px; border: 1px solid #ddd;">{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, Y g:i A') }}</td>
                                <td style="padding: 12px; border: 1px solid #ddd;">{{ $appointment->service->name ?? 'N/A' }}</td>
                                <td style="padding: 12px; border: 1px solid #ddd;">{{ $appointment->employee->name ?? 'N/A' }}</td>
                                <td style="padding: 12px; border: 1px solid #ddd;">
                                    <span style="background: {{ $appointment->status === 'completed' ? '#d4edda' : ($appointment->status === 'scheduled' ? '#cce5ff' : '#e2e3e5') }}; color: {{ $appointment->status === 'completed' ? '#155724' : ($appointment->status === 'scheduled' ? '#004085' : '#383d41') }}; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold;">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="color: #666; margin: 0; font-style: italic;">No appointments found for this patient.</p>
        @endif
    </div>

    <!-- Medical Records Summary -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <div style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #ddd;">
            <h3 style="margin: 0 0 20px 0; color: #333; font-size: 18px;">Recent Prenatal Records</h3>
            @if($prenatalRecords->count() > 0)
                @foreach($prenatalRecords as $record)
                
                    <div style="padding: 10px; border: 1px solid #eee; border-radius: 5px; margin-bottom: 10px;">
                        <strong style="color: #333;">{{ \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') }}</strong><br>
                        <small style="color: #666;">{{ $record->provider->name ?? 'Unknown Provider' }}</small>
                    </div>
                @endforeach
            @else
                <p style="color: #666; margin: 0; font-style: italic;">No prenatal records found.</p>
            @endif
        </div>

        <div style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #ddd;">
            <h3 style="margin: 0 0 20px 0; color: #333; font-size: 18px;">Recent Lab Results</h3>
            @if($labResults->count() > 0)
                @foreach($labResults as $result)
                    <div style="padding: 10px; border: 1px solid #eee; border-radius: 5px; margin-bottom: 10px;">
                        <strong style="color: #333;">{{ $result->test_name ?? 'Lab Test' }}</strong><br>
                        <small style="color: #666;">{{ \Carbon\Carbon::parse($result->test_ordered_date_time)->format('M d, Y') }}</small>
                    </div>
                @endforeach
            @else
                <p style="color: #666; margin: 0; font-style: italic;">No lab results found.</p>
            @endif
        </div>
    </div>
</div>
@endsection
