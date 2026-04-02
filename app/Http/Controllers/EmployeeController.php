<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\Treatment;
use App\Models\User;
use App\Models\EmployeeProfile;
use App\Models\EmployeeSchedule;
use App\Models\EmployeeAttendance;
use App\Models\PatientProfile;
use App\Models\Appointment;
use App\Models\PrenatalRecord;
use App\Models\LaboratoryResult;
use App\Models\PostnatalRecord;
use App\Models\PostpartumRecord;
use App\Models\DeliveryRecord;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Payroll;
use App\Models\Service;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    // Middleware is now handled at route level with 'auth:backpack' and 'employee'

    /**
     * Show the employee dashboard
     */
    public function dashboard()
    {
        $user = backpack_user();
        $employeeProfile = $user->employeeProfile;
        $todaySchedule = $this->getTodaySchedule($user);
        $recentAttendance = $this->getRecentAttendance($user);

        // Get upcoming appointments for the employee (next 7 days)
        $upcomingAppointments = Appointment::where('employee_id', $user->id)
            ->where('appointment_datetime', '>', now())
            ->where('appointment_datetime', '<=', now()->addDays(7))
            ->where('status', '!=', 'cancelled')
            ->with(['patient', 'service'])
            ->orderBy('appointment_datetime', 'asc')
            ->limit(5)
            ->get();

        return view('employee.dashboard', compact('user', 'employeeProfile', 'todaySchedule', 'recentAttendance', 'upcomingAppointments'));
    }

    /**
     * Show employee profile
     */
    public function profile()
    {
        $user = backpack_user();
        $employeeProfile = $user->employeeProfile;

        return view('employee.profile', compact('user', 'employeeProfile'));
    }

    /**
     * Update employee profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . backpack_user()->id,
            'phone' => 'nullable|philippine_phone',
            'address' => 'nullable|string|max:500',
            'user_pin' => 'nullable|numeric|digits:6|confirmed|unique:users,pin,' . backpack_user()->id,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = backpack_user();
        $employeeProfile = $user->employeeProfile;

        // Handle profile image upload
        $imagePath = null;
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($employeeProfile && $employeeProfile->image_path) {
                \Storage::disk('public')->delete($employeeProfile->image_path);
            }

            // Store new image
            $imagePath = $request->file('profile_image')->store('employee-profiles', 'public');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Handle user PIN update
        if ($request->filled('user_pin')) {
            $user->setPin($request->user_pin);
        }

        if ($employeeProfile) {
            $profileData = [
                'phone' => $request->phone,
                'address' => $request->address,
                'position' => $request->position,
            ];

            // Only update image_path if a new image was uploaded
            if ($imagePath) {
                $profileData['image_path'] = $imagePath;
            }

            $employeeProfile->update($profileData);
        }

        return redirect()->route('employee.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Show employee schedule
     */
    public function schedule()
    {
        $user = backpack_user();
        $schedules = $user->schedules()->orderBy('day_of_week')->get();

        return view('employee.schedule', compact('schedules'));
    }

    /**
     * Show attendance records (view-only for employees)
     */
    public function attendance()
    {
        $user = backpack_user();

        $userAttendance = EmployeeAttendance::where('employee_id', $user->id);

        \Log::info("Employee attendance - User ID: " . $user->id . ", User Type: " . $user->user_type);

        if (!$userAttendance) {
            \Log::info("Employee attendance - No employee profile found for user " . $user->id);
            return redirect()->route('employee.dashboard')->with('error', 'Employee profile not found.');
        }

        $attendanceRecords = $userAttendance->orderBy('date', 'desc')->paginate(20);

        \Log::info("Employee attendance - Records found: " . $attendanceRecords->count());

        // Calculate attendance statistics
        $allAttendance = $userAttendance->get();

        $totalDays = $allAttendance->count();
        $presentDays = $allAttendance->whereNotNull('clock_in')->count();
        $lateDays = $allAttendance->filter(function ($record) {
            if (!$record->clock_in) return false;
            $scheduledTime = '08:00:00'; // Default 8 AM
            return strtotime($record->clock_in) > strtotime($scheduledTime);
        })->count();

        // Calculate total hours worked
        $totalHours = 0;
        foreach ($allAttendance as $record) {
            if ($record->clock_in && $record->clock_out) {
                $start = \Carbon\Carbon::parse($record->clock_in);
                $end = \Carbon\Carbon::parse($record->clock_out);
                $totalHours += $start->diffInHours($end);
            }
        }

        return view('employee.attendance', compact(
            'attendanceRecords',
            'totalDays',
            'presentDays',
            'lateDays',
            'totalHours'
        ));
    }

    /**
     * Show employee's payroll records
     */
    public function payroll()
    {
        $user = backpack_user();

        $payrollRecords = Payroll::where('employee_id', $user->id)
            ->orderBy('pay_period_end', 'desc')
            ->paginate(20);

        // Calculate payroll statistics
        $totalPayrolls = $payrollRecords->total();
        $paidPayrolls = $payrollRecords->where('status', 'paid')->count();
        $pendingPayrolls = $payrollRecords->where('status', 'pending')->count();
        $processedPayrolls = $payrollRecords->where('status', 'processed')->count();
        $totalEarnings = $payrollRecords->where('status', 'paid')->sum('net_pay');

        return view('employee.payroll', compact(
            'payrollRecords',
            'totalPayrolls',
            'paidPayrolls',
            'pendingPayrolls',
            'processedPayrolls',
            'totalEarnings'
        ));
    }

    /**
     * Show employee's appointments with filtering
     */
    public function appointments(Request $request)
    {
        $user = backpack_user();

        // Start building the query
        $query = Appointment::where('employee_id', $user->id)
            ->with(['patient', 'service', 'payments']);

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply date range filters
        if ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case 'today':
                    $query->whereDate('appointment_datetime', today());
                    break;
                case 'tomorrow':
                    $query->whereDate('appointment_datetime', today()->addDay());
                    break;
                case 'this_week':
                    $query->whereBetween('appointment_datetime', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'next_week':
                    $query->whereBetween('appointment_datetime', [now()->addWeek()->startOfWeek(), now()->addWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('appointment_datetime', now()->month)
                          ->whereYear('appointment_datetime', now()->year);
                    break;
                case 'custom':
                    if ($request->filled('start_date') && $request->filled('end_date')) {
                        $query->whereBetween('appointment_datetime', [$request->start_date, $request->end_date]);
                    }
                    break;
            }
        }

        // Apply payment status filter
        if ($request->filled('payment_status')) {
            switch ($request->payment_status) {
                case 'paid':
                    $query->whereHas('payments', function($q) {
                        $q->where('status', 'completed');
                    });
                    break;
                case 'pending':
                    $query->whereHas('payments', function($q) {
                        $q->where('status', 'pending');
                    });
                    break;
                case 'unpaid':
                    $query->whereDoesntHave('payments', function($q) {
                        $q->where('status', 'completed');
                    });
                    break;
            }
        }

        // Apply service type filter
        if ($request->filled('service_type')) {
            $query->whereHas('service', function($q) use ($request) {
                $q->where('service_type', $request->service_type);
            });
        }

        // Apply search filter (patient name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'appointment_datetime');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortBy, ['appointment_datetime', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('appointment_datetime', 'desc');
        }

        // Get the filtered appointments with pagination
        $appointments = $query->paginate(20);

        // Calculate statistics based on the same filters but without pagination
        $statsQuery = clone $query;
        $statsQuery->getQuery()->orders = null; // Remove ordering for stats
        $filteredAppointments = $statsQuery->get();

        // Separate upcoming and past appointments from filtered results
        $upcomingAppointments = $filteredAppointments->filter(function ($appointment) {
            return $appointment->appointment_datetime > now();
        });

        $pastAppointments = $filteredAppointments->filter(function ($appointment) {
            return $appointment->appointment_datetime <= now();
        });

        // Calculate appointment statistics from filtered results
        $totalAppointments = $filteredAppointments->count();
        $upcomingCount = $upcomingAppointments->count();
        $completedCount = $pastAppointments->where('status', 'completed')->count();
        $cancelledCount = $filteredAppointments->where('status', 'cancelled')->count();

        // Get available service types for filter dropdown
        $serviceTypes = Service::distinct()->pluck('service_type')->filter()->values();

        return view('employee.appointments', compact(
            'appointments',
            'upcomingAppointments',
            'pastAppointments',
            'totalAppointments',
            'upcomingCount',
            'completedCount',
            'cancelledCount',
            'serviceTypes'
        ));
    }

    /**
     * Get today's schedule for the employee
     */
    private function getTodaySchedule(User $user)
    {
        $dayOfWeek = now()->dayOfWeekIso; // 1 = Monday, 7 = Sunday
        return $user->schedules()->where('day_of_week', $dayOfWeek)->first();
    }

    /**
     * Get recent attendance records
     */
    private function getRecentAttendance(User $user, $limit = 5)
    {
        if (!$user->employeeProfile) {
            return collect(); // Return empty collection if no employee profile
        }

        return $user->employeeProfile->attendance()
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get();
    }

    // ===== PATIENT MANAGEMENT METHODS =====

    /**
     * Show list of all patients
     */
    public function patients(Request $request)
    {
        $query = User::where('user_type', 'patient')
            ->with('patientProfile');

        // Search by patient name, email, or phone
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('patientProfile', function($profileQuery) use ($search) {
                      $profileQuery->where('phone', 'like', "%{$search}%");
                  });
            });
        }

        $patients = $query->orderBy('name')->paginate(20);

        // Ensure all patients have profiles
        foreach ($patients as $patient) {
            if (!$patient->patientProfile) {
                $patient->patientProfile()->create([
                    'phone' => 'Not provided',
                    'birth_date' => null,
                    'gender' => 'male', // Default value since column is not nullable
                    'civil_status' => null,
                    'address' => 'Not provided',
                    'emergency_contact_name' => 'Not provided',
                    'emergency_contact_phone' => 'Not provided',
                    'emergency_contact_relationship' => 'Not provided',
                ]);
            }
        }

        // Reload with profiles
        $patients->load('patientProfile');

        return view('employee.patients.index', compact('patients'));
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

        // Ensure patient has a profile, create one if missing
        if (!$patient->patientProfile) {
            $patient->patientProfile()->create([
                'phone' => 'Not provided',
                'birth_date' => now()->subYears(25)->toDateString(),
                'gender' => 'male', // Default value since column is not nullable
                'civil_status' => null,
                'address' => 'Not provided',
                'emergency_contact_name' => 'Not provided',
                'emergency_contact_phone' => 'Not provided',
                'emergency_contact_relationship' => 'Not provided',
            ]);
            // Reload the patient with the new profile
            $patient->load('patientProfile');
        }

        // Get patient's appointments
        $appointments = Appointment::where('patient_id', $patient->id)
            ->with(['service', 'employee'])
            ->orderBy('appointment_datetime', 'desc')
            ->paginate(10);

        // Get patient's medical records
        $prenatalRecords = \App\Models\PrenatalRecord::where('patient_id', $patient->id)
            ->with('attendingPhysician')
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

        $treatments = \App\Models\Treatment::where('patient_id', $patient->id)
            ->with(['prescriber', 'prescriptions.inventory'])
            ->orderBy('prescribed_date', 'desc')
            ->limit(5)
            ->get();

        return view('employee.patients.show', compact(
            'patient',
            'appointments',
            'prenatalRecords',
            'postnatalRecords',
            'postpartumRecords',
            'deliveryRecords',
            'labResults',
            'treatments'
        ));
    }

    /**
     * Show create patient form
     */
    public function createPatient()
    {
        return view('employee.patients.create');
    }

    /**
     * Store new patient
     */
    public function storePatient(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|philippine_phone',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'civil_status' => 'nullable|in:single,married,widowed,separated',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|philippine_phone',
            'emergency_contact_relationship' => 'nullable|string|max:100',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'patient',
            'status' => 'prescribed',
        ]);

        // Create patient profile
        $user->patientProfile()->create([
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'civil_status' => $request->civil_status,
            'address' => $request->address,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
        ]);

        return redirect()->route('employee.patients')->with('success', 'Patient created successfully.');
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

        // Ensure patient has a profile, create one if missing
        if (!$patient->patientProfile) {
            $patient->patientProfile()->create([
                'phone' => 'Not provided',
                'birth_date' => now()->subYears(25)->toDateString(),
                'gender' => 'male', // Default value since column is not nullable
                'civil_status' => null,
                'address' => 'Not provided',
                'emergency_contact_name' => 'Not provided',
                'emergency_contact_phone' => 'Not provided',
                'emergency_contact_relationship' => 'Not provided',
            ]);
            // Reload the patient with the new profile
            $patient->load('patientProfile');
        }

        return view('employee.patients.edit', compact('patient'));
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $patient->id,
            'phone' => 'nullable|philippine_phone',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'civil_status' => 'nullable|in:single,married,widowed,separated',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|philippine_phone',
            'emergency_contact_relationship' => 'nullable|string|max:100',
        ]);

        // Update user
        $patient->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Prepare profile data, filtering out null values for required fields
        $profileData = array_filter([
            'phone' => $request->phone,
            'gender' => $request->gender,
            'civil_status' => $request->civil_status,
            'address' => $request->address,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
        ], function($value) {
            return $value !== null && $value !== '';
        });

        // Handle birth_date separately - only update if provided
        if ($request->filled('birth_date')) {
            $profileData['birth_date'] = $request->birth_date;
        }

        // Ensure patient has a profile, create one if missing
        if (!$patient->patientProfile) {
            // For new profiles, provide defaults for required fields
            $defaultProfileData = [
                'phone' => $request->phone,
                'birth_date' => $request->birth_date ?: now()->subYears(25)->toDateString(), // Default 25 years ago
                'gender' => $request->gender ?: 'male', // Default gender
                'civil_status' => $request->civil_status,
                'address' => $request->address,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
            ];
            $patient->patientProfile()->create($defaultProfileData);
        } else {
            // Update existing patient profile - only update provided fields
            if (!empty($profileData)) {
                $patient->patientProfile->update($profileData);
            }
        }

        return redirect()->route('employee.patients.show', $patient->id)
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

        return redirect()->route('employee.patients')->with('success', 'Patient deleted successfully.');
    }

    /**
     * Show schedule appointment form for a patient
     */
    public function scheduleAppointment($patientId)
    {
        $patient = User::where('user_type', 'patient')
            ->where('id', $patientId)
            ->with('patientProfile')
            ->firstOrFail();

        $services = \App\Models\Service::all();
        $employees = User::where('user_type', 'employee')->get();

        return view('employee.patients.schedule-appointment', compact('patient', 'services', 'employees'));
    }

    /**
     * Store scheduled appointment for a patient
     */
    public function storeScheduledAppointment(Request $request, $patientId)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'employee_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required|date_format:H:i',
            'duration_in_minutes' => 'required|integer|min:15|max:480',
            'notes' => 'nullable|string|max:1000',
        ]);

        $patient = User::where('user_type', 'patient')
            ->where('id', $patientId)
            ->firstOrFail();

        // Combine date and time
        $appointmentDateTime = $request->appointment_date . ' ' . $request->appointment_time . ':00';

        // Check for scheduling conflicts
        $conflictingAppointment = Appointment::where('employee_id', $request->employee_id)
            ->where('appointment_datetime', $appointmentDateTime)
            ->first();

        if ($conflictingAppointment) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'The selected employee is not available at this time. Please choose a different time or employee.');
        }

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'service_id' => $request->service_id,
            'employee_id' => $request->employee_id,
            'appointment_datetime' => $appointmentDateTime,
            'duration_in_minutes' => $request->duration_in_minutes,
            'status' => 'scheduled',
            'notes' => $request->notes,
        ]);

        // Fire SMS notification event for appointment reminder
        event(new \App\Events\AppointmentReminder($appointment));

        return redirect()->route('employee.patients.show', $patient->id)
            ->with('success', 'Appointment scheduled successfully for ' . $patient->name);
    }

    /**
     * Show patient's appointments
     */
    public function patientAppointments($patientId)
    {
        $patient = User::where('user_type', 'patient')
            ->where('id', $patientId)
            ->firstOrFail();

        // Get all appointments for the patient
        $allAppointments = Appointment::where('patient_id', $patient->id)
            ->with(['service', 'employee'])
            ->orderBy('appointment_datetime', 'desc')
            ->get();

        // Separate upcoming and past appointments
        $upcomingAppointments = $allAppointments->filter(function ($appointment) {
            return $appointment->appointment_datetime > now();
        });

        $pastAppointments = $allAppointments->filter(function ($appointment) {
            return $appointment->appointment_datetime <= now();
        });

        // Paginate based on request parameter
        $filter = request('filter', 'all');
        switch ($filter) {
            case 'upcoming':
                $appointments = $upcomingAppointments->sortBy('appointment_datetime');
                break;
            case 'past':
                $appointments = $pastAppointments->sortByDesc('appointment_datetime');
                break;
            default:
                $appointments = $allAppointments->sortByDesc('appointment_datetime');
        }

        $appointments = new \Illuminate\Pagination\LengthAwarePaginator(
            $appointments->forPage(request('page', 1), 15),
            $appointments->count(),
            15,
            request('page', 1),
            ['path' => request()->url(), 'pageName' => 'page']
        );

        // Calculate appointment statistics
        $totalAppointments = $allAppointments->count();
        $upcomingCount = $upcomingAppointments->count();
        $completedCount = $pastAppointments->where('status', 'completed')->count();
        $cancelledCount = $pastAppointments->where('status', 'cancelled')->count();
        $scheduledCount = $allAppointments->where('status', 'scheduled')->count();

        return view('employee.patients.appointments', compact(
            'patient',
            'appointments',
            'totalAppointments',
            'upcomingCount',
            'completedCount',
            'cancelledCount',
            'scheduledCount',
            'filter'
        ));
    }

    /**
     * Get appointment details for AJAX
     */
    public function getAppointmentDetails($patientId, $appointmentId)
    {
        $patient = User::where('user_type', 'patient')
            ->where('id', $patientId)
            ->firstOrFail();

        $appointment = Appointment::where('id', $appointmentId)
            ->where('patient_id', $patient->id)
            ->with(['service', 'employee'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'appointment' => [
                'id' => $appointment->id,
                'appointment_datetime' => $appointment->appointment_datetime ? $appointment->appointment_datetime->format('M d, Y h:i A') : 'N/A',
                'duration_in_minutes' => $appointment->duration_in_minutes ?? 0,
                'status' => ucfirst($appointment->status),
                'notes' => $appointment->notes ?? 'No notes',
                'service' => $appointment->service ? [
                    'name' => $appointment->service->name,
                    'type' => $appointment->service->service_type ?? 'N/A',
                    'description' => $appointment->service->description ?? 'No description'
                ] : null,
                'employee' => $appointment->employee ? [
                    'name' => $appointment->employee->name,
                    'email' => $appointment->employee->email,
                    'phone' => $appointment->employee->employeeProfile->phone ?? 'N/A'
                ] : null,
                'patient' => [
                    'name' => $patient->name,
                    'email' => $patient->email,
                    'phone' => $patient->patientProfile->phone ?? 'N/A',
                    'birth_date' => $patient->patientProfile->birth_date ? \Carbon\Carbon::parse($patient->patientProfile->birth_date)->format('M d, Y') : 'N/A'
                ]
            ]
        ]);
    }

    /**
     * Update appointment status
     */
    public function updateAppointmentStatus(Request $request, $patientId, $appointmentId)
    {
        $request->validate([
            'status' => 'required|in:scheduled,confirmed,completed,cancelled',
        ]);

        $patient = User::where('user_type', 'patient')
            ->where('id', $patientId)
            ->firstOrFail();

        $appointment = Appointment::where('id', $appointmentId)
            ->where('patient_id', $patient->id)
            ->firstOrFail();

        $oldStatus = $appointment->status;

        // Update the appointment status
        $appointment->update([
            'status' => $request->status,
        ]);

        // Fire SMS notification event if appointment is confirmed
        if ($request->status === 'confirmed' && $oldStatus !== 'confirmed') {
            event(new \App\Events\AppointmentConfirmed($appointment));
        }

        // Fire SMS notification event if appointment is cancelled
        if ($request->status === 'cancelled' && $oldStatus !== 'cancelled') {
            event(new \App\Events\AppointmentCancelled($appointment));
        }

        // Return JSON response for AJAX
        return response()->json([
            'success' => true,
            'message' => 'Appointment status updated successfully.',
            'status' => $appointment->status,
        ]);
    }

    /**
     * Update appointment status for employee's appointments
     */
    public function updateEmployeeAppointmentStatus(Request $request, $appointmentId)
    {
        $request->validate([
            'status' => 'required|in:scheduled,confirmed,completed,cancelled',
        ]);

        $user = backpack_user();

        $appointment = Appointment::where('id', $appointmentId)
            ->where('employee_id', $user->id)
            ->firstOrFail();

        // Update the appointment status
        $appointment->update([
            'status' => $request->status,
        ]);

        // Fire SMS notification event if appointment is confirmed
        if ($request->status === 'confirmed') {
            event(new \App\Events\AppointmentConfirmed($appointment));
        }

        // Fire SMS notification event if appointment is cancelled
        if ($request->status === 'cancelled') {
            event(new \App\Events\AppointmentCancelled($appointment));
        }

        // Return JSON response for AJAX
        return response()->json([
            'success' => true,
            'message' => 'Appointment status updated successfully.',
            'status' => $appointment->status,
            'appointment_id' => $appointment->id,
        ]);
    }

    /**
     * Show patient's medical records
     */
    public function patientMedicalRecords($patientId)
    {
        $patient = User::where('user_type', 'patient')
            ->where('id', $patientId)
            ->firstOrFail();

        $prenatalRecords = \App\Models\PrenatalRecord::where('patient_id', $patient->id)
            ->with('attendingPhysician')
            ->orderBy('visit_date', 'desc')
            ->paginate(10);

        $postnatalRecords = \App\Models\PostnatalRecord::where('patient_id', $patient->id)
            ->with('provider')
            ->orderBy('visit_date', 'desc')
            ->paginate(10);

        $postpartumRecords = \App\Models\PostpartumRecord::where('patient_id', $patient->id)
            ->with('provider')
            ->orderBy('visit_date', 'desc')
            ->paginate(10);

        $deliveryRecords = \App\Models\DeliveryRecord::where('patient_id', $patient->id)
            ->with(['attendingProvider', 'deliveringProvider'])
            ->orderBy('delivery_date_time', 'desc')
            ->paginate(10);

        $labResults = \App\Models\LaboratoryResult::where('patient_id', $patient->id)
            ->with(['orderingProvider', 'performingTechnician', 'reviewingProvider'])
            ->orderBy('test_ordered_date_time', 'desc')
            ->paginate(10);

        $treatments = \App\Models\Treatment::where('patient_id', $patient->id)
            ->where('treatment_type', 'medication')
            ->with(['prescriber', 'prescriptions.inventory'])
            ->orderBy('prescribed_date', 'desc')
            ->paginate(10);

        return view('employee.patients.medical-records', compact(
            'patient',
            'prenatalRecords',
            'postnatalRecords',
            'postpartumRecords',
            'deliveryRecords',
            'labResults',
            'treatments'
        ));
    }

    // ========== MEDICAL RECORDS MANAGEMENT ==========

    /**
     * Show prenatal records for a patient
     */
    public function prenatalRecords($patientId)
    {
        $patient = User::findOrFail($patientId);
        $prenatalRecords = PrenatalRecord::where('patient_id', $patientId)
            ->orderBy('visit_date', 'desc')
            ->paginate(10);

        return view('employee.patients.prenatal-records', compact('patient', 'prenatalRecords'));
    }


    /**
     * Show individual prenatal record details
     */
    public function showPrenatalRecord($patientId, $recordId)
    {
        $patient = User::findOrFail($patientId);
        $record = PrenatalRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->with(['attendingPhysician', 'midwife'])
            ->firstOrFail();

        return view('employee.patients.show-prenatal-record', compact('patient', 'record'));
    }

    /**
     * Show edit prenatal record form
     */
    public function editPrenatalRecord($patientId, $recordId)
    {
        $patient = User::findOrFail($patientId);
        $record = PrenatalRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->firstOrFail();
        $providers = User::where('user_type', 'employee')->get();

        return view('employee.patients.edit-prenatal-record', compact('patient', 'record', 'providers'));
    }

    /**
     * Update prenatal record
     */
    public function updatePrenatalRecord(Request $request, $patientId, $recordId)
    {
        $record = PrenatalRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

        $request->validate([
            'attending_physician_id' => 'required|exists:users,id',
            'midwife_id' => 'nullable|exists:users,id',
            'visit_date' => 'required|date',
            'visit_time' => 'nullable|date_format:H:i',
            'times_visited' => 'nullable|integer|min:1',
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
            'times_visited' => $request->times_visited,
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

        return redirect()->route('employee.patients.prenatal-records', $patientId)
            ->with('success', 'Prenatal record updated successfully.');
    }

    /**
     * Show form to create prenatal record
     */
    public function createPrenatalRecord($patientId)
    {
        $patient = User::findOrFail($patientId);
        $providers = User::where('user_type', 'employee')->get();

        return view('employee.patients.create-prenatal-record', compact('patient', 'providers'));
    }

    /**
     * Store prenatal record
     */
    public function storePrenatalRecord(Request $request, $patientId)
    {
        $request->validate([
            'attending_physician_id' => 'required|exists:users,id',
            'midwife_id' => 'nullable|exists:users,id',
            'visit_date' => 'required|date',
            'visit_time' => 'nullable|date_format:H:i',
            'times_visited' => 'nullable|integer|min:1',
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
            'pulse_rate' => 'nullable|integer|min:0',
            'respiratory_rate' => 'nullable|integer|min:0',
            'temperature_celsius' => 'nullable|numeric|min:0',
            'fetal_heart_rate' => 'nullable|integer|min:0',
            'fundal_height_cm' => 'nullable|numeric|min:0',
            'fetal_position' => 'nullable|string|max:100',
            'fetal_presentation' => 'nullable|string|max:100',
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
            'times_visited' => $request->times_visited ?? 1,
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
            'pulse_rate' => $request->pulse_rate,
            'respiratory_rate' => $request->respiratory_rate,
            'temperature_celsius' => $request->temperature_celsius,
            'fetal_heart_rate' => $request->fetal_heart_rate,
            'fundal_height_cm' => $request->fundal_height_cm,
            'fetal_position' => $request->fetal_position,
            'fetal_presentation' => $request->fetal_presentation,
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

        return redirect()->route('employee.patients.prenatal-records', $patientId)
            ->with('success', 'Prenatal record created successfully.');
    }

    /**
     * Show lab results for a patient
     */
    public function labResults($patientId)
    {
        $patient = User::findOrFail($patientId);
        $labResults = LaboratoryResult::where('patient_id', $patientId)
            ->orderBy('test_ordered_date_time', 'desc')
            ->paginate(10);

        return view('employee.patients.lab-results', compact('patient', 'labResults'));
    }

    /**
     * Show form to create lab result
     */
    public function createLabResult($patientId)
    {
        $patient = User::findOrFail($patientId);
        $providers = User::where('user_type', 'employee')->get();

        return view('employee.patients.create-lab-result', compact('patient', 'providers'));
    }

    /**
     * Store lab result
     */
    public function storeLabResult(Request $request, $patientId)
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

        // Prepare data, handling boolean fields and empty strings
        $data = $request->all();
        $data['patient_id'] = $patientId; // Ensure patient_id is set

        // Handle boolean fields - convert to 0/1
        $booleanFields = [
            'qc_passed', 'covered_by_philhealth', 'requires_follow_up', 'urgent', 'stat'
        ];

        foreach ($booleanFields as $field) {
            $data[$field] = $request->has($field) ? 1 : 0;
        }

        // Convert empty strings to null for nullable fields
        $nullableFields = [
            'performed_by', 'reviewed_by', 'test_code', 'test_description', 'sample_type_other',
            'sample_id', 'test_result', 'result_value', 'result_unit', 'reference_range',
            'test_performed_date_time', 'result_available_date_time', 'result_reviewed_date_time',
            'clinical_indication', 'interpretation', 'comments', 'qc_notes', 'test_cost',
            'philhealth_coverage_amount', 'follow_up_instructions', 'follow_up_date',
            'rejection_reason', 'rejected_date_time'
        ];

        foreach ($nullableFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        $labResult = LaboratoryResult::create($data);

        // Fire SMS notification event if lab result is completed
        if ($request->result_status === 'completed') {
            event(new \App\Events\LabResultsReady($labResult));
        }

        return redirect()->route('employee.patients.lab-results', $patientId)
            ->with('success', 'Lab result created successfully.');
    }

    /**
     * Show individual lab result details
     */
    public function showLabResult($patientId, $resultId)
    {
        $patient = User::findOrFail($patientId);
        $result = LaboratoryResult::where('id', $resultId)
            ->where('patient_id', $patientId)
            ->with(['orderingProvider', 'performingTechnician', 'reviewingProvider'])
            ->firstOrFail();

        return view('employee.patients.show-lab-result', compact('patient', 'result'));
    }

    /**
     * Show edit lab result form
     */
    public function editLabResult($patientId, $resultId)
    {
        $patient = User::findOrFail($patientId);
        $result = LaboratoryResult::where('id', $resultId)
            ->where('patient_id', $patientId)
            ->firstOrFail();
        $providers = User::where('user_type', 'employee')->get();

        return view('employee.patients.edit-lab-result', compact('patient', 'result', 'providers'));
    }

    /**
     * Update lab result
     */
    public function updateLabResult(Request $request, $patientId, $resultId)
    {
        $result = LaboratoryResult::where('id', $resultId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

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

        // Prepare data, handling boolean fields and empty strings
        $data = $request->all();
        $data['patient_id'] = $patientId; // Ensure patient_id is set

        // Handle boolean fields - convert to 0/1
        $booleanFields = [
            'qc_passed', 'covered_by_philhealth', 'requires_follow_up', 'urgent', 'stat'
        ];

        foreach ($booleanFields as $field) {
            $data[$field] = $request->has($field) ? 1 : 0;
        }

        // Convert empty strings to null for nullable fields
        $nullableFields = [
            'performed_by', 'reviewed_by', 'test_code', 'test_description', 'sample_type_other',
            'sample_id', 'test_result', 'result_value', 'result_unit', 'reference_range',
            'test_performed_date_time', 'result_available_date_time', 'result_reviewed_date_time',
            'clinical_indication', 'interpretation', 'comments', 'qc_notes', 'test_cost',
            'philhealth_coverage_amount', 'follow_up_instructions', 'follow_up_date',
            'rejection_reason', 'rejected_date_time'
        ];

        foreach ($nullableFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        $result->update($data);

        // Fire SMS notification event if lab result is completed
        if ($request->result_status === 'completed') {
            event(new \App\Events\LabResultsReady($result));
        }

        return redirect()->route('employee.patients.show-lab-result', [$patientId, $resultId])
            ->with('success', 'Lab result updated successfully.');
    }

    // ========== POSTNATAL RECORDS MANAGEMENT ==========

    /**
     * Show postnatal records for a patient
     */
    public function postnatalRecords($patientId)
    {
        $patient = User::where('user_type', 'patient')->findOrFail($patientId);
        $postnatalRecords = PostnatalRecord::where('patient_id', $patientId)
            ->orderBy('visit_date', 'desc')
            ->paginate(10);

        return view('employee.patients.postnatal-records', compact('patient', 'postnatalRecords'));
    }

    /**
     * Show form to create postnatal record
     */
    public function createPostnatalRecord($patientId)
    {
        $patient = User::where('user_type', 'patient')->findOrFail($patientId);
        $providers = User::where('user_type', 'employee')->get();

        return view('employee.patients.create-postnatal-record', compact('patient', 'providers'));
    }

    /**
     * Store postnatal record
     */
    public function storePostnatalRecord(Request $request, $patientId)
    {
        $patient = User::where('user_type', 'patient')->findOrFail($patientId);

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
            'newborn_check' => $request->has('newborn_check') ? 1 : 0,
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

        return redirect()->route('employee.patients.postnatal-records', $patientId)
            ->with('success', 'Postnatal record created successfully.');
    }

    // ========== POSTPARTUM RECORDS MANAGEMENT ==========

    /**
     * Show postpartum records for a patient
     */
    public function postpartumRecords($patientId)
    {
        $patient = User::findOrFail($patientId);
        $postpartumRecords = PostpartumRecord::where('patient_id', $patientId)
            ->orderBy('visit_date', 'desc')
            ->paginate(10);

        return view('employee.patients.postpartum-records', compact('patient', 'postpartumRecords'));
    }

    /**
     * Show form to create postpartum record
     */
    public function createPostpartumRecord($patientId)
    {
        $patient = User::findOrFail($patientId);
        $providers = User::where('user_type', 'employee')->get();

        return view('employee.patients.create-postpartum-record', compact('patient', 'providers'));
    }

    // /**
    //  * Store postpartum record
    //  */
    // public function storePostpartumRecord(Request $request, $patientId)
    // {
    //     $request->validate([
    //         'provider_id' => 'required|exists:users,id',
    //         'visit_number' => 'required|integer|min:1',
    //         'visit_date' => 'required|date',
    //         'weeks_postpartum' => 'nullable|integer|min:0',
    //         'days_postpartum' => 'nullable|integer|min:0',
    //         'weight' => 'nullable|numeric|min:0',
    //         'blood_pressure_systolic' => 'nullable|integer|min:0',
    //         'blood_pressure_diastolic' => 'nullable|integer|min:0',
    //         'heart_rate' => 'nullable|integer|min:0',
    //         'temperature' => 'nullable|numeric|min:0',
    //         'general_condition' => 'nullable|string|max:100',
    //         'breast_condition' => 'nullable|string|max:100',
    //         'uterus_condition' => 'nullable|string|max:100',
    //         'perineum_condition' => 'nullable|string|max:100',
    //         'lochia_condition' => 'nullable|string|max:100',
    //         'episiotomy_condition' => 'nullable|string|max:100',
    //         'mood_assessment' => 'nullable|string|max:100',
    //         'emotional_support_needs' => 'nullable|string',
    //         'postpartum_depression_screening' => 'nullable|boolean',
    //         'mental_health_notes' => 'nullable|string',
    //         'breastfeeding_status' => 'nullable|string|max:100',
    //         'breastfeeding_challenges' => 'nullable|string',
    //         'lactation_support' => 'nullable|string',
    //         'infant_feeding_assessment' => 'nullable|boolean',
    //         'infant_care_education' => 'nullable|string',
    //         'contraceptive_method' => 'nullable|string|max:100',
    //         'family_planning_counseling' => 'nullable|string',
    //         'next_contraceptive_visit' => 'nullable|date',
    //         'postpartum_complications' => 'nullable|string',
    //         'medications_prescribed' => 'nullable|string',
    //         'wound_care_instructions' => 'nullable|string',
    //         'activity_restrictions' => 'nullable|string',
    //         'follow_up_date' => 'nullable|date',
    //         'follow_up_reason' => 'nullable|string|max:200',
    //         'education_provided' => 'nullable|array',
    //         'nutrition_counseling' => 'nullable|string',
    //         'exercise_guidance' => 'nullable|string',
    //         'warning_signs_education' => 'nullable|string',
    //         'assessment_notes' => 'nullable|string',
    //         'plan_notes' => 'nullable|string',
    //         'alerts_flags' => 'nullable|array',
    //         'quality_indicators_met' => 'nullable|array',
    //     ]);

    //     PostpartumRecord::create([
    //         'patient_id' => $patientId,
    //         'provider_id' => $request->provider_id,
    //         'visit_number' => $request->visit_number,
    //         'visit_date' => $request->visit_date,
    //         'weeks_postpartum' => $request->weeks_postpartum,
    //         'days_postpartum' => $request->days_postpartum,
    //         'weight' => $request->weight,
    //         'blood_pressure_systolic' => $request->blood_pressure_systolic,
    //         'blood_pressure_diastolic' => $request->blood_pressure_diastolic,
    //         'heart_rate' => $request->heart_rate,
    //         'temperature' => $request->temperature,
    //         'general_condition' => $request->general_condition,
    //         'breast_condition' => $request->breast_condition,
    //         'uterus_condition' => $request->uterus_condition,
    //         'perineum_condition' => $request->perineum_condition,
    //         'lochia_condition' => $request->lochia_condition,
    //         'episiotomy_condition' => $request->episiotomy_condition,
    //         'mood_assessment' => $request->mood_assessment,
    //         'emotional_support_needs' => $request->emotional_support_needs,
    //         'postpartum_depression_screening' => $request->postpartum_depression_screening,
    //         'mental_health_notes' => $request->mental_health_notes,
    //         'breastfeeding_status' => $request->breastfeeding_status,
    //         'breastfeeding_challenges' => $request->breastfeeding_challenges,
    //         'lactation_support' => $request->lactation_support,
    //         'infant_feeding_assessment' => $request->infant_feeding_assessment,
    //         'infant_care_education' => $request->infant_care_education,
    //         'contraceptive_method' => $request->contraceptive_method,
    //         'family_planning_counseling' => $request->family_planning_counseling,
    //         'next_contraceptive_visit' => $request->next_contraceptive_visit,
    //         'postpartum_complications' => $request->postpartum_complications,
    //         'medications_prescribed' => $request->medications_prescribed,
    //         'wound_care_instructions' => $request->wound_care_instructions,
    //         'activity_restrictions' => $request->activity_restrictions,
    //         'follow_up_date' => $request->follow_up_date,
    //         'follow_up_reason' => $request->follow_up_reason,
    //         'education_provided' => $request->education_provided,
    //         'nutrition_counseling' => $request->nutrition_counseling,
    //         'exercise_guidance' => $request->exercise_guidance,
    //         'warning_signs_education' => $request->warning_signs_education,
    //         'assessment_notes' => $request->assessment_notes,
    //         'plan_notes' => $request->plan_notes,
    //         'alerts_flags' => $request->alerts_flags,
    //         'quality_indicators_met' => $request->quality_indicators_met,
    //     ]);

    //     return redirect()->route('employee.patients.postpartum-records', $patientId)
    //         ->with('success', 'Postpartum record created successfully.');
    // }

    // ========== DELIVERY RECORDS MANAGEMENT ==========

    /**
     * Show delivery records for a patient
     */
    public function deliveryRecords($patientId)
    {
        $patient = User::findOrFail($patientId);
        $deliveryRecords = DeliveryRecord::where('patient_id', $patientId)
            ->orderBy('delivery_date_time', 'desc')
            ->paginate(10);

        return view('employee.patients.delivery-records', compact('patient', 'deliveryRecords'));
    }

    /**
     * Show form to create delivery record
     */
    public function createDeliveryRecord($patientId)
    {
        $patient = User::findOrFail($patientId);
        $providers = User::where('user_type', 'employee')->get();

        return view('employee.patients.create-delivery-record', compact('patient', 'providers'));
    }

    /**
     * Store delivery record
     */
    public function storeDeliveryRecord(Request $request, $patientId)
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

        // Handle boolean fields properly
        $booleanFields = ['episiotomy_performed', 'placenta_complete'];
        $data = $request->all();

        foreach ($booleanFields as $field) {
            $value = $request->input($field);
            $data[$field] = $value === '1' || $value === 1 || $value === true ? 1 : 0;
        }

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
            'episiotomy_performed' => $data['episiotomy_performed'],
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
            'placenta_complete' => $data['placenta_complete'],
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

        return redirect()->route('employee.patients.delivery-records', $patientId)
            ->with('success', 'Delivery record created successfully.');
    }

    /**
     * Delete prenatal record
     */
    public function deletePrenatalRecord($patientId, $recordId)
    {
        $record = PrenatalRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

        $record->delete();

        return redirect()->route('employee.patients.prenatal-records', $patientId)
            ->with('success', 'Prenatal record deleted successfully.');
    }

    /**
     * Show postnatal record details
     */
    public function showPostnatalRecord($patientId, $recordId)
    {
        $patient = User::where('user_type', 'patient')->findOrFail($patientId);
        $record = PostnatalRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->with('provider')
            ->firstOrFail();

        return view('employee.patients.show-postnatal-record', compact('patient', 'record'));
    }

    /**
     * Show edit postnatal record form
     */
    public function editPostnatalRecord($patientId, $recordId)
    {
        $patient = User::where('user_type', 'patient')->findOrFail($patientId);
        $record = PostnatalRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->firstOrFail();
        $providers = User::where('user_type', 'employee')->get();

        return view('employee.patients.edit-postnatal-record', compact('patient', 'record', 'providers'));
    }

    /**
     * Update postnatal record
     */
    public function updatePostnatalRecord(Request $request, $patientId, $recordId)
    {
        $patient = User::where('user_type', 'patient')->findOrFail($patientId);
        $record = PostnatalRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

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

        $record->update($request->all());

        return redirect()->route('employee.patients.show-postnatal-record', [$patientId, $recordId])
            ->with('success', 'Postnatal record updated successfully.');
    }

    /**
     * Show postpartum record details
     */
    public function showPostpartumRecord($patientId, $recordId)
    {
        $patient = User::findOrFail($patientId);
        $record = PostpartumRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->with('provider')
            ->firstOrFail();

        return view('employee.patients.show-postpartum-record', compact('patient', 'record'));
    }

    /**
     * Show edit postpartum record form
     */
    public function editPostpartumRecord($patientId, $recordId)
    {
        $patient = User::findOrFail($patientId);
        $record = PostpartumRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->firstOrFail();
        $providers = User::where('user_type', 'employee')->get();

        return view('employee.patients.edit-postpartum-record', compact('patient', 'record', 'providers'));
    }


    /**
     * Store new postpartum record
     */
    public function storePostpartumRecord(Request $request, $patientId)
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

        $record = PostpartumRecord::create(array_merge($request->all(), ['patient_id' => $patientId]));

        return redirect()->route('employee.patients.show-postpartum-record', [$patientId, $record->id])
            ->with('success', 'Postpartum record created successfully.');
    }

    /**
     * Update postpartum record
     */
    public function updatePostpartumRecord(Request $request, $patientId, $recordId)
    {
        $record = PostpartumRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

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

        $record->update($request->all());

        return redirect()->route('employee.patients.show-postpartum-record', [$patientId, $recordId])
            ->with('success', 'Postpartum record updated successfully.');
    }

    /**
     * Delete postnatal record
     */
    public function deletePostnatalRecord($patientId, $recordId)
    {
        $patient = User::where('user_type', 'patient')->findOrFail($patientId);
        $record = PostnatalRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

        $record->delete();

        return redirect()->route('employee.patients.postnatal-records', $patientId)
            ->with('success', 'Postnatal record deleted successfully.');
    }

    /**
     * Delete postpartum record
     */
    public function deletePostpartumRecord($patientId, $recordId)
    {
        $record = PostpartumRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

        $record->delete();

        return redirect()->route('employee.patients.postpartum-records', $patientId)
            ->with('success', 'Postpartum record deleted successfully.');
    }

    /**
     * Show individual delivery record details
     */
    public function showDeliveryRecord($patientId, $recordId)
    {
        $patient = User::findOrFail($patientId);
        $record = DeliveryRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->with(['attendingProvider', 'deliveringProvider', 'anesthesiologist'])
            ->firstOrFail();

        return view('employee.patients.show-delivery-record', compact('patient', 'record'));
    }

    /**
     * Show edit delivery record form
     */
    public function editDeliveryRecord($patientId, $recordId)
    {
        $patient = User::findOrFail($patientId);
        $record = DeliveryRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->firstOrFail();
        $providers = User::where('user_type', 'employee')->get();

        return view('employee.patients.edit-delivery-record', compact('patient', 'record', 'providers'));
    }

    /**
     * Update delivery record
     */
    public function updateDeliveryRecord(Request $request, $patientId, $recordId)
    {
        $record = DeliveryRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

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

        // Handle boolean fields properly
        $booleanFields = ['episiotomy_performed', 'placenta_complete'];
        $data = $request->all();

        foreach ($booleanFields as $field) {
            $value = $request->input($field);
            $data[$field] = $value === '1' || $value === 1 || $value === true ? 1 : 0;
        }

        $record->update($data);

        return redirect()->route('employee.patients.show-delivery-record', [$patientId, $recordId])
            ->with('success', 'Delivery record updated successfully.');
    }

    /**
     * Delete delivery record
     */
    public function deleteDeliveryRecord($patientId, $recordId)
    {
        $record = DeliveryRecord::where('id', $recordId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

        $record->delete();

        return redirect()->route('employee.patients.delivery-records', $patientId)
            ->with('success', 'Delivery record deleted successfully.');
    }

    /**
     * Delete lab result
     */
    public function deleteLabResult($patientId, $resultId)
    {
        $result = LaboratoryResult::where('id', $resultId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

        $result->delete();

        return redirect()->route('employee.patients.lab-results', $patientId)
            ->with('success', 'Lab result deleted successfully.');
    }

    // ========== PAYMENT MANAGEMENT ==========

    /**
     * Show all payments for employees to manage
     */
    public function payments(Request $request)
    {
        $query = Payment::with(['patient', 'appointment.service', 'items.service']);

        // Filter by patient
        if ($request->has('patient_id') && !is_null($request->patient_id) && $request->patient_id !== '') {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by status
        if ($request->has('status') && !is_null($request->status) && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method') && !is_null($request->payment_method) && $request->payment_method !== '') {
            $query->where('payment_method', $request->payment_method);
        }

        // Search by patient name or payment reference
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('patient', function($patientQuery) use ($search) {
                    $patientQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhere('payment_reference', 'like', "%{$search}%");
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get patients for filter dropdown
        $patients = User::where('user_type', 'patient')->orderBy('name')->get();

        // Calculate payment statistics
        $totalPayments = $payments->total();
        $completedPayments = $payments->whereIn('status', ['completed', 'successful'])->count();
        $pendingPayments = $payments->where('status', 'pending')->count();
        $awaitingApprovalPayments = $payments->where('status', 'awaiting_approval')->count();
        $failedPayments = $payments->where('status', 'failed')->count();
        $totalAmount = $payments->sum('amount');

        return view('employee.payments.index', compact(
            'payments',
            'patients',
            'totalPayments',
            'completedPayments',
            'pendingPayments',
            'awaitingApprovalPayments',
            'failedPayments',
            'totalAmount'
        ));
    }

    /**
     * Show payment details
     */
    public function showPayment($paymentId)
    {
        $payment = Payment::with(['patient', 'appointment.service', 'items.service'])->findOrFail($paymentId);

        return view('employee.payments.show', compact('payment'));
    }

    /**
     * Show create payment form for a patient
     */
    public function createPayment($patientId)
    {
        $patient = User::where('user_type', 'patient')
            ->where('id', $patientId)
            ->firstOrFail();

        $services = Service::all();
        $appointments = Appointment::where('patient_id', $patientId)
            ->where('status', '!=', 'cancelled')
            ->with('service')
            ->get();

        return view('employee.payments.create', compact('patient', 'services', 'appointments'));
    }

    /**
     * Store new payment
     */
    public function storePayment(Request $request, $patientId)
    {
        $request->validate([
            'appointment_id' => 'nullable|exists:appointments,id',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id',
            'payment_method' => 'required|in:paypal,gcash,cash',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $services = Service::whereIn('id', $request->services)->get();
            $total = $services->sum('price');

            // Create payment record
            $payment = Payment::create([
                'patient_id' => $patientId,
                'appointment_id' => $request->appointment_id,
                'amount' => $total,
                'currency' => 'PHP',
                'payment_method' => $request->payment_method,
                'description' => 'Payment for FRYDT Clinic Services',
                'status' => $request->payment_method === 'cash' ? 'completed' : 'pending',
                'notes' => $request->notes,
                'paid_at' => $request->payment_method === 'cash' ? now() : null,
            ]);

            // Create payment items
            foreach ($services as $service) {
                PaymentItem::create([
                    'payment_id' => $payment->id,
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'quantity' => 1,
                    'unit_price' => $service->price,
                    'total_price' => $service->price
                ]);
            }

            DB::commit();

            // Update appointment status if applicable
            if ($payment->appointment && $payment->status === 'completed') {
                $payment->appointment->update(['status' => 'confirmed']);
            }

            return redirect()->route('employee.payments.show', $payment->id)
                ->with('success', 'Payment created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment creation error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create payment. Please try again.');
        }
    }


    /**
     * Show patient's payment history
     */
    public function patientPayments($patientId)
    {
        $patient = User::where('user_type', 'patient')
            ->where('id', $patientId)
            ->firstOrFail();

        $payments = Payment::where('patient_id', $patientId)
            ->with(['appointment.service', 'items.service'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculate payment statistics for this patient
        $totalPaid = $payments->where('status', 'completed')->sum('amount');
        $pendingAmount = $payments->where('status', 'pending')->sum('amount');
        $completedCount = $payments->where('status', 'completed')->count();

        return view('employee.patients.payments', compact(
            'patient',
            'payments',
            'totalPaid',
            'pendingAmount',
            'completedCount'
        ));
    }

    /**
     * Show treatments for a patient
     */
    public function treatments($patientId)
    {
        $patient = User::findOrFail($patientId);
        $treatments = Treatment::where('patient_id', $patientId)
            ->with(['prescriber', 'prescriptions.inventory'])
            ->orderBy('prescribed_date', 'desc')
            ->paginate(10);

        return view('employee.patients.treatments', compact('patient', 'treatments'));
    }

    /**
     * Show create treatment form
     */
    public function createTreatment($patientId)
    {
        $patient = User::findOrFail($patientId);
        $providers = User::where('user_type', 'employee')->get();
        $inventory = \App\Models\Inventory::active()->where('current_quantity', '>', 0)->get();

        return view('employee.patients.create-treatment', compact('patient', 'providers', 'inventory'));
    }

    /**
     * Store new treatment
     */
    public function storeTreatment(Request $request, $patientId)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventory,id',
            'quantity_dispensed' => 'required|integer|min:1',
            'prescribed_by' => 'required|exists:users,id',
            'prescribed_date' => 'required|date',
            'dosage' => 'nullable|string|max:100',
            'frequency' => 'nullable|string|max:100',
            'route' => 'nullable|in:oral,intravenous,intramuscular,subcutaneous,topical,inhalation,other',
            'duration_days' => 'nullable|integer|min:1',
            'priority' => 'required|in:routine,urgent,stat',
            'indication' => 'required|string|max:500',
            'special_instructions' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Get inventory item details
            $inventory = \App\Models\Inventory::findOrFail($request->inventory_id);

            // Check if enough stock is available
            if ($inventory->current_quantity < $request->quantity_dispensed) {
                throw new \Exception('Insufficient inventory stock for this medication.');
            }

            // Create treatment
            $treatment = Treatment::create([
                'patient_id' => $patientId,
                'prescribed_by' => $request->prescribed_by,
                'treatment_type' => 'medication',
                'treatment_name' => $inventory->name,
                'generic_name' => $inventory->description,
                'brand_name' => $inventory->manufacturer,
                'dosage' => $request->dosage,
                'frequency' => $request->frequency,
                'route' => $request->route,
                'duration_days' => $request->duration_days,
                'quantity_prescribed' => $request->quantity_dispensed,
                'priority' => $request->priority,
                'indication' => $request->indication,
                'special_instructions' => $request->special_instructions,
                'prescribed_date' => $request->prescribed_date,
                'status' => 'prescribed',
            ]);

            // Create prescription
            Prescription::create([
                'treatment_id' => $treatment->id,
                'inventory_id' => $request->inventory_id,
                'quantity_prescribed' => $request->quantity_dispensed,
                'quantity_dispensed' => 0, // Not dispensed yet
                'dosage_instructions' => $request->dosage . ' ' . $request->frequency . ' ' . ($request->route ?? ''),
                'duration_days' => $request->duration_days,
                'special_instructions' => $request->special_instructions,
                'prescribed_date' => $request->prescribed_date,
                'unit_price' => $inventory->selling_price,
                'total_price' => null, // Will be set when dispensed
                'prescribed_by' => $request->prescribed_by,
                'status' => 'prescribed', // Patient will choose purchase location
            ]);

            // Do not update inventory stock yet - wait for patient choice

            DB::commit();

            return redirect()->route('employee.patients.medical-records', $patientId)
                ->with('success', 'Prescription created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create prescription: ' . $e->getMessage());
        }
    }

    /**
     * Show individual treatment details
     */
    public function showTreatment($patientId, $treatmentId)
    {
        $patient = User::findOrFail($patientId);
        $treatment = Treatment::where('id', $treatmentId)
            ->where('patient_id', $patientId)
            ->with(['prescriber', 'prescriptions.inventory', 'prescriptions.dispenser', 'prescriptions.payment'])
            ->firstOrFail();

        return view('employee.patients.show-treatment', compact('patient', 'treatment'));
    }

    /**
     * Show edit treatment form
     */
    public function editTreatment($patientId, $treatmentId)
    {
        $patient = User::findOrFail($patientId);
        $treatment = Treatment::where('id', $treatmentId)
            ->where('patient_id', $patientId)
            ->firstOrFail();
        $providers = User::where('user_type', 'employee')->get();
        $inventory = \App\Models\Inventory::active()->where('current_quantity', '>', 0)->get();

        return view('employee.patients.edit-treatment', compact('patient', 'treatment', 'providers', 'inventory'));
    }

    /**
     * Update treatment
     */
    public function updateTreatment(Request $request, $patientId, $treatmentId)
    {
        $treatment = Treatment::where('id', $treatmentId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

        $request->validate([
            'inventory_id' => 'required|exists:inventory,id',
            'quantity_dispensed' => 'required|integer|min:1',
            'dosage' => 'nullable|string|max:100',
            'frequency' => 'nullable|string|max:100',
            'route' => 'nullable|in:oral,intravenous,intramuscular,subcutaneous,topical,inhalation,other',
            'prescribed_by' => 'required|exists:users,id',
            'prescribed_date' => 'required|date',
            'duration_days' => 'nullable|integer|min:1',
            'priority' => 'required|in:routine,urgent,stat',
            'indication' => 'required|string|max:500',
            'special_instructions' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Get inventory item details
            $inventory = \App\Models\Inventory::findOrFail($request->inventory_id);

            // Update treatment
            $treatment->update([
                'prescribed_by' => $request->prescribed_by,
                'treatment_name' => $inventory->name,
                'generic_name' => $inventory->description,
                'brand_name' => $inventory->manufacturer,
                'dosage' => $request->dosage,
                'frequency' => $request->frequency,
                'route' => $request->route,
                'duration_days' => $request->duration_days,
                'quantity_prescribed' => $request->quantity_dispensed,
                'priority' => $request->priority,
                'indication' => $request->indication,
                'special_instructions' => $request->special_instructions,
                'prescribed_date' => $request->prescribed_date,
            ]);

            // Update associated prescription
            $prescription = $treatment->prescriptions()->first();
            if ($prescription) {
                $prescription->update([
                    'inventory_id' => $request->inventory_id,
                    'quantity_prescribed' => $request->quantity_dispensed,
                    'dosage_instructions' => $request->dosage . ' ' . $request->frequency . ' ' . ($request->route ?? ''),
                    'duration_days' => $request->duration_days,
                    'special_instructions' => $request->special_instructions,
                    'prescribed_date' => $request->prescribed_date,
                    'unit_price' => $inventory->selling_price,
                    'prescribed_by' => $request->prescribed_by,
                ]);
            }

            DB::commit();

            return redirect()->route('employee.patients.show-treatment', [$patientId, $treatmentId])
                ->with('success', 'Prescription updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update prescription: ' . $e->getMessage());
        }
    }

    /**
     * Delete treatment
     */
    public function deleteTreatment($patientId, $treatmentId)
    {
        $treatment = Treatment::where('id', $treatmentId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

        $treatment->delete();

        return redirect()->route('employee.patients.treatments', $patientId)
            ->with('success', 'Treatment deleted successfully.');
    }

    /**
     * Dispense medication for existing treatment
     */
    public function dispenseMedication(Request $request, $patientId, $treatmentId)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventory,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $treatment = Treatment::where('id', $treatmentId)
            ->where('patient_id', $patientId)
            ->firstOrFail();

        $inventory = \App\Models\Inventory::findOrFail($request->inventory_id);

        DB::beginTransaction();

        try {
            // Check stock availability
            if ($inventory->current_quantity < $request->quantity) {
                throw new \Exception('Insufficient inventory stock for this medication.');
            }

            // Create prescription record
            Prescription::create([
                'treatment_id' => $treatment->id,
                'inventory_id' => $request->inventory_id,
                'quantity' => $request->quantity,
                'unit' => $inventory->unit_of_measure ?? 'units',
                'dispensed_date' => now(),
                'dispensed_by' => backpack_user()->id,
                'notes' => $request->notes,
            ]);

            // Update inventory stock
            $inventory->removeStock($request->quantity, "Dispensed for treatment #{$treatment->id}");

            DB::commit();

            return redirect()->back()
                ->with('success', 'Medication dispensed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to dispense medication: ' . $e->getMessage());
        }
    }

    /**
     * Generate pay slip PDF
     */
    public function generatePaySlip($payrollId)
    {
        $payroll = Payroll::with('employee')->findOrFail($payrollId);

        // Authorization: Only admin or the employee themselves can view the payslip
        if (auth()->user()->user_type !== 'admin' && $payroll->employee_id !== Auth::id()) {
            abort(403, 'You are not authorized to view this payslip.');
        }

        // Get attendance records for the pay period
        $attendanceRecords = EmployeeAttendance::where('employee_id', $payroll->employee_id)
            ->whereBetween('date', [$payroll->pay_period_start, $payroll->pay_period_end])
            ->orderBy('date', 'asc')
            ->get();

        // For now, return a simple HTML view that can be printed as PDF
        // In a real application, you might use a PDF library like TCPDF or DomPDF
        return view('admin-portal.pay-slip', compact('payroll', 'attendanceRecords'));
    }

    //
}
