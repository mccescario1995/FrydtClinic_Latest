<?php
namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\DeliveryRecord;
use App\Models\LaboratoryResult;
use App\Models\Payment;
use App\Models\PostnatalRecord;
use App\Models\PostpartumRecord;
use App\Models\PrenatalRecord;
use App\Models\User;
use App\Models\Service;
use App\Models\Payroll;
use App\Models\EmployeeAttendance;
use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\ActivityLogger;

class AdminController extends Controller
{
    /**
     * Admin dashboard
     */
    public function dashboard()
    {
        // Key metrics
        $totalUsers           = User::count();
        $totalPatients        = User::where('user_type', 'patient')->count();
        $totalEmployees       = User::where('user_type', 'employee')->count();
        $totalAppointments    = Appointment::count();
        $upcomingAppointments = Appointment::where('appointment_datetime', '>', now())
            ->where('status', '!=', 'cancelled')
            ->count();
        $completedAppointments = Appointment::where('status', 'completed')->count();

        // Recent appointments
        $recentAppointments = Appointment::with(['patient', 'employee', 'service'])
            ->orderBy('appointment_datetime', 'desc')
            ->limit(10)
            ->get();

        // Medical records stats
        $prenatalRecords   = PrenatalRecord::count();
        $postnatalRecords  = PostnatalRecord::count();
        $postpartumRecords = PostpartumRecord::count();
        $deliveryRecords   = DeliveryRecord::count();
        $labResults        = LaboratoryResult::count();

        // Inventory alerts
        $expiringItems = \App\Models\Inventory::active()->expiringSoon()->get();
        $expiredItems = \App\Models\Inventory::active()->where('expiry_date', '<', now())->get();
        $lowStockItems = \App\Models\Inventory::active()->lowStock()->get();
        $outOfStockItems = \App\Models\Inventory::active()->where('current_quantity', 0)->get();

        return view('admin-portal.dashboard', compact(
            'totalUsers',
            'totalPatients',
            'totalEmployees',
            'totalAppointments',
            'upcomingAppointments',
            'completedAppointments',
            'recentAppointments',
            'prenatalRecords',
            'postnatalRecords',
            'postpartumRecords',
            'deliveryRecords',
            'labResults',
            'expiringItems',
            'expiredItems',
            'lowStockItems',
            'outOfStockItems'
        ));
    }

    /**
     * Users management
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Filter by user type
        if ($request->has('type') && ! is_null($request->type) && $request->type !== '') {
            $query->where('user_type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && ! is_null($request->status) && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search by name or email
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query
            ->with('patientProfile', 'employeeProfile')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin-portal.users', compact('users'));
    }

    /**
     * Show user details
     */
    public function showUser($userId)
    {
        $user = User::with('patientProfile', 'employeeProfile')->findOrFail($userId);

        // Ensure patient has a profile if they are a patient
        if ($user->user_type === 'patient' && !$user->patientProfile) {
            $user->patientProfile()->create([
                'phone' => '',
                'birth_date' => now()->subYears(25)->toDateString(),
                'gender' => 'male',
                'address' => '',
                'emergency_contact_name' => null,
                'emergency_contact_phone' => null,
                'emergency_contact_relationship' => null,
            ]);
            $user->load('patientProfile');
        }

        // Get user's appointments
        $appointments = Appointment::where('patient_id', $user->id)
            ->with(['service', 'employee'])
            ->orderBy('appointment_datetime', 'desc')
            ->paginate(10);

        // Get medical records if patient
        $medicalRecords = [];
        if ($user->user_type === 'patient') {
            $medicalRecords = [
                'prenatal'   => PrenatalRecord::where('patient_id', $user->id)->count(),
                'postnatal'  => PostnatalRecord::where('patient_id', $user->id)->count(),
                'postpartum' => PostpartumRecord::where('patient_id', $user->id)->count(),
                'delivery'   => DeliveryRecord::where('patient_id', $user->id)->count(),
                'lab'        => LaboratoryResult::where('patient_id', $user->id)->count(),
            ];
        }

        // Get recent activity for this user
        $recentActivity = ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin-portal.show-user', compact('user', 'appointments', 'medicalRecords', 'recentActivity'));
    }

    /**
     * Show edit user form
     */
    public function editUser($userId)
    {
        $user = User::findOrFail($userId);

        return view('admin-portal.edit-user', compact('user'));
    }

    /**
     * Update user information
     */
    public function updateUser(Request $request, $userId)
    {
        $user        = User::findOrFail($userId);
        $currentUser = backpack_user();

        // Base validation for all users
        $validationRules = [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'pin' => 'nullable|numeric|digits:6|unique:users,pin,' . $user->id,
            'position' => 'nullable|string|max:255',
            'specialty' => 'nullable|string|max:255',
            'hourly_rate' => 'nullable|numeric|min:0',
            'phone' => 'nullable|philippine_phone',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'civil_status' => 'nullable|in:single,married,widowed,separated',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|philippine_phone',
            'emergency_contact_relationship' => 'nullable|string|max:100',
        ];

        // Only admins can update user_type and status
        if ($currentUser->user_type === 'admin') {
            $validationRules['user_type'] = 'required|in:admin,employee,patient';
            $validationRules['status']    = 'required|in:active,inactive';
        }

        $request->validate($validationRules);

        // Update basic info
        $updateData = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        // PIN is stored in users table for all users
        if ($request->filled('pin')) {
            $updateData['pin'] = $request->pin;
            $updateData['pin_verified'] = false; // Reset verification when PIN changes
            $updateData['pin_verified_at'] = null;
        } else {
            $updateData['pin'] = null;
            $updateData['pin_verified'] = false;
            $updateData['pin_verified_at'] = null;
        }

        // Only admins can update user_type and status
        if ($currentUser->user_type === 'admin') {
            $updateData['user_type'] = $request->user_type;
            $updateData['status']    = $request->status;
        }

        $user->update($updateData);

        // Prepare profile data
        $profileData = array_filter([
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'civil_status' => $request->civil_status,
            'address' => $request->address,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
        ], function ($value) {
            return $value !== null && $value !== '';
        });

        // Handle employee-specific fields
        if ($user->user_type === 'employee') {
            $profileData['position'] = $request->position;
            $profileData['hourly_rate'] = $request->hourly_rate;
            $profileData['specialty'] = $request->specialty;

            // Ensure employee profile exists
            if (!$user->employeeProfile) {
                $user->employeeProfile()->create($profileData);
            } else {
                $user->employeeProfile->update($profileData);
            }
        } elseif ($user->user_type === 'patient') {
            // Ensure patient profile exists
            if (!$user->patientProfile) {
                $user->patientProfile()->create($profileData);
            } else {
                $user->patientProfile->update($profileData);
            }
        }

        ActivityLogger::logUpdated($user, null, "Updated user {$user->name}");

        return redirect()->route('admin-portal.users.show', $user->id)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Show create user form
     */
    public function createUser()
    {
        return view('admin-portal.create-user');
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:admin,employee,patient',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|philippine_phone',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'civil_status' => 'nullable|in:single,married,widowed,separated',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|philippine_phone',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'specialty' => 'nullable|string|max:255',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'status' => 'active',
        ]);

        // Create profile based on user type
        if ($request->user_type === 'patient') {
            $user->patientProfile()->create([
                'phone' => $request->phone,
                'birth_date' => $request->birth_date ?: now()->subYears(25)->toDateString(),
                'gender' => $request->gender ?: 'male',
                'civil_status' => $request->civil_status,
                'address' => $request->address,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
            ]);
        } elseif ($request->user_type === 'employee') {
            $user->employeeProfile()->create([
                'position' => $request->position,
                'phone' => $request->phone,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender ?: 'male',
                'civil_status' => $request->civil_status,
                'address' => $request->address,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
                // 'specialty' => $request->specialty,
                'hourly_rate' => 0, // Default, can be updated later
                'pin' => null, // Will be set later
            ]);
        }
        // Admin users don't need additional profiles

        ActivityLogger::logCreated($user, "Created new {$request->user_type} user {$user->name}");

