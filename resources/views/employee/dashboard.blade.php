@extends('employee.layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">

    <!-- Header -->
    <div style="margin-bottom: 30px;">
        <h1 style="color: #333; margin: 0; font-size: 28px;">Welcome, {{ Auth::user()->name }}</h1>
        <p style="color: #666; margin: 5px 0 0 0;">Employee Dashboard</p>
    </div>

    <!-- Quick Actions -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <h3 style="margin: 0 0 15px 0; color: #333;">Quick Actions</h3>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('employee.attendance') }}" style="background: #007bff; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px; display: inline-block;">
                <i class="fas fa-clock me-1"></i>View Attendance
            </a>
            <a href="{{ route('employee.patients') }}" style="background: #17a2b8; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px; display: inline-block;">
                <i class="fas fa-users me-1"></i>Manage Patients
            </a>
            <a href="{{ route('employee.schedule') }}" style="background: #28a745; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px; display: inline-block;">
                <i class="fas fa-calendar me-1"></i>View Schedule
            </a>
        </div>
        <div style="margin-top: 15px;">
            <small style="color: #666;">
                <i class="fas fa-info-circle me-1"></i>
                Note: Attendance is managed by your administrator. Use the attendance system when prompted.
            </small>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Main Content Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">

        <!-- Today's Schedule -->
        <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
            <h3 style="margin: 0 0 15px 0; color: #333;">Today's Schedule</h3>
            @if($todaySchedule)
                <div style="background: #e3f2fd; padding: 15px; border-radius: 5px;">
                    <strong style="font-size: 18px; color: #1976d2;">{{ $todaySchedule->day_name }}</strong><br>
                    <span style="color: #666;">{{ $todaySchedule->start_time }} - {{ $todaySchedule->end_time }}</span>
                </div>
            @else
                <p style="color: #666; margin: 0;">No schedule for today</p>
            @endif
        </div>

        <!-- Upcoming Appointments -->
        <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
            <h3 style="margin: 0 0 15px 0; color: #333;">Upcoming Appointments</h3>
            @if($upcomingAppointments->count() > 0)
                <div style="max-height: 300px; overflow-y: auto;">
                    @foreach($upcomingAppointments as $appointment)
                        <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 10px; border-left: 4px solid #007bff;">
                            <div style="font-weight: bold; color: #333; font-size: 14px;">
                                {{ $appointment->patient->name ?? 'N/A' }}
                            </div>
                            <div style="color: #666; font-size: 12px; margin: 2px 0;">
                                {{ $appointment->service->name ?? 'N/A' }}
                            </div>
                            <div style="color: #007bff; font-size: 12px; font-weight: bold;">
                                <i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, h:i A') }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <div style="margin-top: 15px; text-align: center;">
                    <a href="{{ route('employee.appointments') }}" style="color: #007bff; text-decoration: none; font-size: 14px;">
                        <i class="fas fa-arrow-right me-1"></i>View All Appointments
                    </a>
                </div>
            @else
                <div style="text-align: center; padding: 20px;">
                    <i class="fas fa-calendar-times" style="font-size: 48px; color: #ccc; margin-bottom: 10px;"></i>
                    <p style="color: #666; margin: 0; font-size: 14px;">No upcoming appointments</p>
                </div>
            @endif
        </div>

        <!-- Employee Information -->
        <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
            <h3 style="margin: 0 0 15px 0; color: #333;">Employee Information</h3>
            @if($employeeProfile)
                <div style="line-height: 1.6;">
                    <p style="margin: 0;"><strong>Employee ID:</strong> {{ $employeeProfile->employee_id }}</p>
                    <p style="margin: 5px 0;"><strong>Position:</strong> {{ $employeeProfile->position }}</p>
                    <p style="margin: 5px 0;"><strong>Status:</strong>
                        <span style="background: {{ $employeeProfile->status === 'active' ? '#d4edda' : '#fff3cd' }}; color: {{ $employeeProfile->status === 'active' ? '#155724' : '#856404' }}; padding: 2px 8px; border-radius: 3px; font-size: 12px;">
                            {{ ucfirst($employeeProfile->status) }}
                        </span>
                    </p>
                    <p style="margin: 5px 0;"><strong>Hire Date:</strong> {{ $employeeProfile->hire_date ? $employeeProfile->hire_date->format('M d, Y') : 'N/A' }}</p>
                </div>
            @else
                <p style="color: #666; margin: 0;">Employee profile not found.</p>
            @endif
        </div>

        <!-- Recent Attendance -->
        <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
            <h3 style="margin: 0 0 15px 0; color: #333;">Recent Attendance</h3>
            @if($recentAttendance->count() > 0)
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa;">
                                <th style="padding: 8px; text-align: left; border: 1px solid #ddd; font-weight: bold; font-size: 12px;">Date</th>
                                <th style="padding: 8px; text-align: left; border: 1px solid #ddd; font-weight: bold; font-size: 12px;">Time In</th>
                                <th style="padding: 8px; text-align: left; border: 1px solid #ddd; font-weight: bold; font-size: 12px;">Time Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentAttendance as $attendance)
                                <tr>
                                    <td style="padding: 8px; border: 1px solid #ddd; font-size: 12px;">{{ \Carbon\Carbon::parse($attendance->date)->format('M d') }}</td>
                                    <td style="padding: 8px; border: 1px solid #ddd; font-size: 12px;">{{ $attendance->clock_in ?: '-' }}</td>
                                    <td style="padding: 8px; border: 1px solid #ddd; font-size: 12px;">{{ $attendance->clock_out ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="color: #666; margin: 0; font-size: 14px;">No attendance records found.</p>
            @endif
        </div>
    </div>
</div>
@endsection