        return redirect()->route('admin-portal.account.created', $user->id);
    }

    /**
     * Appointments management
     */
    public function appointments(Request $request)
    {
        $query = Appointment::with(['patient', 'employee', 'service']);

        // Filter by status
        if ($request->has('status') && ! is_null($request->status) && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && ! is_null($request->date_from) && $request->date_from !== '') {
            $query->where('appointment_datetime', '>=', $request->date_from . ' 00:00:00');
        }
        if ($request->has('date_to') && ! is_null($request->date_to) && $request->date_to !== '') {
            $query->where('appointment_datetime', '<=', $request->date_to . ' 23:59:59');
        }

        // Search by patient name
        if ($request->has('search') && ! is_null($request->search) && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $appointments = $query->orderBy('appointment_datetime', 'desc')->paginate(20);

        return view('admin-portal.appointments', compact('appointments'));
    }

    /**
     * Show appointment details
     */
    public function showAppointment($appointmentId)
    {
        $appointment = Appointment::with(['patient.patientProfile', 'employee.employeeProfile', 'service', 'payments'])
            ->findOrFail($appointmentId);

        return view('admin-portal.show-appointment', compact('appointment'));
    }

    /**
     * Update appointment status
     */
    public function updateAppointmentStatus(Request $request, $appointmentId)
    {
        $request->validate([
            'status' => 'required|in:scheduled,confirmed,completed,cancelled',
        ]);

        $appointment = Appointment::findOrFail($appointmentId);
        $oldStatus = $appointment->status;
        $appointment->update([
            'status' => $request->status,
        ]);

        // Fire SMS notification event if appointment is confirmed
        if ($request->status === 'confirmed' && $oldStatus !== 'confirmed') {
            event(new \App\Events\AppointmentConfirmed($appointment));
        }

        // Fire SMS notification event if appointment is cancelled
        if ($request->status === 'cancelled') {
            event(new \App\Events\AppointmentCancelled($appointment));
        }

        ActivityLogger::logUpdated($appointment, ['status' => $oldStatus], "Updated appointment status from {$oldStatus} to {$request->status}");

        return response()->json([
            'success' => true,
            'message' => 'Appointment status updated successfully.',
            'status'  => $appointment->status,
        ]);
    }

    /**
     * Medical records overview
     */
    public function medicalRecords(Request $request)
    {
        $stats = [
            'prenatal'   => PrenatalRecord::count(),
            'postnatal'  => PostnatalRecord::count(),
            'postpartum' => PostpartumRecord::count(),
            'delivery'   => DeliveryRecord::count(),
            'lab'        => LaboratoryResult::count(),
        ];

        // Recent records
        $recentPrenatal = PrenatalRecord::with(['patient', 'attendingPhysician', 'midwife'])->orderBy('visit_date', 'desc')->limit(5)->get();
        $recentPostnatal = PostnatalRecord::with(['patient', 'provider'])->orderBy('visit_date', 'desc')->limit(5)->get();
        $recentPostpartum = PostpartumRecord::with(['patient', 'provider'])->orderBy('visit_date', 'desc')->limit(5)->get();
        $recentDelivery = DeliveryRecord::with(['patient', 'attendingProvider'])->orderBy('delivery_date_time', 'desc')->limit(5)->get();
        $recentLab      = LaboratoryResult::with('patient')->orderBy('test_ordered_date_time', 'desc')->limit(5)->get();

        return view('admin-portal.medical-records', compact('stats', 'recentPrenatal', 'recentPostnatal', 'recentPostpartum', 'recentDelivery', 'recentLab'));
    }

    /**
     * Prenatal records management
     */
    public function prenatalRecords(Request $request)
    {
        $query = PrenatalRecord::with(['patient', 'attendingPhysician', 'midwife']);

        // Filter by patient
        if ($request->has('patient_id') && !is_null($request->patient_id) && $request->patient_id !== '') {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by provider
        if ($request->has('provider_id') && !is_null($request->provider_id) && $request->provider_id !== '') {
            $query->where('provider_id', $request->provider_id);
        }

        // Filter by date range
        if ($request->has('date_from') && !is_null($request->date_from) && $request->date_from !== '') {
            $query->where('visit_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !is_null($request->date_to) && $request->date_to !== '') {
            $query->where('visit_date', '<=', $request->date_to);
        }

        $prenatalRecords = $query->orderBy('visit_date', 'desc')->paginate(20);

        // Get patients and providers for filters
        $patients = User::where('user_type', 'patient')->orderBy('name')->get();
        $providers = User::where('user_type', 'employee')->orderBy('name')->get();

        return view('admin-portal.prenatal-records', compact('prenatalRecords', 'patients', 'providers'));
    }

    /**
     * Attendance management
     */
    public function attendance(Request $request)
    {
        $query = EmployeeAttendance::with('employee');

        // Debug: Check total records before filters
        $totalBeforeFilters = EmployeeAttendance::count();
        \Illuminate\Support\Facades\Log::info("Total attendance records in DB: " . $totalBeforeFilters);

        // Filter by employee
        if ($request->has('employee_id') && ! is_null($request->employee_id) && $request->employee_id !== '') {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by date range
        if ($request->has('date_from') && ! is_null($request->date_from) && $request->date_from !== '') {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && ! is_null($request->date_to) && $request->date_to !== '') {
            $query->where('date', '<=', $request->date_to);
        }

        // Filter by status
        if ($request->has('status') && ! is_null($request->status) && $request->status !== '') {
            switch ($request->status) {
                case 'present':
                    $query->whereNotNull('check_in_time');
                    break;
                case 'absent':
                    $query->whereNull('check_in_time');
                    break;
                case 'completed':
                    $query->whereNotNull('check_in_time')->whereNotNull('check_out_time');
                    break;
                case 'incomplete':
                    $query->whereNotNull('check_in_time')->whereNull('check_out_time');
                    break;
            }
        }

        $attendanceRecords = $query->orderBy('date', 'desc')->paginate(20);

        // Debug: Check query results
        \Illuminate\Support\Facades\Log::info("Attendance records after query: " . $attendanceRecords->count());
        \Illuminate\Support\Facades\Log::info("First record employee: " . ($attendanceRecords->first() ? $attendanceRecords->first()->employee : 'null'));

        // Get employees for filter dropdown
        $employees = User::where('user_type', 'employee')->orderBy('name')->get();

        // Calculate statistics
        $totalRecords  = $attendanceRecords->total();
        $presentDays   = $attendanceRecords->whereNotNull('check_in_time')->count();
        $completedDays = $attendanceRecords->whereNotNull('check_in_time')->whereNotNull('check_out_time')->count();

        return view('admin-portal.attendance', compact(
            'attendanceRecords',
            'employees',
            'totalRecords',
            'presentDays',
            'completedDays'
        ));
    }

    /**
     * Get attendance details for AJAX
     */
    public function getAttendanceDetails($attendanceId)
    {
        $attendance = \App\Models\EmployeeAttendance::with('employee')->findOrFail($attendanceId);

        return response()->json([
            'success'    => true,
            'attendance' => $attendance,
        ]);
    }

    /**
     * Delete attendance record
     */
    public function deleteAttendance($attendanceId)
    {
        $attendance = \App\Models\EmployeeAttendance::findOrFail($attendanceId);

        // Delete associated image files if they exist
        if ($attendance->image_proof_check_in) {
            \Illuminate\Support\Facades\Storage::delete('public/' . $attendance->image_proof_check_in);
        }
        if ($attendance->image_proof_check_out) {
            \Illuminate\Support\Facades\Storage::delete('public/' . $attendance->image_proof_check_out);
        }

        $attendance->delete();

        ActivityLogger::logDeleted($attendance, "Deleted attendance record for {$attendance->employee->name} on {$attendance->date}");

        return redirect()->back()->with('success', 'Attendance record deleted successfully.');
    }

    /**
     * Show edit attendance form
     */
    public function editAttendance($attendanceId)
    {
        $attendance = \App\Models\EmployeeAttendance::with('employee')->findOrFail($attendanceId);

        return view('admin-portal.edit-attendance', compact('attendance'));
    }

    /**
     * Update attendance record
     */
    public function updateAttendance(Request $request, $attendanceId)
    {
        $request->validate([
            'check_in_time'  => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after:check_in_time',
        ]);

        $attendance = \App\Models\EmployeeAttendance::findOrFail($attendanceId);

        $updateData = [];

        if ($request->filled('check_in_time')) {
            $checkInDateTime             = Carbon::parse($attendance->date)->format('Y-m-d') . ' ' . $request->check_in_time . ':00';
            $updateData['check_in_time'] = $checkInDateTime;
        } else {
            $updateData['check_in_time'] = null;
        }

        if ($request->filled('check_out_time')) {
            $checkOutDateTime             = Carbon::parse($attendance->date)->format('Y-m-d') . ' ' . $request->check_out_time . ':00';
            $updateData['check_out_time'] = $checkOutDateTime;
        } else {
            $updateData['check_out_time'] = null;
        }

        $attendance->update($updateData);

        ActivityLogger::logUpdated($attendance, null, "Updated attendance record for {$attendance->employee->name}");

        return redirect()->route('admin-portal.attendance')->with('success', 'Attendance record updated successfully.');
    }

    /**
     * Admin manual clock in for employee
     */
    public function adminClockIn($employeeId)
    {
        $employee    = User::where('user_type', 'employee')->findOrFail($employeeId);
        $currentDate = Carbon::today()->toDateString();

        // Check if already clocked in today
        $existingAttendance = EmployeeAttendance::where('employee_id', $employeeId)
            ->where('date', $currentDate)
            ->first();

        if ($existingAttendance && $existingAttendance->check_in_time) {
            return redirect()->back()->with('error', 'Employee is already clocked in for today.');
        }

        if ($existingAttendance) {
            // Update existing record
            $existingAttendance->update([
                'check_in_time' => now(),
            ]);
            $attendance = $existingAttendance;
        } else {
            // Create new record
            $attendance = EmployeeAttendance::create([
                'employee_id'   => $employeeId,
                'date'          => $currentDate,
                'check_in_time' => now(),
            ]);
        }

        // Fire clock in event
        event(new \App\Events\EmployeeClockIn($attendance));

        ActivityLogger::log('clock_in', "Admin manually clocked in {$employee->name}", $employee);

        return redirect()->back()->with('success', 'Successfully clocked in ' . $employee->name);
    }

    /**
     * Admin manual clock out for employee
     */
    public function adminClockOut($employeeId)
    {
        $employee    = User::where('user_type', 'employee')->findOrFail($employeeId);
        $currentDate = Carbon::today()->toDateString();

        // Find today's attendance record
        $attendance = EmployeeAttendance::where('employee_id', $employeeId)
            ->where('date', $currentDate)
            ->first();

        if (! $attendance) {
            return redirect()->back()->with('error', 'No attendance record found for today. Employee must clock in first.');
        }

        if ($attendance->check_out_time) {
            return redirect()->back()->with('error', 'Employee is already clocked out for today.');
        }

        if (! $attendance->check_in_time) {
            return redirect()->back()->with('error', 'Employee has not clocked in yet.');
        }

        // Clock out
        $attendance->update([
            'check_out_time' => now(),
        ]);

        // Fire clock out event
        event(new \App\Events\EmployeeClockOut($attendance));

        ActivityLogger::log('clock_out', "Admin manually clocked out {$employee->name}", $employee);

        return redirect()->back()->with('success', 'Successfully clocked out ' . $employee->name);
    }

    /**
     * Reports and analytics
     */
    public function reports()
    {
        // Monthly appointment stats for the last 12 months
        $appointmentStats = [];
        for ($i = 11; $i >= 0; $i--) {
            $date      = now()->subMonths($i);
            $monthName = $date->format('M Y');
            $count     = Appointment::whereYear('appointment_datetime', $date->year)
                ->whereMonth('appointment_datetime', $date->month)
                ->count();
            $appointmentStats[] = [
                'month' => $monthName,
                'count' => $count,
            ];
        }

        // Appointment status distribution
        $statusStats = Appointment::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Active users count
        $activeUsers = User::where('status', 'active')->count();

        return view('admin-portal.reports', compact('appointmentStats', 'statusStats', 'activeUsers'));
    }

    /**
     * Patients management
     */
    public function patients(Request $request)
    {
        $query = User::where('user_type', 'patient');

        // Filter by status
        if ($request->has('status') && ! is_null($request->status) && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search by name or email
        if ($request->has('search') && ! is_null($request->search) && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $patients = $query
            ->with('patientProfile')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin-portal.patients', compact('patients'));
    }

    /**
     * Show create patient form
     */
    public function createPatient()
    {
        return view('admin-portal.create-patient');
    }

    /**
     * Store new patient
     */
    public function storePatient(Request $request)
    {
        $request->validate([
            'name'                           => 'required|string|max:255',
            'email'                          => 'required|email|unique:users,email',
            'password'                       => 'required|string|min:8|confirmed',
            'phone'                          => 'nullable|philippine_phone',
            'birth_date'                     => 'nullable|date|before:today',
            'gender'                         => 'nullable|in:male,female',
            'civil_status'                   => 'nullable|in:single,married,widowed,separated',
            'address'                        => 'nullable|string|max:500',
            'emergency_contact_name'         => 'nullable|string|max:255',
            'emergency_contact_phone'        => 'nullable|philippine_phone',
            'emergency_contact_relationship' => 'nullable|string|max:100',
        ]);

        // Create user
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'user_type' => 'patient',
            'status'    => 'active',
        ]);

        // Create patient profile with defaults for required fields
        $user->patientProfile()->create([
            'phone'                          => $request->phone,
            'birth_date'                     => $request->birth_date ?: now()->subYears(25)->toDateString(), // Default 25 years ago
            'gender'                         => $request->gender ?: 'male',                                  // Default gender
            'civil_status'                   => $request->civil_status,
            'address'                        => $request->address,
            'emergency_contact_name'         => $request->emergency_contact_name,
            'emergency_contact_phone'        => $request->emergency_contact_phone,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
        ]);

        ActivityLogger::logCreated($user, "Created new patient {$user->name}");

        return redirect()->route('admin-portal.patients')->with('success', 'Patient created successfully.');
    }

    /**
     * Show patient details
     */
    public function showPatient($patientId)
    {
        $patient = User::where('user_type', 'patient')
            ->where('id', $patientId)
            ->with('patientProfile')
            ->firstOrFail();

        // Ensure patient has a profile to prevent null reference errors
        if (!$patient->patientProfile) {
            $patient->patientProfile()->create([
                'phone' => '',
                'birth_date' => now()->subYears(25)->toDateString(),
                'gender' => 'male',
                'address' => '',
                'emergency_contact_name' => null,
                'emergency_contact_phone' => null,
                'emergency_contact_relationship' => null,
            ]);
            $patient->load('patientProfile');
        }

        // Get patient's appointments
        $appointments = \App\Models\Appointment::where('patient_id', $patient->id)
            ->with(['service', 'employee'])
            ->orderBy('appointment_datetime', 'desc')
            ->paginate(10);

        // Get patient's medical records
        $prenatalRecords = \App\Models\PrenatalRecord::where('patient_id', $patient->id)
            ->with(['attendingPhysician', 'midwife'])
            ->orderBy('visit_date', 'desc')
            ->limit(5)
            ->get();

        $postnatalRecords = \App\Models\PostnatalRecord::where('patient_id', $patient->id)
            ->with('provider')
            ->orderBy('visit_date', 'desc')
            ->limit(5)
            ->get();

        $postpartumRecords = \App\Models\PostpartumRecord::where('patient_id', $patient->id)
            ->with('provider')
            ->orderBy('visit_date', 'desc')
            ->limit(5)
            ->get();

        $deliveryRecords = \App\Models\DeliveryRecord::where('patient_id', $patient->id)
            ->with(['attendingProvider', 'deliveringProvider'])
            ->orderBy('delivery_date_time', 'desc')
            ->limit(5)
            ->get();

        $labResults = \App\Models\LaboratoryResult::where('patient_id', $patient->id)
            ->with(['orderingProvider', 'performingTechnician'])
            ->orderBy('test_ordered_date_time', 'desc')
            ->limit(5)
            ->get();

        return view('admin-portal.show-patient', compact(
            'patient',
            'appointments',
            'prenatalRecords',
            'postnatalRecords',
            'postpartumRecords',
            'deliveryRecords',
            'labResults'
        ));
    }

    /**
     * Show edit patient form
     */
    public function editPatient($patientId)
    {
        $patient = User::where('user_type', 'patient')
            ->where('id', $patientId)
            ->with('patientProfile')
            ->firstOrFail();

        return view('admin-portal.edit-patient', compact('patient'));
    }

    /**
     * Update patient information
     */
    public function updatePatient(Request $request, $patientId)
    {
        $patient = User::where('user_type', 'patient')
            ->where('id', $patientId)
            ->with('patientProfile')
            ->firstOrFail();

        $request->validate([
            'name'                           => 'required|string|max:255',
            'email'                          => 'required|email|unique:users,email,' . $patient->id,
            'phone'                          => 'nullable|philippine_phone',
            'birth_date'                     => 'nullable|date|before:today',
            'gender'                         => 'nullable|in:male,female',
            'civil_status'                   => 'nullable|in:single,married,widowed,separated',
            'address'                        => 'nullable|string|max:500',
            'emergency_contact_name'         => 'nullable|string|max:255',
            'emergency_contact_phone'        => 'nullable|philippine_phone',
            'emergency_contact_relationship' => 'nullable|string|max:100',
        ]);

        // Update user
        $patient->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        // Prepare profile data, filtering out null values for required fields
        $profileData = array_filter([
            'phone'                          => $request->phone,
            'gender'                         => $request->gender,
            'civil_status'                   => $request->civil_status,
            'address'                        => $request->address,
            'emergency_contact_name'         => $request->emergency_contact_name,
            'emergency_contact_phone'        => $request->emergency_contact_phone,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
        ], function ($value) {
            return $value !== null && $value !== '';
        });

        // Handle birth_date separately - only update if provided
        if ($request->filled('birth_date')) {
            $profileData['birth_date'] = $request->birth_date;
        }

        // Ensure patient has a profile, create one if missing
        if (! $patient->patientProfile) {
            // For new profiles, provide defaults for required fields
            $defaultProfileData = [
                'phone'                          => $request->phone,
                'birth_date'                     => $request->birth_date ?: now()->subYears(25)->toDateString(), // Default 25 years ago
                'gender'                         => $request->gender ?: 'male',                                  // Default gender
                'civil_status'                   => $request->civil_status,
                'address'                        => $request->address,
                'emergency_contact_name'         => $request->emergency_contact_name,
                'emergency_contact_phone'        => $request->emergency_contact_phone,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
            ];
            $patient->patientProfile()->create($defaultProfileData);
        } else {
            // Update existing patient profile - only update provided fields
            if (! empty($profileData)) {
                $patient->patientProfile->update($profileData);
            }
        }

        ActivityLogger::logUpdated($patient, null, "Updated patient {$patient->name}");

        return redirect()->route('admin-portal.patients.show', $patient->id)
            ->with('success', 'Patient updated successfully.');
    }

    /**
     * Delete patient
     */
    public function deletePatient($patientId)
    {
        $patient = User::where('user_type', 'patient')
            ->where('id', $patientId)
            ->firstOrFail();

        // Delete associated records first
        $patient->patientProfile()->delete();
        $patient->patientAppointments()->delete();
        // Delete medical records
        \App\Models\PrenatalRecord::where('patient_id', $patient->id)->delete();
        \App\Models\PostnatalRecord::where('patient_id', $patient->id)->delete();
        \App\Models\PostpartumRecord::where('patient_id', $patient->id)->delete();
        \App\Models\DeliveryRecord::where('patient_id', $patient->id)->delete();
        \App\Models\LaboratoryResult::where('patient_id', $patient->id)->delete();

        // Delete the user
        $patient->delete();

        return redirect()->route('admin-portal.patients')->with('success', 'Patient deleted successfully.');
    }

    /**
     * Show clock-in/out form for admin portal
     */
    public function clockInOut()
    {
        return view('admin-portal.clock-in-out');
    }

    /**
     * Process clock-in/out for admin portal
     */
    public function processClockInOut(Request $request)
    {
        // dd($request->all());
        // 1. Validate the PIN and image data
        $request->validate([
            'pin'        => 'required|exists:users,pin|digits:6',
            'image_data' => 'required',
        ], [
            'pin.required'        => 'Please enter your 6-digit PIN.',
            'pin.digits'          => 'Your PIN must be exactly 6 digits.',
            'pin.exists'          => 'The entered PIN does not match any record.',
            'image_data.required' => 'Please capture or upload an image.',
        ]);

        // 2. Find the employee and their attendance record for the day
        $user = \App\Models\User::where('pin', $request->pin)->where('user_type', 'employee')->first();
        if (!$user) {
            return redirect()->route('admin-portal.clock-in-out')->withErrors(['pin' => 'Invalid PIN or user is not an employee.']);
        }
        $employeeId = $user->id;
        $currentDate     = Carbon::today()->toDateString();

        $attendance = \App\Models\EmployeeAttendance::where('employee_id', $employeeId)
            ->where('date', $currentDate)
            ->first();

        // Decode the image
        $imageData = $request->image_data;
        $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);

        // 3. Determine if it's a 'time_in' or 'time_out'
        if (! $attendance) {
            // -------- TIME IN --------
            $actionType = 'time_in';
            $message    = 'Time In successful!';

            // Save the file to the 'public' disk
            $imageName = 'attendance_' . $employeeId . '_' . $currentDate . '_timein.jpeg';
            $imagePath = 'images/attendance/' . $imageName; // Path relative to storage/app/public/

            // Store the file on the public disk
            \Illuminate\Support\Facades\Storage::disk('public')->put($imagePath, base64_decode($imageData));

            // Save just the relative path in the database
            $imageStoragePath = $imagePath; // This will be 'images/attendance/filename.jpeg'

            $attendance = \App\Models\EmployeeAttendance::create([
                'employee_id'          => $employeeId,
                'date'                 => $currentDate,
                'check_in_time'        => now(),
                'image_proof_check_in' => $imageStoragePath, // Store: 'images/attendance/filename.jpeg'
            ]);

            // Fire clock in event
            event(new \App\Events\EmployeeClockIn($attendance));

            ActivityLogger::log('clock_in', "Employee {$user->name} clocked in", $user);

        } elseif (! $attendance->check_out_time) {
            // -------- TIME OUT --------
            $actionType = 'time_out';
            $message    = 'Time Out successful!';

            $imageName = 'attendance_' . $employeeId . '_' . $currentDate . '_timeout.jpeg';
            $imagePath = 'images/attendance/' . $imageName;

            \Illuminate\Support\Facades\Storage::disk('public')->put($imagePath, base64_decode($imageData));

            $imageStoragePath = $imagePath;

            $attendance->update([
                'check_out_time'        => now(),
                'image_proof_check_out' => $imageStoragePath,
            ]);

            // Fire clock out event
            event(new \App\Events\EmployeeClockOut($attendance));

            ActivityLogger::log('clock_out', "Employee {$user->name} clocked out", $user);

        } else {
            // -------- ALREADY TIMED OUT --------
            return redirect()->route('admin-portal.clock-in-out')->with('error', 'You have already Timed Out today.');
        }

        return view('admin-portal.clock-in-out', compact('user', 'attendance', 'message', 'actionType'));
    }

    /**
     * Payroll management
     */
    public function payroll(Request $request)
    {
        $query = Payroll::with('employee');

        // Filter by employee
        $employeeId = $request->input('employee_id');
        if (!is_null($employeeId) && $employeeId !== '') {
            $query->where('employee_id', $employeeId);
        }

        // Filter by status
        if ($request->has('status') && !is_null($request->status) && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by pay period
        if ($request->has('pay_period_start') && !is_null($request->pay_period_start) && $request->pay_period_start !== '') {
            $query->where('pay_period_start', '>=', $request->pay_period_start);
        }
        if ($request->has('pay_period_end') && !is_null($request->pay_period_end) && $request->pay_period_end !== '') {
            $query->where('pay_period_end', '<=', $request->pay_period_end);
        }

        $payrolls = $query->orderBy('pay_period_end', 'desc')->paginate(20);

        // Get employees for filter dropdown
        $employees = User::where('user_type', 'employee')->orderBy('name')->get();

        // Calculate statistics
        $totalPayrolls = $payrolls->total();
        $pendingPayrolls = Payroll::where('status', 'pending')->count();
        $processedPayrolls = Payroll::where('status', 'processed')->count();
        $paidPayrolls = Payroll::where('status', 'paid')->count();
        $totalAmountPaid = Payroll::where('status', 'paid')->sum('net_pay');

        return view('admin-portal.payroll', compact(
            'payrolls',
            'employees',
            'totalPayrolls',
            'pendingPayrolls',
            'processedPayrolls',
            'paidPayrolls',
            'totalAmountPaid'
        ));
    }

    // /**
    //  * Generate payroll for employees
    //  */
    // public function generatePayroll(Request $request)
    // {
    //     $request->validate([
    //         'pay_period_start' => 'required|date',
    //         'pay_period_end' => 'required|date|after:pay_period_start',
    //         'employee_ids' => 'required|array',
    //         'employee_ids.*' => 'exists:users,id',
    //         'overtime_rate' => 'required|numeric|min:0',
    //         'sss_deduction' => 'required|numeric|min:0',
    //         'philhealth_deduction' => 'required|numeric|min:0',
    //         'pagibig_deduction' => 'required|numeric|min:0',
    //         'tax_deduction' => 'required|numeric|min:0',
    //         'other_deductions' => 'required|numeric|min:0',
    //     ]);

    //     $payPeriodStart = Carbon::parse($request->pay_period_start);
    //     $payPeriodEnd = Carbon::parse($request->pay_period_end);

    //     $generatedPayrolls = [];
    //     $skippedEmployees = [];

    //     foreach ($request->employee_ids as $employeeId) {
    //         // Check if payroll already exists for this period
    //         $existingPayroll = Payroll::where('employee_id', $employeeId)
    //             ->where('pay_period_start', $payPeriodStart)
    //             ->where('pay_period_end', $payPeriodEnd)
    //             ->first();

    //         if ($existingPayroll) {
    //             $skippedEmployees[] = ['id' => $employeeId, 'reason' => 'Payroll already exists for this period'];
    //             continue; // Skip if already exists
    //         }

    //         // Get hourly rate from employee profile
    //         $employeeProfile = \App\Models\EmployeeProfile::where('employee_id', $employeeId)->first();
    //         $hourlyRate = $employeeProfile ? $employeeProfile->hourly_rate : 0;

    //         if ($hourlyRate <= 0) {
    //             $employee = User::find($employeeId);
    //             $employeeName = $employee ? $employee->name : 'Unknown Employee';
    //             $skippedEmployees[] = ['id' => $employeeId, 'name' => $employeeName, 'reason' => 'No hourly rate set'];
    //             continue; // Skip if no hourly rate set
    //         }

    //         $payroll = new Payroll([
    //             'employee_id' => $employeeId,
    //             'pay_period_start' => $payPeriodStart,
    //             'pay_period_end' => $payPeriodEnd,
    //             'hourly_rate' => $hourlyRate,
    //             'overtime_rate' => $request->overtime_rate,
    //             'sss_deduction' => $request->sss_deduction,
    //             'philhealth_deduction' => $request->philhealth_deduction,
    //             'pagibig_deduction' => $request->pagibig_deduction,
    //             'tax_deduction' => $request->tax_deduction,
    //             'other_deductions' => $request->other_deductions,
    //         ]);

    //         $payroll->calculatePay();
    //         $generatedPayrolls[] = $payroll;

    //         ActivityLogger::log('payroll_generated', "Payroll generated for {$payroll->employee->name}", $payroll->employee);
    //     }

    //     $message = 'Payroll generated for ' . count($generatedPayrolls) . ' employees.';

    //     if (!empty($skippedEmployees)) {
    //         $message .= ' Skipped ' . count($skippedEmployees) . ' employees: ';
    //         $skipReasons = [];
    //         foreach ($skippedEmployees as $skipped) {
    //             $name = $skipped['name'] ?? 'Employee #' . $skipped['id'];
    //             $skipReasons[] = $name . ' (' . $skipped['reason'] . ')';
    //         }
    //         $message .= implode(', ', $skipReasons);
    //     }

    //     return redirect()->route('admin-portal.payroll')->with('success', $message);
    // }

    /**
     * Process payroll (mark as processed)
     */
    public function processPayroll($payrollId)
    {
        $payroll = Payroll::findOrFail($payrollId);
        $payroll->update(['status' => 'processed']);

        ActivityLogger::logUpdated($payroll, ['status' => 'pending'], "Payroll processed for {$payroll->employee->name}");

        return redirect()->back()->with('success', 'Payroll marked as processed.');
    }

    /**
     * Mark payroll as paid
     */
    public function markPayrollAsPaid($payrollId)
    {
        $payroll = Payroll::findOrFail($payrollId);
        $payroll->markAsPaid();

        ActivityLogger::logUpdated($payroll, ['status' => 'processed'], "Payroll marked as paid for {$payroll->employee->name}");

        return redirect()->back()->with('success', 'Payroll marked as paid.');
    }

    /**
     * Generate pay slip PDF
     */
    public function generatePaySlip($payrollId)
    {
        $payroll = Payroll::with('employee')->findOrFail($payrollId);

        // Get attendance records for the pay period
        $attendanceRecords = EmployeeAttendance::where('employee_id', $payroll->employee_id)
            ->whereBetween('date', [$payroll->pay_period_start, $payroll->pay_period_end])
            ->orderBy('date', 'asc')
            ->get();

        // For now, return a simple HTML view that can be printed as PDF
        // In a real application, you might use a PDF library like TCPDF or DomPDF
        return view('admin-portal.pay-slip', compact('payroll', 'attendanceRecords'));
    }

    /**
     * Show payroll reports
     */
    public function payrollReports(Request $request)
    {
        $query = Payroll::with('employee');

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->where('pay_period_start', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->where('pay_period_end', '<=', $request->end_date);
        }

        $payrolls = $query->where('status', 'paid')->get();

        // Calculate summary statistics
        $totalGrossPay = $payrolls->sum('gross_pay');
        $totalDeductions = $payrolls->sum('deductions');
        $totalNetPay = $payrolls->sum('net_pay');
        $totalEmployees = $payrolls->unique('employee_id')->count();

        return view('admin-portal.payroll-reports', compact(
            'payrolls',
            'totalGrossPay',
            'totalDeductions',
            'totalNetPay',
            'totalEmployees'
        ));
    }

    /**
     * Documents management
     */
    public function documents(Request $request)
    {
        $query = Document::with('patient', 'uploader');

        // Filter by patient
        if ($request->has('patient_id') && !is_null($request->patient_id) && $request->patient_id !== '') {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by document type
        if ($request->has('document_type') && !is_null($request->document_type) && $request->document_type !== '') {
            $query->where('document_type', $request->document_type);
        }

        // Filter by status
        if ($request->has('status') && !is_null($request->status) && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search by title or description
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get patients for filter dropdown
        $patients = User::where('user_type', 'patient')->orderBy('name')->get();

        return view('admin-portal.documents', compact('documents', 'patients'));
    }

    /**
     * Show create document form
     */
    public function createDocument()
    {
        $patients = User::where('user_type', 'patient')->orderBy('name')->get();
        return view('admin-portal.create-document', compact('patients'));
    }

    /**
     * Store new document
     */
    public function storeDocument(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type' => 'required|string|max:100',
            'category' => 'nullable|in:prenatal,labor_delivery,postnatal,pediatric,general_medicine,surgical,emergency,administrative,other',
            'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'document_date' => 'nullable|date',
            'expiration_date' => 'nullable|date|after:today',
            'is_confidential' => 'boolean',
        ]);

        $file = $request->file('document_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        Document::create([
            'patient_id' => $request->patient_id,
            'uploaded_by' => backpack_user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'document_type' => $request->document_type,
            'category' => $request->category,
            'file_name' => $fileName,
            'original_file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_url' => asset('storage/' . $filePath),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_extension' => $file->getClientOriginalExtension(),
            'document_date' => $request->document_date,
            'expiration_date' => $request->expiration_date,
            'is_confidential' => $request->boolean('is_confidential'),
            'status' => 'active',
        ]);

        return redirect()->route('admin-portal.documents')->with('success', 'Document uploaded successfully.');
    }

    /**
     * Show document details
     */
    public function showDocument($documentId)
    {
        $document = Document::with('patient', 'uploader', 'signer', 'reviewer')->findOrFail($documentId);
        return view('admin-portal.show-document', compact('document'));
    }

    /**
     * Show edit document form
     */
    public function editDocument($documentId)
    {
        $document = Document::findOrFail($documentId);
        $patients = User::where('user_type', 'patient')->orderBy('name')->get();
        return view('admin-portal.edit-document', compact('document', 'patients'));
    }

    /**
     * Update document
     */
    public function updateDocument(Request $request, $documentId)
    {
        $document = Document::findOrFail($documentId);

        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type' => 'required|string|max:100',
            'category' => 'nullable|string|max:100',
            'document_date' => 'nullable|date',
            'expiration_date' => 'nullable|date',
            'is_confidential' => 'boolean',
            'status' => 'required|in:active,inactive,expired,archived',
        ]);

        $document->update($request->only([
            'patient_id', 'title', 'description', 'document_type', 'category',
            'document_date', 'expiration_date', 'is_confidential', 'status'
        ]));

        return redirect()->route('admin-portal.documents.show', $document->id)
            ->with('success', 'Document updated successfully.');
    }

    /**
     * Delete document
     */
    public function deleteDocument($documentId)
    {
        $document = Document::findOrFail($documentId);

        // Delete the file from storage
        if ($document->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($document->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('admin-portal.documents')->with('success', 'Document deleted successfully.');
    }

    /**
     * Download document
     */
    public function downloadDocument($documentId)
    {
        $document = Document::findOrFail($documentId);

        // Update download count and last accessed
        $document->increment('download_count');
        $document->update([
            'last_accessed_at' => now(),
            'last_accessed_by' => backpack_user()->id
        ]);

        return response()->download(storage_path('app/public/' . $document->file_path), $document->original_file_name);
    }

    /**
     * Services management
     */
    public function services(Request $request)
    {
        $query = Service::query();

        // Filter by type
        if ($request->has('type') && !is_null($request->type) && $request->type !== '') {
            $query->where('type', $request->type);
        }

        // Filter by service_type
        if ($request->has('service_type') && !is_null($request->service_type) && $request->service_type !== '') {
            $query->where('service_type', $request->service_type);
        }

        // Filter by category
        if ($request->has('category') && !is_null($request->category) && $request->category !== '') {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('status') && !is_null($request->status) && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search by name or code
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $services = $query->orderBy('name')->paginate(20);

        // Calculate statistics
        $totalServices = Service::count();
        $activeServices = Service::where('status', 'active')->count();
        $singleServices = Service::where('type', 'single')->count();
        $packageServices = Service::where('type', 'package')->count();

        return view('admin-portal.services', compact(
            'services',
            'totalServices',
            'activeServices',
            'singleServices',
            'packageServices'
        ));
    }

    /**
     * Show create service form
     */
    public function createService()
    {
        return view('admin-portal.create-service');
    }

    /**
     * Store new service
     */
    public function storeService(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:services,code',
            'description' => 'nullable|string',
            'type' => 'required|in:single,package',
            'service_type' => 'required|in:consultation,procedure,laboratory,imaging,therapy,vaccination,prenatal_care,delivery,postnatal_care,other',
            'category' => 'required|in:general_practice,obstetrics_gynecology,pediatrics,internal_medicine,surgery,emergency_care,preventive_care,diagnostic,therapeutic',
            'base_price' => 'required|numeric|min:0',
            'philhealth_price' => 'nullable|numeric|min:0',
            'philhealth_covered' => 'boolean',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'duration_minutes' => 'nullable|integer|min:1',
            'preparation_instructions' => 'nullable|string',
            'post_service_instructions' => 'nullable|string',
            'contraindications' => 'nullable|string',
            'required_equipment' => 'nullable|string',
            'required_supplies' => 'nullable|string',
            'staff_requirements' => 'nullable|string',
            'requires_appointment' => 'boolean',
            'available_emergency' => 'boolean',
            'requires_lab_results' => 'boolean',
            'advance_booking_days' => 'nullable|integer|min:0',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'quality_indicators' => 'nullable|string',
            'regulatory_requirements' => 'nullable|string',
            'consent_form_required' => 'nullable|string',
            'documentation_requirements' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $service = Service::create($request->all());

        ActivityLogger::logCreated($service, "Created new service {$service->name}");

        return redirect()->route('admin-portal.services')->with('success', 'Service created successfully.');
    }

    /**
     * Show service details
     */
    public function showService($serviceId)
    {
        $service = Service::findOrFail($serviceId);

        // Get related appointments
        $appointments = $service->appointments()
            ->with(['patient', 'employee'])
            ->orderBy('appointment_datetime', 'desc')
            ->limit(10)
            ->get();

        return view('admin-portal.show-service', compact('service', 'appointments'));
    }

    /**
     * Show edit service form
     */
    public function editService($serviceId)
    {
        $service = Service::findOrFail($serviceId);

        return view('admin-portal.edit-service', compact('service'));
    }

    /**
     * Update service
     */
    public function updateService(Request $request, $serviceId)
    {
        $service = Service::findOrFail($serviceId);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:services,code,' . $service->id,
            'description' => 'nullable|string',
            'type' => 'required|in:single,package',
            'service_type' => 'required|in:consultation,procedure,laboratory,imaging,therapy,vaccination,prenatal_care,delivery,postnatal_care,other',
            'category' => 'required|in:general_practice,obstetrics_gynecology,pediatrics,internal_medicine,surgery,emergency_care,preventive_care,diagnostic,therapeutic',
            'base_price' => 'required|numeric|min:0',
            'philhealth_price' => 'nullable|numeric|min:0',
            'philhealth_covered' => 'boolean',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'duration_minutes' => 'nullable|integer|min:1',
            'preparation_instructions' => 'nullable|string',
            'post_service_instructions' => 'nullable|string',
            'contraindications' => 'nullable|string',
            'required_equipment' => 'nullable|string',
            'required_supplies' => 'nullable|string',
            'staff_requirements' => 'nullable|string',
            'status' => 'required|in:active,inactive,discontinued',
            'requires_appointment' => 'boolean',
            'available_emergency' => 'boolean',
            'requires_lab_results' => 'boolean',
            'advance_booking_days' => 'nullable|integer|min:0',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'quality_indicators' => 'nullable|string',
            'regulatory_requirements' => 'nullable|string',
            'consent_form_required' => 'nullable|string',
            'documentation_requirements' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $service->update($request->all());

        ActivityLogger::logUpdated($service, null, "Updated service {$service->name}");

        return redirect()->route('admin-portal.services.show', $service->id)
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Delete service
     */
    public function deleteService($serviceId)
    {
        $service = Service::findOrFail($serviceId);

        // Check if service has related appointments
        if ($service->appointments()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete service that has associated appointments.');
        }

        $serviceName = $service->name;
        $service->delete();

        ActivityLogger::logDeleted($service, "Deleted service {$serviceName}");

        return redirect()->route('admin-portal.services')->with('success', 'Service deleted successfully.');
    }

    /**
     * Inventory management
     */
    public function inventory(Request $request)
    {
        $query = \App\Models\Inventory::query();

        // Filter by item type
        if ($request->has('item_type') && !is_null($request->item_type) && $request->item_type !== '') {
            $query->where('item_type', $request->item_type);
        }

        // Filter by category
        if ($request->has('category') && !is_null($request->category) && $request->category !== '') {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('status') && !is_null($request->status) && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by stock status
        if ($request->has('stock_status') && !is_null($request->stock_status) && $request->stock_status !== '') {
            switch ($request->stock_status) {
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->where('current_quantity', 0);
                    break;
                case 'expiring_soon':
                    $query->expiringSoon();
                    break;
                case 'expired':
                    $query->where('expiry_date', '<', now());
                    break;
            }
        }

        // Search by name, item code, or description
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('item_code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $inventory = $query->orderBy('name')->paginate(20);

        // Calculate statistics
        $totalItems = \App\Models\Inventory::count();
        $activeItems = \App\Models\Inventory::where('status', 'active')->count();
        $lowStockItems = \App\Models\Inventory::lowStock()->count();
        $outOfStockItems = \App\Models\Inventory::where('current_quantity', 0)->count();
        $expiringSoonItems = \App\Models\Inventory::expiringSoon()->count();
        $expiredItems = \App\Models\Inventory::where('expiry_date', '<', now())->count();

        // Inventory alerts
        $lowStockItemsCollection = \App\Models\Inventory::active()->lowStock()->get();
        $outOfStockItemsCollection = \App\Models\Inventory::active()->where('current_quantity', 0)->get();
        $expiringSoonItemsCollection = \App\Models\Inventory::active()->expiringSoon()->get();
        $expiredItemsCollection = \App\Models\Inventory::active()->where('expiry_date', '<', now())->get();

        return view('admin-portal.inventory', compact(
            'inventory',
            'totalItems',
            'activeItems',
            'lowStockItems',
            'outOfStockItems',
            'expiringSoonItems',
            'expiredItems',
            'lowStockItemsCollection',
            'outOfStockItemsCollection',
            'expiringSoonItemsCollection',
            'expiredItemsCollection'
        ));
    }

    /**
     * Show create inventory form
     */
    public function createInventory()
    {
        return view('admin-portal.create-inventory');
    }

    /**
     * Store new inventory item
     */
    public function storeInventory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'item_code' => 'required|string|max:255|unique:inventory,item_code',
            'description' => 'nullable|string',
            'manufacturer' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'item_type' => 'required|in:medical_supply,equipment,medication,consumable,durable_medical_equipment,laboratory_supply,office_supply,other',
            'category' => 'required|in:surgical_instruments,diagnostic_equipment,medications,bandages_dressings,gloves_masks,syringes_needles,laboratory_supplies,office_supplies,furniture,other',
            'current_quantity' => 'required|integer|min:0',
            'minimum_quantity' => 'nullable|integer|min:0',
            'maximum_quantity' => 'nullable|integer|min:0',
            'unit_of_measure' => 'required|string|max:100',
            'storage_location' => 'nullable|string|max:255',
            'room_number' => 'nullable|string|max:50',
            'cabinet_drawer' => 'nullable|string|max:100',
            'storage_conditions' => 'nullable|in:room_temperature,refrigerated,frozen,controlled_room,dark_place,other',
            'unit_cost' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'supplier_name' => 'nullable|string|max:255',
            'supplier_contact' => 'nullable|string|max:20',
            'expiry_date' => 'nullable|date|after:today',
            'batch_lot_number' => 'nullable|string|max:100',
            'fda_registration_number' => 'nullable|string|max:100',
            'requires_prescription' => 'boolean',
            'regulatory_notes' => 'nullable|string',
            'low_stock_alert' => 'boolean',
            'expiry_alert' => 'boolean',
            'alert_before_expiry_days' => 'nullable|integer|min:1|max:365',
            'special_handling_instructions' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        // Prepare data, handling empty strings for nullable fields
        $data = $request->all();

        // Set defaults for fields with defaults in migration
        $data['minimum_quantity'] = $request->filled('minimum_quantity') ? $request->minimum_quantity : 0;
        $data['usage_count'] = 0; // Default 0
        $data['low_stock_alert'] = $request->has('low_stock_alert') ? 1 : 0;
        $data['expiry_alert'] = $request->has('expiry_alert') ? 1 : 0;
        $data['alert_before_expiry_days'] = $request->filled('alert_before_expiry_days') ? $request->alert_before_expiry_days : 30;
        $data['requires_prescription'] = $request->has('requires_prescription') ? 1 : 0;
        $data['status'] = 'active'; // Default active

        // Convert empty strings to null for nullable fields
        $nullableFields = [
            'description', 'manufacturer', 'model_number', 'serial_number',
            'maximum_quantity', 'storage_location', 'room_number', 'cabinet_drawer',
            'storage_conditions', 'unit_cost', 'selling_price', 'supplier_name',
            'supplier_contact', 'expiry_date', 'batch_lot_number', 'fda_registration_number',
            'regulatory_notes', 'special_handling_instructions', 'internal_notes'
        ];

        foreach ($nullableFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        $inventory = \App\Models\Inventory::create($data);

        ActivityLogger::logCreated($inventory, "Created new inventory item {$inventory->name}");

        return redirect()->route('admin-portal.inventory')->with('success', 'Inventory item created successfully.');
    }

    /**
     * Show inventory item details
     */
    public function showInventory($inventoryId)
    {
        $inventory = \App\Models\Inventory::findOrFail($inventoryId);

        // Get recent movements
        $recentMovements = $inventory->movements()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin-portal.show-inventory', compact('inventory', 'recentMovements'));
    }

    /**
     * Show inventory movements for a specific item
     */
    public function inventoryMovements($inventoryId)
    {
        $inventory = \App\Models\Inventory::findOrFail($inventoryId);

        // Get all movements for this inventory item
        $movements = $inventory->movements()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin-portal.inventory-movements', compact('inventory', 'movements'));
    }

    /**
     * Show edit inventory form
     */
    public function editInventory($inventoryId)
    {
        $inventory = \App\Models\Inventory::findOrFail($inventoryId);

        return view('admin-portal.edit-inventory', compact('inventory'));
    }

    /**
     * Update inventory item
     */
    public function updateInventory(Request $request, $inventoryId)
    {
        $inventory = \App\Models\Inventory::findOrFail($inventoryId);

        $request->validate([
            'name' => 'required|string|max:255',
            'item_code' => 'required|string|max:255|unique:inventory,item_code,' . $inventory->id,
            'description' => 'nullable|string',
            'manufacturer' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'item_type' => 'required|in:medical_supply,equipment,medication,consumable,durable_medical_equipment,laboratory_supply,office_supply,other',
            'category' => 'required|in:surgical_instruments,diagnostic_equipment,medications,bandages_dressings,gloves_masks,syringes_needles,laboratory_supplies,office_supplies,furniture,other',
            'current_quantity' => 'required|integer|min:0',
            'minimum_quantity' => 'nullable|integer|min:0',
            'maximum_quantity' => 'nullable|integer|min:0',
            'unit_of_measure' => 'required|string|max:100',
            'storage_location' => 'nullable|string|max:255',
            'room_number' => 'nullable|string|max:50',
            'cabinet_drawer' => 'nullable|string|max:100',
            'storage_conditions' => 'nullable|in:room_temperature,refrigerated,frozen,controlled_room,dark_place,other',
            'unit_cost' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'supplier_name' => 'nullable|string|max:255',
            'supplier_contact' => 'nullable|string|max:20',
            'expiry_date' => 'nullable|date',
            'batch_lot_number' => 'nullable|string|max:100',
            'fda_registration_number' => 'nullable|string|max:100',
            'requires_prescription' => 'boolean',
            'regulatory_notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,discontinued,under_maintenance,out_of_stock,expired',
            'low_stock_alert' => 'boolean',
            'expiry_alert' => 'boolean',
            'alert_before_expiry_days' => 'nullable|integer|min:1|max:365',
            'special_handling_instructions' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $inventory->update($request->all());

        ActivityLogger::logUpdated($inventory, null, "Updated inventory item {$inventory->name}");

        return redirect()->route('admin-portal.inventory.show', $inventory->id)
            ->with('success', 'Inventory item updated successfully.');
    }

    /**
     * Update inventory stock
     */
    public function updateInventoryStock(Request $request, $inventoryId)
    {
        $request->validate([
            'new_quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $inventory = \App\Models\Inventory::findOrFail($inventoryId);

        $oldQuantity = $inventory->current_quantity;
        $newQuantity = $request->new_quantity;

        // Update stock using the model's method
        $inventory->updateStock($newQuantity, 'adjustment', $request->notes);

        ActivityLogger::logUpdated($inventory, ['current_quantity' => $oldQuantity], "Updated stock from {$oldQuantity} to {$newQuantity}");

        return redirect()->back()->with('success', 'Stock updated successfully.');
    }

    /**
     * Delete inventory item
     */
    public function deleteInventory($inventoryId)
    {
        $inventory = \App\Models\Inventory::findOrFail($inventoryId);

        // Check if item has movements
        if ($inventory->movements()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete inventory item that has stock movements.');
        }

        $itemName = $inventory->name;
        $inventory->delete();

        ActivityLogger::logDeleted($inventory, "Deleted inventory item {$itemName}");

        return redirect()->route('admin-portal.inventory')->with('success', 'Inventory item deleted successfully.');
    }

    /**
     * Employee schedules - now shows employee selector with filters
     */
    public function schedules(Request $request)
    {
        // Get employees with filters
        $query = User::where('user_type', 'employee')
            ->with('employeeProfile')
            ->withCount('schedules');

        // Search by name or email
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by position
        if ($request->has('position') && $request->position !== '') {
            $query->whereHas('employeeProfile', function ($q) use ($request) {
                $q->where('position', $request->position);
            });
        }

        // Filter by schedule status
        if ($request->has('schedule_status') && $request->schedule_status !== '') {
            if ($request->schedule_status === 'with_schedules') {
                $query->having('schedules_count', '>', 0);
            } elseif ($request->schedule_status === 'without_schedules') {
                $query->having('schedules_count', '=', 0);
            }
        }

        $employees = $query->orderBy('name')->paginate(20);

        // Calculate statistics
        $totalSchedules = \App\Models\EmployeeSchedule::count();
        $uniqueEmployees = \App\Models\EmployeeSchedule::distinct('employee_id')->count('employee_id');

        return view('admin-portal.schedules', compact(
            'employees',
            'totalSchedules',
            'uniqueEmployees'
        ));
    }

    /**
     * Show create schedule form
     */
    public function createSchedule()
    {
        $employees = User::where('user_type', 'employee')->orderBy('name')->get();
        return view('admin-portal.create-schedule', compact('employees'));
    }

    /**
     * Store new schedule
     */
    public function storeSchedule(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'days' => 'required|array|min:1',
            'days.*.enabled' => 'sometimes|accepted',
            'days.*.start_time' => 'required_if:days.*.enabled,1|nullable|date_format:H:i',
            'days.*.end_time' => 'required_if:days.*.enabled,1|nullable|date_format:H:i|after:days.*.start_time',
        ]);

        $employee = \App\Models\User::findOrFail($request->employee_id);
        $createdSchedules = [];
        $skippedSchedules = [];

        foreach ($request->days as $dayNum => $dayData) {
            // Skip if day is not enabled
            if (!isset($dayData['enabled']) || $dayData['enabled'] != '1') {
                continue;
            }

            // Check if schedule already exists for this employee and day
            $existingSchedule = \App\Models\EmployeeSchedule::where('employee_id', $request->employee_id)
                ->where('day_of_week', $dayNum)
                ->first();

            if ($existingSchedule) {
                $dayName = [
                    1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday',
                    5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'
                ][$dayNum] ?? 'Unknown';
                $skippedSchedules[] = $dayName;
                continue;
            }

            $schedule = \App\Models\EmployeeSchedule::create([
                'employee_id' => $request->employee_id,
                'day_of_week' => $dayNum,
                'start_time' => $dayData['start_time'],
                'end_time' => $dayData['end_time'],
            ]);

            $createdSchedules[] = $schedule;
        }

        // Log activity
        if (!empty($createdSchedules)) {
            $scheduleCount = count($createdSchedules);
            ActivityLogger::log('schedules_created', "Created {$scheduleCount} schedule(s) for {$employee->name}", $employee);
        }

        // Prepare success message
        $message = 'Schedule creation completed. ';
        if (!empty($createdSchedules)) {
            $message .= count($createdSchedules) . ' schedule(s) created successfully.';
        }
        if (!empty($skippedSchedules)) {
            $message .= ' Skipped ' . count($skippedSchedules) . ' day(s) with existing schedules: ' . implode(', ', $skippedSchedules) . '.';
        }

        return redirect()->route('admin-portal.schedules')->with('success', $message);
    }

    /**
     * Show schedule details
     */
    public function showSchedule($scheduleId)
    {
        $schedule = \App\Models\EmployeeSchedule::with('employee')->findOrFail($scheduleId);

        // Get employee's other schedules
        $otherSchedules = \App\Models\EmployeeSchedule::where('employee_id', $schedule->employee_id)
            ->where('id', '!=', $schedule->id)
            ->orderBy('day_of_week')
            ->get();

        return view('admin-portal.show-schedule', compact('schedule', 'otherSchedules'));
    }

    /**
     * Show edit schedule form
     */
    public function editSchedule($scheduleId)
    {
        $schedule = \App\Models\EmployeeSchedule::findOrFail($scheduleId);
        $employees = User::where('user_type', 'employee')->orderBy('name')->get();

        return view('admin-portal.edit-schedule', compact('schedule', 'employees'));
    }

    /**
     * Update schedule
     */
    public function updateSchedule(Request $request, $scheduleId)
    {
        $schedule = \App\Models\EmployeeSchedule::findOrFail($scheduleId);

        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'day_of_week' => 'required|integer|between:1,7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Check if schedule already exists for this employee and day (excluding current schedule)
        $existingSchedule = \App\Models\EmployeeSchedule::where('employee_id', $request->employee_id)
            ->where('day_of_week', $request->day_of_week)
            ->where('id', '!=', $schedule->id)
            ->first();

        if ($existingSchedule) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A schedule already exists for this employee on the selected day.');
        }

        $schedule->update($request->all());

        ActivityLogger::logUpdated($schedule, null, "Updated schedule for {$schedule->employee->name} on {$schedule->dayName}");

        return redirect()->route('admin-portal.schedules.show', $schedule->id)
            ->with('success', 'Schedule updated successfully.');
    }

    /**
     * Show manage employee schedules page
     */
    public function manageEmployeeSchedules($employeeId)
    {
        $employee = User::where('user_type', 'employee')->findOrFail($employeeId);
        $schedules = \App\Models\EmployeeSchedule::where('employee_id', $employeeId)
            ->orderBy('day_of_week')
            ->get();

        return view('admin-portal.manage-employee-schedules', compact('employee', 'schedules'));
    }

    /**
     * Update all schedules for an employee
     */
    public function updateEmployeeSchedules(Request $request, $employeeId)
    {
        $employee = User::where('user_type', 'employee')->findOrFail($employeeId);

        $request->validate([
            'days' => 'required|array|min:1',
            'days.*.enabled' => 'sometimes|accepted',
            'days.*.start_time' => 'required_if:days.*.enabled,1|nullable|date_format:H:i',
            'days.*.end_time' => 'required_if:days.*.enabled,1|nullable|date_format:H:i|after:days.*.start_time',
        ]);

        $createdSchedules = [];
        $updatedSchedules = [];
        $deletedSchedules = [];

        foreach ($request->days as $dayNum => $dayData) {
            $existingSchedule = \App\Models\EmployeeSchedule::where('employee_id', $employeeId)
                ->where('day_of_week', $dayNum)
                ->first();

            if (!isset($dayData['enabled']) || $dayData['enabled'] != '1') {
                // Remove schedule if it exists
                if ($existingSchedule) {
                    $existingSchedule->delete();
                    $deletedSchedules[] = $existingSchedule;
                }
                continue;
            }

            // Create or update schedule
            if ($existingSchedule) {
                $existingSchedule->update([
                    'start_time' => $dayData['start_time'],
                    'end_time' => $dayData['end_time'],
                ]);
                $updatedSchedules[] = $existingSchedule;
            } else {
                $newSchedule = \App\Models\EmployeeSchedule::create([
                    'employee_id' => $employeeId,
                    'day_of_week' => $dayNum,
                    'start_time' => $dayData['start_time'],
                    'end_time' => $dayData['end_time'],
                ]);
                $createdSchedules[] = $newSchedule;
            }
        }

        // Log activity
        $totalChanges = count($createdSchedules) + count($updatedSchedules) + count($deletedSchedules);
        if ($totalChanges > 0) {
            ActivityLogger::log('schedules_updated', "Updated schedules for {$employee->name}: {$totalChanges} changes", $employee);
        }

        // Prepare success message
        $message = 'Employee schedules updated successfully. ';
        if (!empty($createdSchedules)) {
            $message .= count($createdSchedules) . ' created, ';
        }
        if (!empty($updatedSchedules)) {
            $message .= count($updatedSchedules) . ' updated, ';
        }
        if (!empty($deletedSchedules)) {
            $message .= count($deletedSchedules) . ' deleted.';
        }

        return redirect()->route('admin-portal.schedules.manage', $employeeId)->with('success', rtrim($message, ', '));
    }

    /**
     * Delete schedule
     */
    public function deleteSchedule($scheduleId)
    {
        $schedule = \App\Models\EmployeeSchedule::findOrFail($scheduleId);

        $scheduleInfo = "{$schedule->employee->name} on {$schedule->dayName}";
        $schedule->delete();

        ActivityLogger::logDeleted($schedule, "Deleted schedule for {$scheduleInfo}");

        return redirect()->back()->with('success', 'Schedule deleted successfully.');
    }

    /**
     * System settings
     */
    public function settings()
    {
        return view('admin-portal.settings');
    }

    /**
     * SMS logs management
     */
    public function smsLogs(Request $request)
    {
        $query = \App\Models\SmsLog::with(['user', 'sender']);

        // Filter by status
        if ($request->has('status') && !is_null($request->status) && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by SMS type
        if ($request->has('sms_type') && !is_null($request->sms_type) && $request->sms_type !== '') {
            $query->where('sms_type', $request->sms_type);
        }

        // Filter by phone number
        if ($request->has('phone') && !is_null($request->phone) && $request->phone !== '') {
            $query->where('phone_number', 'like', '%' . $request->phone . '%');
        }

        // Filter by date range
        if ($request->has('date_from') && !is_null($request->date_from) && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !is_null($request->date_to) && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $smsLogs = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $totalLogs = \App\Models\SmsLog::count();
        $successfulLogs = \App\Models\SmsLog::where('status', 'sent')->count();
        $failedLogs = \App\Models\SmsLog::where('status', 'failed')->count();
        $pendingLogs = \App\Models\SmsLog::where('status', 'pending')->count();

        return view('admin-portal.sms-logs', compact(
            'smsLogs',
            'totalLogs',
            'successfulLogs',
            'failedLogs',
            'pendingLogs'
        ));
    }

    /**
     * Get SMS log details for AJAX
     */
    public function getSmsLogDetails($smsLogId)
    {
        $smsLog = \App\Models\SmsLog::with(['user', 'sender'])->findOrFail($smsLogId);

        return response()->json([
            'success' => true,
            'smsLog' => $smsLog,
        ]);
    }

    /**
     * Delete SMS log
     */
    public function deleteSmsLog($smsLogId)
    {
        $smsLog = \App\Models\SmsLog::findOrFail($smsLogId);

        $smsLog->delete();

        ActivityLogger::logDeleted($smsLog, "Deleted SMS log for {$smsLog->phone_number}");

        return redirect()->back()->with('success', 'SMS log deleted successfully.');
    }

    /**
     * Update SMS settings
     */
    public function updateSmsSettings(Request $request)
    {
        $request->validate([
            'iprogsms_token' => 'nullable|string|max:255',
            'iprogsms_url' => 'nullable|url',
            'admin_sms_number' => 'nullable|string|max:20',
            'sms_enabled' => 'nullable|boolean',
        ]);

        // Update settings in database
        \App\Models\Setting::set('iprogsms_token', $request->iprogsms_token ?: '', 'string', 'sms', 'iProgSMS API Token');
        \App\Models\Setting::set('iprogsms_url', $request->iprogsms_url ?: 'https://www.iprogsms.com/api/v1/sms_messages', 'string', 'sms', 'iProgSMS API URL');
        \App\Models\Setting::set('admin_sms_number', $request->admin_sms_number ?: '', 'string', 'sms', 'Admin SMS notification number');
        \App\Models\Setting::set('sms_enabled', $request->sms_enabled ? '1' : '0', 'boolean', 'sms', 'Enable SMS notifications');

        ActivityLogger::log('settings_updated', 'SMS settings updated by ' . backpack_user()->name);

        return redirect()->back()->with('success', 'SMS settings updated successfully.');
    }

    /**
     * Check SMS credits
     */
    public function checkSmsCredits(Request $request)
    {
        $smsService = app(\App\Services\SmsService::class);

        if (!$smsService->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'SMS service is not configured. Please check your settings.',
                'credits' => null
            ]);
        }

        $credits = $smsService->getCredits();

        if ($credits) {
            return response()->json([
                'success' => true,
                'message' => 'Credits retrieved successfully.',
                'credits' => $credits['load_balance']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve SMS credits. Please check your API configuration.',
                'credits' => null
            ]);
        }
    }

    /**
     * Test SMS functionality
     */
    public function testSms(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        $smsService = app(\App\Services\SmsService::class);

        if (!$smsService->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'SMS service is not configured. Please check your settings.'
            ]);
        }

        $result = $smsService->sendNotification($request->phone, 'This is a test SMS from FRYDT Clinic Management System.', [
            'type' => 'test',
            'sent_by' => backpack_user()->id,
            'metadata' => [
                'test_by' => backpack_user()->name,
                'test_time' => now()->toISOString(),
            ]
        ]);

        if ($result) {
            ActivityLogger::log('sms_test', 'Test SMS sent to ' . $request->phone . ' by ' . backpack_user()->name);
            return response()->json([
                'success' => true,
                'message' => 'Test SMS sent successfully!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test SMS. Please check your Twilio configuration.'
            ]);
        }
    }

    /**
     * Show edit PhilHealth form for a patient
     */
    public function editPatientPhilHealth($patientId)
    {
        $patient = User::where('user_type', 'patient')
            ->where('id', $patientId)
            ->with('patientProfile')
            ->firstOrFail();

        return view('admin-portal.edit-patient-philhealth', compact('patient'));
    }

    /**
     * Update patient PhilHealth information
     */
    public function updatePatientPhilHealth(Request $request, $patientId)
    {
        $patient = User::where('user_type', 'patient')
            ->where('id', $patientId)
            ->with('patientProfile')
            ->firstOrFail();

        $request->validate([
            'philhealth_membership' => 'nullable|in:yes,no',
            'philhealth_number' => 'nullable|string|max:20|regex:/^[0-9\-]+$/',
        ]);

        // Update patient profile PhilHealth information
        $patient->patientProfile->update([
            'philhealth_membership' => $request->philhealth_membership,
            'philhealth_number' => $request->philhealth_number,
        ]);

        ActivityLogger::logUpdated($patient, null, "Updated PhilHealth information for patient {$patient->name}");

        return redirect()->route('admin-portal.patients.show', $patient->id)
            ->with('success', 'PhilHealth information updated successfully.');
    }

    // ========== ADMIN MEDICAL RECORDS MANAGEMENT ==========

    /**
     * Admin prenatal records overview
     */
    public function adminPrenatalRecords(Request $request)
    {
        $query = PrenatalRecord::with(['patient', 'attendingPhysician', 'midwife']);

        // Filter by patient
        if ($request->has('patient_id') && !is_null($request->patient_id) && $request->patient_id !== '') {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by provider
        if ($request->has('provider_id') && !is_null($request->provider_id) && $request->provider_id !== '') {
            $query->where(function ($q) use ($request) {
                $q->where('attending_physician_id', $request->provider_id)
                  ->orWhere('midwife_id', $request->provider_id);
            });
        }

        // Filter by date range
        if ($request->has('date_from') && !is_null($request->date_from) && $request->date_from !== '') {
            $query->where('visit_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !is_null($request->date_to) && $request->date_to !== '') {
            $query->where('visit_date', '<=', $request->date_to);
        }

        $prenatalRecords = $query->orderBy('visit_date', 'desc')->paginate(20);

        // Get patients and providers for filters
        $patients = User::where('user_type', 'patient')->orderBy('name')->get();
        $providers = User::where('user_type', 'employee')->orderBy('name')->get();

        return view('admin-portal.medical-records.prenatal', compact('prenatalRecords', 'patients', 'providers'));
    }

    /**
     * Admin view patient's prenatal records
     */
    public function adminPatientPrenatalRecords($patientId)
    {
        $patient = User::findOrFail($patientId);
        $prenatalRecords = PrenatalRecord::where('patient_id', $patientId)
            ->orderBy('visit_date', 'desc')
            ->paginate(10);

        return view('admin-portal.patients.prenatal-records', compact('patient', 'prenatalRecords'));
    }

    /**
     * Admin create prenatal record form
     */
    public function adminCreatePrenatalRecord($patientId)
    {
        $patient = User::findOrFail($patientId);
        $providers = User::where('user_type', 'employee')->orderBy('name')->get();

        return view('admin-portal.patients.create-prenatal-record', compact('patient', 'providers'));
    }

    /**
     * Admin store prenatal record
     */
    public function adminStorePrenatalRecord(Request $request, $patientId)
    {
        $request->validate([
            'attending_physician_id' => 'required|exists:users,id',
            'midwife_id' => 'nullable|exists:users,id',
            'visit_date' => 'required|date',
            'visit_time' => 'nullable|date_format:H:i',
            'last_menstrual_period' => 'nullable|date',
            'estimated_due_date' => 'nullable|date',
            'gestational_age_weeks' => 'nullable|integer|min:0|max:42',
            'gestational_age_days' => 'nullable|integer|min:0|max:6',
            'gravida' => 'nullable|integer|min:0',
            'para' => 'nullable|integer|min:0',
            'abortion' => 'nullable|integer|min:0',
            'living_children' => 'nullable|integer|min:0',
            'blood_pressure_systolic' => 'nullable|numeric|min:0',
            'blood_pressure_diastolic' => 'nullable|numeric|min:0',
            'weight_kg' => 'nullable|numeric|min:0',
            'height_cm' => 'nullable|numeric|min:0',
            'bmi' => 'nullable|numeric|min:0',
            'pulse_rate' => 'nullable|integer|min:0',
            'respiratory_rate' => 'nullable|integer|min:0',
            'temperature_celsius' => 'nullable|numeric|min:0',
            'fetal_heart_rate' => 'nullable|integer|min:0',
            'fetal_position' => 'nullable|string|max:100',
            'fetal_presentation' => 'nullable|string|max:100',
            'fundal_height_cm' => 'nullable|numeric|min:0',
            'blood_type' => 'nullable|string|max:10',
            'hemoglobin_level' => 'nullable|string|max:50',
            'hematocrit_level' => 'nullable|string|max:50',
            'urinalysis' => 'nullable|string|max:500',
            'vdrl_test' => 'nullable|string|max:50',
            'hbsag_test' => 'nullable|string|max:50',
            'risk_factors' => 'nullable|string',
            'complications' => 'nullable|string',
            'risk_level' => 'nullable|in:low,moderate,high',
            'td_vaccine_given' => 'nullable|boolean',
            'td_vaccine_date' => 'nullable|date',
            'td_vaccine_dose' => 'nullable|integer|min:0',
            'medications' => 'nullable|string',
            'iron_supplements' => 'nullable|boolean',
            'calcium_supplements' => 'nullable|boolean',
            'vitamin_supplements' => 'nullable|boolean',
            'counseling_topics' => 'nullable|string',
            'patient_education' => 'nullable|string',
            'next_visit_date' => 'nullable|date',
            'next_visit_notes' => 'nullable|string',
            'pregnancy_status' => 'nullable|in:active,completed,terminated',
            'general_notes' => 'nullable|string',
            'physician_notes' => 'nullable|string',
        ]);

        PrenatalRecord::create([
            'patient_id' => $patientId,
            'attending_physician_id' => $request->attending_physician_id,
            'midwife_id' => $request->midwife_id,
            'visit_date' => $request->visit_date,
            'visit_time' => $request->visit_time,
            'last_menstrual_period' => $request->last_menstrual_period,
            'estimated_due_date' => $request->estimated_due_date,
            'gestational_age_weeks' => $request->gestational_age_weeks,
            'gestational_age_days' => $request->gestational_age_days,
            'gravida' => $request->gravida,
            'para' => $request->para,
            'abortion' => $request->abortion,
            'living_children' => $request->living_children,
            'blood_pressure_systolic' => $request->blood_pressure_systolic,
            'blood_pressure_diastolic' => $request->blood_pressure_diastolic,
            'weight_kg' => $request->weight_kg,
            'height_cm' => $request->height_cm,
            'bmi' => $request->bmi,
            'pulse_rate' => $request->pulse_rate,
            'respiratory_rate' => $request->respiratory_rate,
            'temperature_celsius' => $request->temperature_celsius,
            'fetal_heart_rate' => $request->fetal_heart_rate,
            'fetal_position' => $request->fetal_position,
            'fetal_presentation' => $request->fetal_presentation,
            'fundal_height_cm' => $request->fundal_height_cm,
            'blood_type' => $request->blood_type,
            'hemoglobin_level' => $request->hemoglobin_level,
            'hematocrit_level' => $request->hematocrit_level,
            'urinalysis' => $request->urinalysis,
            'vdrl_test' => $request->vdrl_test,
            'hbsag_test' => $request->hbsag_test,
            'risk_factors' => $request->risk_factors,
            'complications' => $request->complications,
            'risk_level' => $request->risk_level ?? 'low',
            'td_vaccine_given' => $request->has('td_vaccine_given') ? 1 : 0,
            'td_vaccine_date' => $request->td_vaccine_date,
            'td_vaccine_dose' => $request->td_vaccine_dose,
            'medications' => $request->medications,
            'iron_supplements' => $request->has('iron_supplements') ? 1 : 0,
            'calcium_supplements' => $request->has('calcium_supplements') ? 1 : 0,
            'vitamin_supplements' => $request->has('vitamin_supplements') ? 1 : 0,
            'counseling_topics' => $request->counseling_topics,
            'patient_education' => $request->patient_education,
            'next_visit_date' => $request->next_visit_date,
            'next_visit_notes' => $request->next_visit_notes,
            'pregnancy_status' => $request->pregnancy_status ?? 'active',
            'general_notes' => $request->general_notes,
            'physician_notes' => $request->physician_notes,
        ]);

        return redirect()->route('admin-portal.patients.prenatal-records', $patientId)
            ->with('success', 'Prenatal record created successfully.');
    }

    /**
     * Admin edit prenatal record form
     */
    public function adminEditPrenatalRecord($patientId, $recordId)
    {
        $patient = User::findOrFail($patientId);
        $record = PrenatalRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->firstOrFail();
        $providers = User::where('user_type', 'employee')->orderBy('name')->get();

        return view('admin-portal.patients.edit-prenatal-record', compact('patient', 'record', 'providers'));
    }

    /**
     * Admin update prenatal record
     */
    public function adminUpdatePrenatalRecord(Request $request, $patientId, $recordId)
    {
        $record = PrenatalRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

        $request->validate([
            'attending_physician_id' => 'required|exists:users,id',
            'midwife_id' => 'nullable|exists:users,id',
            'visit_date' => 'required|date',
            'visit_time' => 'nullable|date_format:H:i',
            'last_menstrual_period' => 'nullable|date',
            'estimated_due_date' => 'nullable|date',
            'gestational_age_weeks' => 'nullable|integer|min:0|max:42',
            'gestational_age_days' => 'nullable|integer|min:0|max:6',
            'gravida' => 'nullable|integer|min:0',
            'para' => 'nullable|integer|min:0',
            'abortion' => 'nullable|integer|min:0',
            'living_children' => 'nullable|integer|min:0',
            'blood_pressure_systolic' => 'nullable|numeric|min:0',
            'blood_pressure_diastolic' => 'nullable|numeric|min:0',
            'weight_kg' => 'nullable|numeric|min:0',
            'height_cm' => 'nullable|numeric|min:0',
            'bmi' => 'nullable|numeric|min:0',
            'pulse_rate' => 'nullable|integer|min:0',
            'respiratory_rate' => 'nullable|integer|min:0',
            'temperature_celsius' => 'nullable|numeric|min:0',
            'fetal_heart_rate' => 'nullable|integer|min:0',
            'fetal_position' => 'nullable|string|max:100',
            'fetal_presentation' => 'nullable|string|max:100',
            'fundal_height_cm' => 'nullable|numeric|min:0',
            'blood_type' => 'nullable|string|max:10',
            'hemoglobin_level' => 'nullable|string|max:50',
            'hematocrit_level' => 'nullable|string|max:50',
            'urinalysis' => 'nullable|string|max:500',
            'vdrl_test' => 'nullable|string|max:50',
            'hbsag_test' => 'nullable|string|max:50',
            'risk_factors' => 'nullable|string',
            'complications' => 'nullable|string',
            'risk_level' => 'nullable|in:low,moderate,high',
            'td_vaccine_given' => 'nullable|boolean',
            'td_vaccine_date' => 'nullable|date',
            'td_vaccine_dose' => 'nullable|integer|min:0',
            'medications' => 'nullable|string',
            'iron_supplements' => 'nullable|boolean',
            'calcium_supplements' => 'nullable|boolean',
            'vitamin_supplements' => 'nullable|boolean',
            'counseling_topics' => 'nullable|string',
            'patient_education' => 'nullable|string',
            'next_visit_date' => 'nullable|date',
            'next_visit_notes' => 'nullable|string',
            'pregnancy_status' => 'nullable|in:active,completed,terminated',
            'general_notes' => 'nullable|string',
            'physician_notes' => 'nullable|string',
        ]);

        $record->update([
            'attending_physician_id' => $request->attending_physician_id,
            'midwife_id' => $request->midwife_id,
            'visit_date' => $request->visit_date,
            'visit_time' => $request->visit_time,
            'last_menstrual_period' => $request->last_menstrual_period,
            'estimated_due_date' => $request->estimated_due_date,
            'gestational_age_weeks' => $request->gestational_age_weeks,
            'gestational_age_days' => $request->gestational_age_days,
            'gravida' => $request->gravida,
            'para' => $request->para,
            'abortion' => $request->abortion,
            'living_children' => $request->living_children,
            'blood_pressure_systolic' => $request->blood_pressure_systolic,
            'blood_pressure_diastolic' => $request->blood_pressure_diastolic,
            'weight_kg' => $request->weight_kg,
            'height_cm' => $request->height_cm,
            'bmi' => $request->bmi,
            'pulse_rate' => $request->pulse_rate,
            'respiratory_rate' => $request->respiratory_rate,
            'temperature_celsius' => $request->temperature_celsius,
            'fetal_heart_rate' => $request->fetal_heart_rate,
            'fetal_position' => $request->fetal_position,
            'fetal_presentation' => $request->fetal_presentation,
            'fundal_height_cm' => $request->fundal_height_cm,
            'blood_type' => $request->blood_type,
            'hemoglobin_level' => $request->hemoglobin_level,
            'hematocrit_level' => $request->hematocrit_level,
            'urinalysis' => $request->urinalysis,
            'vdrl_test' => $request->vdrl_test,
            'hbsag_test' => $request->hbsag_test,
            'risk_factors' => $request->risk_factors,
            'complications' => $request->complications,
            'risk_level' => $request->risk_level,
            'td_vaccine_given' => $request->has('td_vaccine_given') ? 1 : 0,
            'td_vaccine_date' => $request->td_vaccine_date,
            'td_vaccine_dose' => $request->td_vaccine_dose,
            'medications' => $request->medications,
            'iron_supplements' => $request->has('iron_supplements') ? 1 : 0,
            'calcium_supplements' => $request->has('calcium_supplements') ? 1 : 0,
            'vitamin_supplements' => $request->has('vitamin_supplements') ? 1 : 0,
            'counseling_topics' => $request->counseling_topics,
            'patient_education' => $request->patient_education,
            'next_visit_date' => $request->next_visit_date,
            'next_visit_notes' => $request->next_visit_notes,
            'pregnancy_status' => $request->pregnancy_status,
            'general_notes' => $request->general_notes,
            'physician_notes' => $request->physician_notes,
        ]);

        return redirect()->route('admin-portal.patients.prenatal-records', $patientId)
            ->with('success', 'Prenatal record updated successfully.');
    }

    /**
     * Admin delete prenatal record
     */
    public function adminDeletePrenatalRecord($patientId, $recordId)
    {
        $record = PrenatalRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

        $record->delete();

        return redirect()->route('admin-portal.patients.prenatal-records', $patientId)
            ->with('success', 'Prenatal record deleted successfully.');
    }

    // ========== POSTNATAL RECORDS ==========

    /**
     * Admin postnatal records overview
     */
    public function adminPostnatalRecords(Request $request)
    {
        $query = PostnatalRecord::with(['patient', 'provider']);

        // Filter by patient
        if ($request->has('patient_id') && !is_null($request->patient_id) && $request->patient_id !== '') {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by provider
        if ($request->has('provider_id') && !is_null($request->provider_id) && $request->provider_id !== '') {
            $query->where('provider_id', $request->provider_id);
        }

        // Filter by date range
        if ($request->has('date_from') && !is_null($request->date_from) && $request->date_from !== '') {
            $query->where('visit_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !is_null($request->date_to) && $request->date_to !== '') {
            $query->where('visit_date', '<=', $request->date_to);
        }

        $postnatalRecords = $query->orderBy('visit_date', 'desc')->paginate(20);

        // Get patients and providers for filters
        $patients = User::where('user_type', 'patient')->orderBy('name')->get();
        $providers = User::where('user_type', 'employee')->orderBy('name')->get();

        return view('admin-portal.medical-records.postnatal', compact('postnatalRecords', 'patients', 'providers'));
    }

    /**
     * Admin view patient's postnatal records
     */
    public function adminPatientPostnatalRecords($patientId)
    {
        $patient = User::findOrFail($patientId);
        $postnatalRecords = PostnatalRecord::where('patient_id', $patientId)
            ->orderBy('visit_date', 'desc')
            ->paginate(10);

        return view('admin-portal.patients.postnatal-records', compact('patient', 'postnatalRecords'));
    }

    /**
     * Admin create postnatal record form
     */
    public function adminCreatePostnatalRecord($patientId)
    {
        $patient = User::findOrFail($patientId);
        $providers = User::where('user_type', 'employee')->orderBy('name')->get();

        return view('admin-portal.patients.create-postnatal-record', compact('patient', 'providers'));
    }

    /**
     * Admin store postnatal record
     */
    public function adminStorePostnatalRecord(Request $request, $patientId)
    {
        $request->validate([
            'provider_id' => 'required|exists:users,id',
            'visit_number' => 'required|integer|min:1',
            'visit_date' => 'required|date',
            'days_postpartum' => 'nullable|integer|min:0',
            'weeks_postpartum' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'blood_pressure_systolic' => 'nullable|integer|min:0',
            'blood_pressure_diastolic' => 'nullable|integer|min:0',
            'heart_rate' => 'nullable|integer|min:0',
            'temperature' => 'nullable|numeric|min:0',
            'respiratory_rate' => 'nullable|integer|min:0',
            'oxygen_saturation' => 'nullable|integer|min:0|max:100',
            'general_condition' => 'nullable|string|max:100',
            'breast_condition' => 'nullable|string|max:100',
            'uterus_condition' => 'nullable|string|max:100',
            'perineum_condition' => 'nullable|string|max:100',
            'lochia_condition' => 'nullable|string|max:100',
            'episiotomy_condition' => 'nullable|string|max:100',
            'breastfeeding_status' => 'nullable|string|max:100',
            'breastfeeding_notes' => 'nullable|string',
            'latch_assessment' => 'nullable|string|max:100',
            'newborn_check' => 'nullable|boolean',
            'newborn_weight' => 'nullable|numeric|min:0',
            'newborn_notes' => 'nullable|string',
            'family_planning_method' => 'nullable|string|max:100',
            'family_planning_counseling' => 'nullable|string',
            'chief_complaint' => 'nullable|string|max:500',
            'assessment' => 'nullable|string',
            'plan' => 'nullable|string',
            'medications_prescribed' => 'nullable|array',
            'instructions_given' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'next_visit_type' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'alerts_flags' => 'nullable|array',
            'quality_indicators_met' => 'nullable|array',
        ]);

        PostnatalRecord::create([
            'patient_id' => $patientId,
            'provider_id' => $request->provider_id,
            'visit_number' => $request->visit_number,
            'visit_date' => $request->visit_date,
            'days_postpartum' => $request->days_postpartum,
            'weeks_postpartum' => $request->weeks_postpartum,
            'weight' => $request->weight,
            'blood_pressure_systolic' => $request->blood_pressure_systolic,
            'blood_pressure_diastolic' => $request->blood_pressure_diastolic,
            'heart_rate' => $request->heart_rate,
            'temperature' => $request->temperature,
            'respiratory_rate' => $request->respiratory_rate,
            'oxygen_saturation' => $request->oxygen_saturation,
            'general_condition' => $request->general_condition,
            'breast_condition' => $request->breast_condition,
            'uterus_condition' => $request->uterus_condition,
            'perineum_condition' => $request->perineum_condition,
            'lochia_condition' => $request->lochia_condition,
            'episiotomy_condition' => $request->episiotomy_condition,
            'breastfeeding_status' => $request->breastfeeding_status,
            'breastfeeding_notes' => $request->breastfeeding_notes,
            'latch_assessment' => $request->latch_assessment,
            'newborn_check' => $request->newborn_check,
            'newborn_weight' => $request->newborn_weight,
            'newborn_notes' => $request->newborn_notes,
            'family_planning_method' => $request->family_planning_method,
            'family_planning_counseling' => $request->family_planning_counseling,
            'chief_complaint' => $request->chief_complaint,
            'assessment' => $request->assessment,
            'plan' => $request->plan,
            'medications_prescribed' => $request->medications_prescribed,
            'instructions_given' => $request->instructions_given,
            'follow_up_date' => $request->follow_up_date,
            'next_visit_type' => $request->next_visit_type,
            'notes' => $request->notes,
            'alerts_flags' => $request->alerts_flags,
            'quality_indicators_met' => $request->quality_indicators_met,
        ]);

        return redirect()->route('admin-portal.patients.postnatal-records', $patientId)
            ->with('success', 'Postnatal record created successfully.');
    }

    /**
     * Admin delete postnatal record
     */
    public function adminDeletePostnatalRecord($patientId, $recordId)
    {
        $record = PostnatalRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

        $record->delete();

        return redirect()->route('admin-portal.patients.postnatal-records', $patientId)
            ->with('success', 'Postnatal record deleted successfully.');
    }

    // ========== POSTPARTUM RECORDS ==========

    /**
     * Admin postpartum records overview
     */
    public function adminPostpartumRecords(Request $request)
    {
        $query = PostpartumRecord::with(['patient', 'provider']);

        // Filter by patient
        if ($request->has('patient_id') && !is_null($request->patient_id) && $request->patient_id !== '') {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by provider
        if ($request->has('provider_id') && !is_null($request->provider_id) && $request->provider_id !== '') {
            $query->where('provider_id', $request->provider_id);
        }

        // Filter by date range
        if ($request->has('date_from') && !is_null($request->date_from) && $request->date_from !== '') {
            $query->where('visit_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !is_null($request->date_to) && $request->date_to !== '') {
            $query->where('visit_date', '<=', $request->date_to);
        }

        $postpartumRecords = $query->orderBy('visit_date', 'desc')->paginate(20);

        // Get patients and providers for filters
        $patients = User::where('user_type', 'patient')->orderBy('name')->get();
        $providers = User::where('user_type', 'employee')->orderBy('name')->get();

        return view('admin-portal.medical-records.postpartum', compact('postpartumRecords', 'patients', 'providers'));
    }

    /**
     * Admin view patient's postpartum records
     */
    public function adminPatientPostpartumRecords($patientId)
    {
        $patient = User::findOrFail($patientId);
        $postpartumRecords = PostpartumRecord::where('patient_id', $patientId)
            ->orderBy('visit_date', 'desc')
            ->paginate(10);

        return view('admin-portal.patients.postpartum-records', compact('patient', 'postpartumRecords'));
    }

    /**
     * Admin create postpartum record form
     */
    public function adminCreatePostpartumRecord($patientId)
    {
        $patient = User::findOrFail($patientId);
        $providers = User::where('user_type', 'employee')->orderBy('name')->get();

        return view('admin-portal.patients.create-postpartum-record', compact('patient', 'providers'));
    }

    /**
     * Admin store postpartum record
     */
    public function adminStorePostpartumRecord(Request $request, $patientId)
    {
        $request->validate([
            'provider_id' => 'required|exists:users,id',
            'visit_number' => 'required|integer|min:1',
            'visit_date' => 'required|date',
            'weeks_postpartum' => 'nullable|integer|min:0',
            'days_postpartum' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'blood_pressure_systolic' => 'nullable|integer|min:0',
            'blood_pressure_diastolic' => 'nullable|integer|min:0',
            'heart_rate' => 'nullable|integer|min:0',
            'temperature' => 'nullable|numeric|min:0',
            'general_condition' => 'nullable|string|max:100',
            'breast_condition' => 'nullable|string|max:100',
            'uterus_condition' => 'nullable|string|max:100',
            'perineum_condition' => 'nullable|string|max:100',
            'lochia_condition' => 'nullable|string|max:100',
            'episiotomy_condition' => 'nullable|string|max:100',
            'mood_assessment' => 'nullable|string|max:100',
            'emotional_support_needs' => 'nullable|string',
            'postpartum_depression_screening' => 'nullable|boolean',
            'mental_health_notes' => 'nullable|string',
            'breastfeeding_status' => 'nullable|string|max:100',
            'breastfeeding_challenges' => 'nullable|string',
            'lactation_support' => 'nullable|string',
            'infant_feeding_assessment' => 'nullable|boolean',
            'infant_care_education' => 'nullable|string',
            'contraceptive_method' => 'nullable|string|max:100',
            'family_planning_counseling' => 'nullable|string',
            'next_contraceptive_visit' => 'nullable|date',
            'postpartum_complications' => 'nullable|string',
            'medications_prescribed' => 'nullable|string',
            'wound_care_instructions' => 'nullable|string',
            'activity_restrictions' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'follow_up_reason' => 'nullable|string|max:200',
            'education_provided' => 'nullable|array',
            'nutrition_counseling' => 'nullable|string',
            'exercise_guidance' => 'nullable|string',
            'warning_signs_education' => 'nullable|string',
            'assessment_notes' => 'nullable|string',
            'plan_notes' => 'nullable|string',
            'alerts_flags' => 'nullable|array',
            'quality_indicators_met' => 'nullable|array',
        ]);

        PostpartumRecord::create([
            'patient_id' => $patientId,
            'provider_id' => $request->provider_id,
            'visit_number' => $request->visit_number,
            'visit_date' => $request->visit_date,
            'weeks_postpartum' => $request->weeks_postpartum,
            'days_postpartum' => $request->days_postpartum,
            'weight' => $request->weight,
            'blood_pressure_systolic' => $request->blood_pressure_systolic,
            'blood_pressure_diastolic' => $request->blood_pressure_diastolic,
            'heart_rate' => $request->heart_rate,
            'temperature' => $request->temperature,
            'general_condition' => $request->general_condition,
            'breast_condition' => $request->breast_condition,
            'uterus_condition' => $request->uterus_condition,
            'perineum_condition' => $request->perineum_condition,
            'lochia_condition' => $request->lochia_condition,
            'episiotomy_condition' => $request->episiotomy_condition,
            'mood_assessment' => $request->mood_assessment,
            'emotional_support_needs' => $request->emotional_support_needs,
            'postpartum_depression_screening' => $request->postpartum_depression_screening,
            'mental_health_notes' => $request->mental_health_notes,
            'breastfeeding_status' => $request->breastfeeding_status,
            'breastfeeding_challenges' => $request->breastfeeding_challenges,
            'lactation_support' => $request->lactation_support,
            'infant_feeding_assessment' => $request->infant_feeding_assessment,
            'infant_care_education' => $request->infant_care_education,
            'contraceptive_method' => $request->contraceptive_method,
            'family_planning_counseling' => $request->family_planning_counseling,
            'next_contraceptive_visit' => $request->next_contraceptive_visit,
            'postpartum_complications' => $request->postpartum_complications,
            'medications_prescribed' => $request->medications_prescribed,
            'wound_care_instructions' => $request->wound_care_instructions,
            'activity_restrictions' => $request->activity_restrictions,
            'follow_up_date' => $request->follow_up_date,
            'follow_up_reason' => $request->follow_up_reason,
            'education_provided' => $request->education_provided,
            'nutrition_counseling' => $request->nutrition_counseling,
            'exercise_guidance' => $request->exercise_guidance,
            'warning_signs_education' => $request->warning_signs_education,
            'assessment_notes' => $request->assessment_notes,
            'plan_notes' => $request->plan_notes,
            'alerts_flags' => $request->alerts_flags,
            'quality_indicators_met' => $request->quality_indicators_met,
        ]);

        return redirect()->route('admin-portal.patients.postpartum-records', $patientId)
            ->with('success', 'Postpartum record created successfully.');
    }

    /**
     * Admin delete postpartum record
     */
    public function adminDeletePostpartumRecord($patientId, $recordId)
    {
        $record = PostpartumRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

        $record->delete();

        return redirect()->route('admin-portal.patients.postpartum-records', $patientId)
            ->with('success', 'Postpartum record deleted successfully.');
    }

    // ========== DELIVERY RECORDS ==========

    /**
     * Admin delivery records overview
     */
    public function adminDeliveryRecords(Request $request)
    {
        $query = DeliveryRecord::with(['patient', 'attendingProvider']);

        // Filter by patient
        if ($request->has('patient_id') && !is_null($request->patient_id) && $request->patient_id !== '') {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by provider
        if ($request->has('provider_id') && !is_null($request->provider_id) && $request->provider_id !== '') {
            $query->where('attending_provider_id', $request->provider_id);
        }

        // Filter by date range
        if ($request->has('date_from') && !is_null($request->date_from) && $request->date_from !== '') {
            $query->where('delivery_date_time', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !is_null($request->date_to) && $request->date_to !== '') {
            $query->where('delivery_date_time', '<=', $request->date_to);
        }

        $deliveryRecords = $query->orderBy('delivery_date_time', 'desc')->paginate(20);

        // Get patients and providers for filters
        $patients = User::where('user_type', 'patient')->orderBy('name')->get();
        $providers = User::where('user_type', 'employee')->orderBy('name')->get();

        return view('admin-portal.medical-records.delivery', compact('deliveryRecords', 'patients', 'providers'));
    }

    /**
     * Admin view patient's delivery records
     */
    public function adminPatientDeliveryRecords($patientId)
    {
        $patient = User::findOrFail($patientId);
        $deliveryRecords = DeliveryRecord::where('patient_id', $patientId)
            ->orderBy('delivery_date_time', 'desc')
            ->paginate(10);

        return view('admin-portal.patients.delivery-records', compact('patient', 'deliveryRecords'));
    }

    /**
     * Admin create delivery record form
     */
    public function adminCreateDeliveryRecord($patientId)
    {
        $patient = User::findOrFail($patientId);
        $providers = User::where('user_type', 'employee')->orderBy('name')->get();

        return view('admin-portal.patients.create-delivery-record', compact('patient', 'providers'));
    }

    /**
     * Admin store delivery record
     */
    public function adminStoreDeliveryRecord(Request $request, $patientId)
    {
        $request->validate([
            'attending_provider_id' => 'required|exists:users,id',
            'delivering_provider_id' => 'nullable|exists:users,id',
            'anesthesiologist_id' => 'nullable|exists:users,id',
            'admission_date_time' => 'required|date',
            'labor_onset_date_time' => 'nullable|date',
            'rupture_of_membranes_date_time' => 'nullable|date',
            'rupture_of_membranes_type' => 'nullable|string|max:50',
            'delivery_date_time' => 'required|date',
            'delivery_type' => 'nullable|string|max:50',
            'delivery_place' => 'nullable|string|max:100',
            'gravida' => 'nullable|integer|min:0',
            'para' => 'nullable|integer|min:0',
            'living_children' => 'nullable|integer|min:0',
            'prenatal_history' => 'nullable|string',
            'risk_factors' => 'nullable|string',
            'labor_duration_hours' => 'nullable|integer|min:0',
            'labor_duration_minutes' => 'nullable|integer|min:0',
            'labor_progress' => 'nullable|string',
            'labor_complications' => 'nullable|string',
            'presentation' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:50',
            'episiotomy_performed' => 'nullable|boolean',
            'episiotomy_degree' => 'nullable|string|max:20',
            'perineal_tear' => 'nullable|string',
            'delivery_complications' => 'nullable|string',
            'anesthesia_type' => 'nullable|string|max:50',
            'anesthesia_notes' => 'nullable|string',
            'newborn_gender' => 'nullable|string|max:10',
            'newborn_weight' => 'nullable|numeric|min:0',
            'newborn_length' => 'nullable|numeric|min:0',
            'newborn_apgar_1min' => 'nullable|integer|min:0|max:10',
            'newborn_apgar_5min' => 'nullable|integer|min:0|max:10',
            'newborn_apgar_10min' => 'nullable|integer|min:0|max:10',
            'newborn_condition' => 'nullable|string',
            'newborn_complications' => 'nullable|string',
            'placenta_delivery' => 'nullable|string|max:50',
            'placenta_complete' => 'nullable|boolean',
            'placenta_notes' => 'nullable|string',
            'estimated_blood_loss' => 'nullable|integer|min:0',
            'blood_pressure_systolic' => 'nullable|integer|min:0',
            'blood_pressure_diastolic' => 'nullable|integer|min:0',
            'heart_rate' => 'nullable|integer|min:0',
            'temperature' => 'nullable|numeric|min:0',
            'postpartum_care' => 'nullable|string',
            'medications_administered' => 'nullable|string',
            'breastfeeding_initiation' => 'nullable|string',
            'expected_discharge_date' => 'nullable|date',
            'discharge_instructions' => 'nullable|string',
            'follow_up_instructions' => 'nullable|string',
            'delivery_summary' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'quality_indicators' => 'nullable|array',
        ]);

        DeliveryRecord::create([
            'patient_id' => $patientId,
            'attending_provider_id' => $request->attending_provider_id,
            'delivering_provider_id' => $request->delivering_provider_id,
            'anesthesiologist_id' => $request->anesthesiologist_id,
            'admission_date_time' => $request->admission_date_time,
            'labor_onset_date_time' => $request->labor_onset_date_time,
            'rupture_of_membranes_date_time' => $request->rupture_of_membranes_date_time,
            'rupture_of_membranes_type' => $request->rupture_of_membranes_type,
            'delivery_date_time' => $request->delivery_date_time,
            'delivery_type' => $request->delivery_type,
            'delivery_place' => $request->delivery_place,
            'gravida' => $request->gravida,
            'para' => $request->para,
            'living_children' => $request->living_children,
            'prenatal_history' => $request->prenatal_history,
            'risk_factors' => $request->risk_factors,
            'labor_duration_hours' => $request->labor_duration_hours,
            'labor_duration_minutes' => $request->labor_duration_minutes,
            'labor_progress' => $request->labor_progress,
            'labor_complications' => $request->labor_complications,
            'presentation' => $request->presentation,
            'position' => $request->position,
            'episiotomy_performed' => $request->episiotomy_performed,
            'episiotomy_degree' => $request->episiotomy_degree,
            'perineal_tear' => $request->perineal_tear,
            'delivery_complications' => $request->delivery_complications,
            'anesthesia_type' => $request->anesthesia_type,
            'anesthesia_notes' => $request->anesthesia_notes,
            'newborn_gender' => $request->newborn_gender,
            'newborn_weight' => $request->newborn_weight,
            'newborn_length' => $request->newborn_length,
            'newborn_apgar_1min' => $request->newborn_apgar_1min,
            'newborn_apgar_5min' => $request->newborn_apgar_5min,
            'newborn_apgar_10min' => $request->newborn_apgar_10min,
            'newborn_condition' => $request->newborn_condition,
            'newborn_complications' => $request->newborn_complications,
            'placenta_delivery' => $request->placenta_delivery,
            'placenta_complete' => $request->placenta_complete,
            'placenta_notes' => $request->placenta_notes,
            'estimated_blood_loss' => $request->estimated_blood_loss,
            'blood_pressure_systolic' => $request->blood_pressure_systolic,
            'blood_pressure_diastolic' => $request->blood_pressure_diastolic,
            'heart_rate' => $request->heart_rate,
            'temperature' => $request->temperature,
            'postpartum_care' => $request->postpartum_care,
            'medications_administered' => $request->medications_administered,
            'breastfeeding_initiation' => $request->breastfeeding_initiation,
            'expected_discharge_date' => $request->expected_discharge_date,
            'discharge_instructions' => $request->discharge_instructions,
            'follow_up_instructions' => $request->follow_up_instructions,
            'delivery_summary' => $request->delivery_summary,
            'additional_notes' => $request->additional_notes,
            'quality_indicators' => $request->quality_indicators,
        ]);

        return redirect()->route('admin-portal.patients.delivery-records', $patientId)
            ->with('success', 'Delivery record created successfully.');
    }

    /**
     * Admin delete delivery record
     */
    public function adminDeleteDeliveryRecord($patientId, $recordId)
    {
        $record = DeliveryRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

        $record->delete();

        return redirect()->route('admin-portal.patients.delivery-records', $patientId)
            ->with('success', 'Delivery record deleted successfully.');
    }

    // ========== LAB RESULTS ==========

    /**
     * Admin lab results overview
     */
    public function adminLabResults(Request $request)
    {
        $query = LaboratoryResult::with(['patient', 'orderingProvider']);

        // Filter by patient
        if ($request->has('patient_id') && !is_null($request->patient_id) && $request->patient_id !== '') {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by provider
        if ($request->has('provider_id') && !is_null($request->provider_id) && $request->provider_id !== '') {
            $query->where('ordered_by', $request->provider_id);
        }

        // Filter by status
        if ($request->has('status') && !is_null($request->status) && $request->status !== '') {
            $query->where('test_status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && !is_null($request->date_from) && $request->date_from !== '') {
            $query->where('test_ordered_date_time', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !is_null($request->date_to) && $request->date_to !== '') {
            $query->where('test_ordered_date_time', '<=', $request->date_to);
        }

        $labResults = $query->orderBy('test_ordered_date_time', 'desc')->paginate(20);

        // Get patients and providers for filters
        $patients = User::where('user_type', 'patient')->orderBy('name')->get();
        $providers = User::where('user_type', 'employee')->orderBy('name')->get();

        return view('admin-portal.medical-records.lab-results', compact('labResults', 'patients', 'providers'));
    }

    /**
     * Admin view patient's lab results
     */
    public function adminPatientLabResults($patientId)
    {
        $patient = User::findOrFail($patientId);
        $labResults = LaboratoryResult::where('patient_id', $patientId)
            ->orderBy('test_ordered_date_time', 'desc')
            ->paginate(10);

        return view('admin-portal.patients.lab-results', compact('patient', 'labResults'));
    }

    /**
     * Admin create lab result form
     */
    public function adminCreateLabResult($patientId)
    {
        $patient = User::findOrFail($patientId);
        $providers = User::where('user_type', 'employee')->orderBy('name')->get();

        return view('admin-portal.patients.create-lab-result', compact('patient', 'providers'));
    }

    /**
     * Admin store lab result
     */
    public function adminStoreLabResult(Request $request, $patientId)
    {
        $request->validate([
            'ordered_by' => 'required|exists:users,id',
            'performed_by' => 'nullable|exists:users,id',
            'reviewed_by' => 'nullable|exists:users,id',
            'test_name' => 'required|string|max:255',
            'test_category' => 'required|string|max:100',
            'test_code' => 'nullable|string|max:50',
            'test_description' => 'nullable|string|max:500',
            'sample_type' => 'required|string|max:100',
            'sample_type_other' => 'nullable|string|max:100',
            'sample_collection_date_time' => 'required|date',
            'sample_id' => 'nullable|string|max:100',
            'test_result' => 'nullable|string|max:500',
            'result_value' => 'nullable|string|max:100',
            'result_unit' => 'nullable|string|max:50',
            'reference_range' => 'nullable|string|max:100',
            'result_status' => 'required|string|max:50',
            'test_ordered_date_time' => 'required|date',
            'test_performed_date_time' => 'nullable|date',
            'result_available_date_time' => 'nullable|date',
            'result_reviewed_date_time' => 'nullable|date',
            'clinical_indication' => 'nullable|string|max:500',
            'interpretation' => 'nullable|string|max:1000',
            'comments' => 'nullable|string|max:1000',
            'qc_passed' => 'nullable|boolean',
            'qc_notes' => 'nullable|string|max:500',
            'test_cost' => 'nullable|numeric|min:0',
            'covered_by_philhealth' => 'nullable|boolean',
            'philhealth_coverage_amount' => 'nullable|numeric|min:0',
            'requires_follow_up' => 'nullable|boolean',
            'follow_up_instructions' => 'nullable|string|max:500',
            'follow_up_date' => 'nullable|date',
            'test_status' => 'required|string|max:50',
            'urgent' => 'nullable|boolean',
            'stat' => 'nullable|boolean',
            'rejection_reason' => 'nullable|string|max:500',
            'rejected_date_time' => 'nullable|date',
        ]);

        $labResult = LaboratoryResult::create([
            'patient_id' => $patientId,
            'ordered_by' => $request->ordered_by,
            'performed_by' => $request->performed_by,
            'reviewed_by' => $request->reviewed_by,
            'test_name' => $request->test_name,
            'test_category' => $request->test_category,
            'test_code' => $request->test_code,
            'test_description' => $request->test_description,
            'sample_type' => $request->sample_type,
            'sample_type_other' => $request->sample_type_other,
            'sample_collection_date_time' => $request->sample_collection_date_time,
            'sample_id' => $request->sample_id,
            'test_result' => $request->test_result,
            'result_value' => $request->result_value,
            'result_unit' => $request->result_unit,
            'reference_range' => $request->reference_range,
            'result_status' => $request->result_status,
            'test_ordered_date_time' => $request->test_ordered_date_time,
            'test_performed_date_time' => $request->test_performed_date_time,
            'result_available_date_time' => $request->result_available_date_time,
            'result_reviewed_date_time' => $request->result_reviewed_date_time,
            'clinical_indication' => $request->clinical_indication,
            'interpretation' => $request->interpretation,
            'comments' => $request->comments,
            'qc_passed' => $request->qc_passed,
            'qc_notes' => $request->qc_notes,
            'test_cost' => $request->test_cost,
            'covered_by_philhealth' => $request->covered_by_philhealth,
            'philhealth_coverage_amount' => $request->philhealth_coverage_amount,
            'requires_follow_up' => $request->requires_follow_up,
            'follow_up_instructions' => $request->follow_up_instructions,
            'follow_up_date' => $request->follow_up_date,
            'test_status' => $request->test_status,
            'urgent' => $request->urgent,
            'stat' => $request->stat,
            'rejection_reason' => $request->rejection_reason,
            'rejected_date_time' => $request->rejected_date_time,
        ]);

        // Fire SMS notification event if lab result is completed
        if ($request->result_status === 'completed') {
            event(new \App\Events\LabResultsReady($labResult));
        }

        return redirect()->route('admin-portal.patients.lab-results', $patientId)
            ->with('success', 'Lab result created successfully.');
    }

    /**
     * Admin delete lab result
     */
    public function adminDeleteLabResult($patientId, $resultId)
    {
        $result = LaboratoryResult::where('id', $resultId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

        $result->delete();

        return redirect()->route('admin-portal.patients.lab-results', $patientId)
            ->with('success', 'Lab result deleted successfully.');
    }

    /**
     * Payment management
     */
    public function payments(Request $request)
    {
        $query = \App\Models\Payment::with(['patient', 'appointment.service']);

        // Filter by status
        if ($request->has('status') && !is_null($request->status) && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method') && !is_null($request->payment_method) && $request->payment_method !== '') {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by patient
        if ($request->has('patient_id') && !is_null($request->patient_id) && $request->patient_id !== '') {
            $query->where('patient_id', $request->patient_id);
        }

        // Search by reference or patient name
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get patients for filter dropdown
        $patients = User::where('user_type', 'patient')->orderBy('name')->get();

        // Statistics
        $totalPayments = \App\Models\Payment::count();
        $pendingPayments = \App\Models\Payment::where('status', 'pending')->count();
        $awaitingApprovalPayments = \App\Models\Payment::where('status', 'awaiting_approval')->count();
        $successfulPayments = \App\Models\Payment::where('status', 'successful')->count();
        $totalAmount = \App\Models\Payment::where('status', 'successful')->sum('amount');
        $cancelledPayments = \App\Models\Payment::where('status', 'cancelled')->count();

        return view('admin-portal.payments', compact(
            'payments',
            'patients',
            'totalPayments',
            'pendingPayments',
            'awaitingApprovalPayments',
            'successfulPayments',
            'cancelledPayments',
            'totalAmount'
        ));
    }

    /**
     * Show payment details
     */
    public function showPayment($paymentId)
    {
        // dd($paymentId);
        $payment = Payment::with(['patient', 'appointment.service', 'items.service', 'approver'])
            ->findOrFail($paymentId);

        return view('admin-portal.show-payment', compact('payment'));
    }

    /**
     * Approve payment
     */
    public function approvePayment(Request $request, $paymentId)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $payment = \App\Models\Payment::findOrFail($paymentId);

        try {
            $payment->approve(backpack_user()->id, $request->notes);

            // Fire SMS notification event
            event(new \App\Events\PaymentCompleted($payment));

            return redirect()->back()
                ->with('success', 'Payment approved successfully.');

        } catch (\Exception $e) {
            Log::error('Payment approval error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to approve payment. Please try again.');
        }
    }

    /**
     * Reject payment
     */
    public function rejectPayment(Request $request, $paymentId)
    {
        $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        $payment = \App\Models\Payment::findOrFail($paymentId);

        try {
            $payment->reject($request->notes);

            return redirect()->back()
                ->with('success', 'Payment rejected.');

        } catch (\Exception $e) {
            Log::error('Payment rejection error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to reject payment. Please try again.');
        }
    }

    /**
     * Process partial payment
     */
    public function processPartialPayment(Request $request, $paymentId)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,gcash,paypal,card',
            'notes' => 'nullable|string|max:500',
        ]);

        $payment = \App\Models\Payment::findOrFail($paymentId);

        try {
            $payment->addPartialPayment(
                $request->paid_amount,
                $request->payment_method,
                $request->notes
            );

            return redirect()->back()
                ->with('success', 'Partial payment processed successfully.');

        } catch (\Exception $e) {
            Log::error('Partial payment processing error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to process partial payment. Please try again.');
        }
    }

    /**
     * Mandatory deductions management
     */
    public function mandatoryDeductions(Request $request)
    {
        // Get mandatory deductions with filters
        $query = \App\Models\MandatoryDeduction::query();

        // Filter by deduction type
        if ($request->has('deduction_type') && !is_null($request->deduction_type) && $request->deduction_type !== '') {
            $query->where('deduction_type', $request->deduction_type);
        }

        // Filter by status
        if ($request->has('status') && !is_null($request->status) && $request->status !== '') {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search by name or description
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('deduction_type', 'like', "%{$search}%");
            });
        }

        $deductions = $query->orderBy('deduction_type')->paginate(20);

        // Calculate statistics
        $totalDeductions = \App\Models\MandatoryDeduction::count();
        $activeDeductions = \App\Models\MandatoryDeduction::where('is_active', true)->count();
        $inactiveDeductions = \App\Models\MandatoryDeduction::where('is_active', false)->count();

        // Get deduction types for filter dropdown
        $deductionTypes = \App\Models\MandatoryDeduction::getPhilippineDeductionTypes();

        return view('admin-portal.mandatory-deductions', compact(
            'deductions',
            'totalDeductions',
            'activeDeductions',
            'inactiveDeductions',
            'deductionTypes'
        ));
    }

    /**
     * Show create mandatory deduction form
     */
    public function createMandatoryDeduction()
    {
        $deductionTypes = \App\Models\MandatoryDeduction::getPhilippineDeductionTypes();
        $employees = \App\Models\User::where('user_type', 'employee')
            ->with('employeeProfile')
            ->orderBy('name')
            ->get(['id', 'name']);
        $positions = User::where('user_type', 'employee')
            ->join('employee_profiles', 'employee_profiles.employee_id', '=', 'users.id')
            ->whereNotNull('employee_profiles.position')
            ->distinct()
            ->orderBy('employee_profiles.position')
            ->pluck('employee_profiles.position')
            ->toArray();
        
        return view('admin-portal.create-mandatory-deduction', compact(
            'deductionTypes', 
            'employees',
            'positions'
        ));
    }

    /**
     * Store new mandatory deduction
     */
    public function storeMandatoryDeduction(Request $request)
    {
        $request->validate([
            'deduction_type' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'percentage_rate' => 'nullable|numeric|min:0|max:100',
            'fixed_amount' => 'nullable|numeric|min:0',
            'minimum_base_salary' => 'nullable|numeric|min:0',
            'maximum_deduction' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'effective_date' => 'nullable|date',
            'notes' => 'nullable|string',
            // Selection validation
            'selection_mode' => 'required|in:individual,position',
            'employee_ids' => 'required_if:selection_mode,individual|array',
            'employee_ids.*' => 'exists:users,id',
            'positions' => 'required_if:selection_mode,position|array',
        ], [], [
            'selection_mode' => 'Selection method',
            'employee_ids' => 'Employees',
            'positions' => 'Positions'
        ]);
        
        // Validate that at least one selection method has values
        if ($request->selection_mode === 'individual' && empty($request->employee_ids)) {
            $request->merge(['employee_ids' => []]); // Will trigger validation error
        }
        if ($request->selection_mode === 'position' && empty($request->positions)) {
            $request->merge(['positions' => []]); // Will trigger validation error
        }
        
        // Create the mandatory deduction
        $deduction = \App\Models\MandatoryDeduction::create([
            'deduction_type' => $request->deduction_type,
            'name' => $request->name,
            'description' => $request->description,
            'percentage_rate' => $request->percentage_rate ?: 0,
            'fixed_amount' => $request->fixed_amount ?: 0,
            'minimum_base_salary' => $request->minimum_base_salary ?: 0,
            'maximum_deduction' => $request->maximum_deduction,
            'is_active' => $request->boolean('is_active'),
            'effective_date' => $request->effective_date,
            'notes' => $request->notes,
        ]);
        
        // Get employees to apply deduction to
        $employeeIds = [];
        
        if ($request->selection_mode === 'individual') {
            $employeeIds = $request->employee_ids;
        } elseif ($request->selection_mode === 'position') {
            // Get all employees with selected positions
            $employees = \App\Models\User::where('user_type', 'employee')
                ->whereHas('employeeProfile', function ($query) use ($request) {
                    $query->whereIn('position', $request->positions);
                })
                ->pluck('id')
                ->toArray();
            
            $employeeIds = $employees;
        }
        
        // Create employee deduction records
        foreach ($employeeIds as $employeeId) {
            \App\Models\EmployeeDeduction::firstOrCreate(
                ['employee_id' => $employeeId, 'deduction_id' => $deduction->id],
                [
                    'is_enabled' => true,
                    'custom_percentage_rate' => null,
                    'custom_fixed_amount' => null,
                    'notes' => $request->selection_mode === 'position' 
                        ? 'Applied via position selection: ' . implode(', ', $request->positions)
                        : 'Applied via individual selection'
                ]
            );
        }
        
        ActivityLogger::logCreated($deduction, "Created new mandatory deduction {$request->name}");
        
        return redirect()->route('admin-portal.mandatory-deductions')
            ->with('success', 'Mandatory deduction created successfully and applied to ' . count($employeeIds) . ' employee(s).');
    }

    /**
     * Show mandatory deduction details
     */
    public function showMandatoryDeduction($deductionId)
    {
        $deduction = \App\Models\MandatoryDeduction::with('employeeDeductions.employee')->findOrFail($deductionId);

        // Get employee deductions for this mandatory deduction
        $employeeDeductions = $deduction->employeeDeductions()->with('employee')->paginate(20);

        // Calculate statistics
        $totalEmployees = $employeeDeductions->total();
        $enabledEmployees = $employeeDeductions->where('is_enabled', true)->count();
        $disabledEmployees = $employeeDeductions->where('is_enabled', false)->count();

        return view('admin-portal.show-mandatory-deduction', compact(
            'deduction',
            'employeeDeductions',
            'totalEmployees',
            'enabledEmployees',
            'disabledEmployees'
        ));
    }

    /**
     * Show edit mandatory deduction form
     */
    public function editMandatoryDeduction($deductionId)
    {
        $deduction = \App\Models\MandatoryDeduction::findOrFail($deductionId);
        $deductionTypes = \App\Models\MandatoryDeduction::getPhilippineDeductionTypes();

        return view('admin-portal.edit-mandatory-deduction', compact('deduction', 'deductionTypes'));
    }

    /**
     * Update mandatory deduction
     */
    public function updateMandatoryDeduction(Request $request, $deductionId)
    {
        $deduction = \App\Models\MandatoryDeduction::findOrFail($deductionId);

        $request->validate([
            'deduction_type' => 'required|string|max:255|unique:mandatory_deductions,deduction_type,' . $deductionId,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'percentage_rate' => 'nullable|numeric|min:0|max:100',
            'fixed_amount' => 'nullable|numeric|min:0',
            'minimum_base_salary' => 'nullable|numeric|min:0',
            'maximum_deduction' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'effective_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $deduction->update([
            'deduction_type' => $request->deduction_type,
            'name' => $request->name,
            'description' => $request->description,
            'percentage_rate' => $request->percentage_rate ?: 0,
            'fixed_amount' => $request->fixed_amount ?: 0,
            'minimum_base_salary' => $request->minimum_base_salary ?: 0,
            'maximum_deduction' => $request->maximum_deduction,
            'is_active' => $request->boolean('is_active'),
            'effective_date' => $request->effective_date,
            'notes' => $request->notes,
        ]);

        ActivityLogger::logUpdated($deduction, null, "Updated mandatory deduction {$deduction->name}");

        return redirect()->route('admin-portal.mandatory-deductions.show', $deduction->id)
            ->with('success', 'Mandatory deduction updated successfully.');
    }

    /**
     * Delete mandatory deduction
     */
    public function deleteMandatoryDeduction($deductionId)
    {
        $deduction = \App\Models\MandatoryDeduction::findOrFail($deductionId);

        // Check if deduction has employee associations
        if ($deduction->employeeDeductions()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete mandatory deduction that has employee associations.');
        }

        $deductionName = $deduction->name;
        $deduction->delete();

        ActivityLogger::logDeleted($deduction, "Deleted mandatory deduction {$deductionName}");

        return redirect()->route('admin-portal.mandatory-deductions')->with('success', 'Mandatory deduction deleted successfully.');
    }

    /**
     * Show manage employee deductions page
     */
    public function manageEmployeeDeductions($employeeId)
    {
        $employee = User::where('user_type', 'employee')->findOrFail($employeeId);

        // Get all active mandatory deductions
        $mandatoryDeductions = \App\Models\MandatoryDeduction::active()->get();

        // Get employee's current deductions
        $employeeDeductions = $employee->employeeDeductions()->get()->keyBy('deduction_id');

        // Calculate statistics
        $totalDeductions = $mandatoryDeductions->count();
        $enabledDeductions = $employeeDeductions->where('is_enabled', true)->count();
        $disabledDeductions = $employeeDeductions->where('is_enabled', false)->count();

        return view('admin-portal.manage-employee-deductions', compact(
            'employee',
            'mandatoryDeductions',
            'employeeDeductions',
            'totalDeductions',
            'enabledDeductions',
            'disabledDeductions'
        ));
    }

    /**
     * Update all deductions for an employee
     */
    public function updateEmployeeDeductions(Request $request, $employeeId)
    {
        $employee = User::where('user_type', 'employee')->findOrFail($employeeId);

        $request->validate([
            'deductions' => 'required|array',
            'deductions.*.deduction_id' => 'required|exists:mandatory_deductions,id',
            'deductions.*.enabled' => 'sometimes|accepted',
            'deductions.*.custom_percentage_rate' => 'nullable|numeric|min:0|max:100',
            'deductions.*.custom_fixed_amount' => 'nullable|numeric|min:0',
            'deductions.*.notes' => 'nullable|string',
        ]);

        $updatedDeductions = [];
        $createdDeductions = [];
        $disabledDeductions = [];

        foreach ($request->deductions as $deductionData) {
            $deductionId = $deductionData['deduction_id'];
            $existingEmployeeDeduction = \App\Models\EmployeeDeduction::where('employee_id', $employeeId)
                ->where('deduction_id', $deductionId)
                ->first();

            if (!isset($deductionData['enabled']) || $deductionData['enabled'] != '1') {
                // Disable or remove deduction
                if ($existingEmployeeDeduction) {
                    $existingEmployeeDeduction->update(['is_enabled' => false]);
                    $disabledDeductions[] = $existingEmployeeDeduction->deduction->name;
                }
                continue;
            }

            // Prepare data for update/create
            $updateData = [
                'is_enabled' => true,
                'custom_percentage_rate' => $deductionData['custom_percentage_rate'] ?? null,
                'custom_fixed_amount' => $deductionData['custom_fixed_amount'] ?? null,
                'notes' => $deductionData['notes'] ?? null,
            ];

            if ($existingEmployeeDeduction) {
                // Update existing
                $existingEmployeeDeduction->update($updateData);
                $updatedDeductions[] = $existingEmployeeDeduction->deduction->name;
            } else {
                // Create new
                $newEmployeeDeduction = \App\Models\EmployeeDeduction::create([
                    'employee_id' => $employeeId,
                    'deduction_id' => $deductionId,
                    ...$updateData
                ]);
                $createdDeductions[] = $newEmployeeDeduction->deduction->name;
            }
        }

        // Log activity
        $totalChanges = count($createdDeductions) + count($updatedDeductions) + count($disabledDeductions);
        if ($totalChanges > 0) {
            ActivityLogger::log('deductions_updated', "Updated deductions for {$employee->name}: {$totalChanges} changes", $employee);
        }

        // Prepare success message
        $message = 'Employee deductions updated successfully. ';
        if (!empty($createdDeductions)) {
            $message .= count($createdDeductions) . ' created, ';
        }
        if (!empty($updatedDeductions)) {
            $message .= count($updatedDeductions) . ' updated, ';
        }
        if (!empty($disabledDeductions)) {
            $message .= count($disabledDeductions) . ' disabled.';
        }

        return redirect()->route('admin-portal.employee-deductions.manage', $employeeId)->with('success', rtrim($message, ', '));
    }

    /**
     * Show employee deductions list for bulk management
     */
    public function employeeDeductions(Request $request)
    {
        // Get employees with filters
        $query = User::where('user_type', 'employee')
            ->with('employeeProfile')
            ->withCount(['employeeDeductions as deductions_count']);

        // Search by name or email
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by position
        if ($request->has('position') && $request->position !== '') {
            $query->whereHas('employeeProfile', function ($q) use ($request) {
                $q->where('position', $request->position);
            });
        }

        // Filter by deduction status
        if ($request->has('deduction_status') && $request->deduction_status !== '') {
            if ($request->deduction_status === 'with_deductions') {
                $query->having('deductions_count', '>', 0);
            } elseif ($request->deduction_status === 'without_deductions') {
                $query->having('deductions_count', '=', 0);
            }
        }

        $employees = $query->orderBy('name')->paginate(20);

        // Calculate statistics
        $totalEmployees = $employees->total();
        $employeesWithDeductions = User::where('user_type', 'employee')->has('employeeDeductions')->count();
        $employeesWithoutDeductions = User::where('user_type', 'employee')->doesntHave('employeeDeductions')->count();

        return view('admin-portal.employee-deductions', compact(
            'employees',
            'totalEmployees',
            'employeesWithDeductions',
            'employeesWithoutDeductions'
        ));
    }

    /**
     * Bulk update deductions for multiple employees
     */
    public function bulkUpdateEmployeeDeductions(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:users,id',
            'deduction_id' => 'required|exists:mandatory_deductions,id',
            'action' => 'required|in:enable,disable,update_rate,update_amount',
            'custom_percentage_rate' => 'nullable|numeric|min:0|max:100',
            'custom_fixed_amount' => 'nullable|numeric|min:0',
        ]);

        $deduction = \App\Models\MandatoryDeduction::findOrFail($request->deduction_id);
        $updatedCount = 0;

        foreach ($request->employee_ids as $employeeId) {
            $employeeDeduction = \App\Models\EmployeeDeduction::firstOrCreate(
                ['employee_id' => $employeeId, 'deduction_id' => $deduction->id],
                ['is_enabled' => false]
            );

            $updateData = [];

            switch ($request->action) {
                case 'enable':
                    $updateData['is_enabled'] = true;
                    break;
                case 'disable':
                    $updateData['is_enabled'] = false;
                    break;
                case 'update_rate':
                    if ($request->filled('custom_percentage_rate')) {
                        $updateData['custom_percentage_rate'] = $request->custom_percentage_rate;
                        $updateData['is_enabled'] = true;
                    }
                    break;
                case 'update_amount':
                    if ($request->filled('custom_fixed_amount')) {
                        $updateData['custom_fixed_amount'] = $request->custom_fixed_amount;
                        $updateData['is_enabled'] = true;
                    }
                    break;
            }

            if (!empty($updateData)) {
                $employeeDeduction->update($updateData);
                $updatedCount++;
            }
        }

        ActivityLogger::log('bulk_deductions_updated', "Bulk updated deductions for {$updatedCount} employees - Action: {$request->action}", null);

        return redirect()->back()->with('success', "Successfully updated deductions for {$updatedCount} employees.");
    }

    /**
     * Generate payroll for employees
     * Updated to use dynamic deduction system
     */
    public function generatePayroll(Request $request)
    {
        $request->validate([
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after:pay_period_start',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:users,id',
            'overtime_rate' => 'required|numeric|min:0',
        ]);

        $payPeriodStart = Carbon::parse($request->pay_period_start);
        $payPeriodEnd = Carbon::parse($request->pay_period_end);

        $generatedPayrolls = [];
        $skippedEmployees = [];

        foreach ($request->employee_ids as $employeeId) {
            // Check if payroll already exists for this period
            $existingPayroll = Payroll::where('employee_id', $employeeId)
                ->where('pay_period_start', $payPeriodStart)
                ->where('pay_period_end', $payPeriodEnd)
                ->first();

            if ($existingPayroll) {
                $skippedEmployees[] = ['id' => $employeeId, 'reason' => 'Payroll already exists for this period'];
                continue; // Skip if already exists
            }

            // Get hourly rate from employee profile
            $employeeProfile = \App\Models\EmployeeProfile::where('employee_id', $employeeId)->first();
            $hourlyRate = $employeeProfile ? $employeeProfile->hourly_rate : 0;

            if ($hourlyRate <= 0) {
                $employee = User::find($employeeId);
                $employeeName = $employee ? $employee->name : 'Unknown Employee';
                $skippedEmployees[] = ['id' => $employeeId, 'name' => $employeeName, 'reason' => 'No hourly rate set'];
                continue; // Skip if no hourly rate set
            }

            // Check if employee has deduction settings configured
            $employeeDeductions = \App\Models\EmployeeDeduction::where('employee_id', $employeeId)->enabled()->count();
            if ($employeeDeductions == 0) {
                $employee = User::find($employeeId);
                $employeeName = $employee ? $employee->name : 'Unknown Employee';
                $skippedEmployees[] = ['id' => $employeeId, 'name' => $employeeName, 'reason' => 'No deduction settings configured'];
                continue; // Skip if no deduction settings
            }

            $payroll = new Payroll([
                'employee_id' => $employeeId,
                'pay_period_start' => $payPeriodStart,
                'pay_period_end' => $payPeriodEnd,
                'hourly_rate' => $hourlyRate,
                'overtime_rate' => $request->overtime_rate,
            ]);

            $payroll->calculatePay();
            $generatedPayrolls[] = $payroll;

            ActivityLogger::log('payroll_generated', "Payroll generated for {$payroll->employee->name}", $payroll->employee);
        }

        $message = 'Payroll generated for ' . count($generatedPayrolls) . ' employees.';

        if (!empty($skippedEmployees)) {
            $message .= ' Skipped ' . count($skippedEmployees) . ' employees: ';
            $skipReasons = [];
            foreach ($skippedEmployees as $skipped) {
                $name = $skipped['name'] ?? 'Employee #' . $skipped['id'];
                $skipReasons[] = $name . ' (' . $skipped['reason'] . ')';
            }
            $message .= implode(', ', $skipReasons);
        }

        return redirect()->route('admin-portal.payroll')->with('success', $message);
    }
}
