<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Billing;
use App\Models\Document;
use App\Models\LaboratoryResult;
use App\Models\PrenatalRecord;
use App\Models\Prescription;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PatientDashboardController extends Controller
{
    public function __construct() {}

    /**
     * Helper method to extract time string from various formats
     * Handles Carbon objects, datetime strings, or raw time strings
     *
     * @param mixed $timeValue
     * @return string
     */
    private function extractTime($timeValue)
    {
        if ($timeValue instanceof \Carbon\Carbon) {
            return $timeValue->format('H:i:s');
        } elseif (is_string($timeValue)) {
            // Check if it's a full datetime string and extract time portion
            try {
                $parsed = \Carbon\Carbon::parse($timeValue);
                return $parsed->format('H:i:s');
            } catch (\Exception $e) {
                // If parsing fails, assume it's already a time string
                return $timeValue;
            }
        }
        
        return '09:00:00'; // Default fallback
    }

    /**
     * Display the patient dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        $patientProfile = $user->patientProfile;

        // Get upcoming appointments
        $upcomingAppointments = Appointment::where('patient_id', $user->id)
            ->where('appointment_datetime', '>=', Carbon::now())
            ->where('status', 'scheduled')
            ->orderBy('appointment_datetime')
            ->limit(5)
            ->with(['service', 'employee'])
            ->get();

        // Get recent appointment history
        $recentAppointments = Appointment::where('patient_id', $user->id)
            ->where('appointment_datetime', '<', Carbon::now())
            ->orderBy('appointment_datetime', 'desc')
            ->limit(5)
            ->with(['service', 'employee'])
            ->get();

        // Get appointment statistics
        $totalAppointments = Appointment::where('patient_id', $user->id)->count();
        $completedAppointments = Appointment::where('patient_id', $user->id)
            ->where('status', 'completed')->count();
        $cancelledAppointments = Appointment::where('patient_id', $user->id)
            ->where('status', 'cancelled')->count();

        // Get recently approved payments (last 7 days)
        $recentlyApprovedPayments = \App\Models\Payment::where('patient_id', $user->id)
            ->where('status', 'successful')
            ->where('approved_at', '>=', Carbon::now()->subDays(7))
            ->with(['items.service'])
            ->orderBy('approved_at', 'desc')
            ->get();

        return view('patient.dashboard', compact(
            'user',
            'patientProfile',
            'upcomingAppointments',
            'recentAppointments',
            'totalAppointments',
            'completedAppointments',
            'cancelledAppointments',
            'recentlyApprovedPayments'
        ));
    }

    /**
     * Show appointment booking form
     */
    public function bookAppointment()
    {
        $services = Service::all();
        $user = auth()->user();

        return view('patient.book-appointment', compact('services', 'user'));
    }

    /**
     * Show patient appointments
     */
    public function appointments()
    {
        $user = auth()->user();

        $appointments = Appointment::where('patient_id', $user->id)
            ->orderBy('appointment_datetime', 'desc')
            ->with(['service', 'employee', 'payments'])
            ->paginate(10);

        return view('patient.appointments', compact('appointments', 'user'));
    }

    /**
     * Show patient profile
     */
    public function profile()
    {
        $user = auth()->user();
        $patientProfile = $user->patientProfile;

        return view('patient.profile', compact('user', 'patientProfile'));
    }

    /**
     * Update patient profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $patientProfile = $user->patientProfile;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'civil_status' => 'nullable|in:single,married,widowed,separated',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'user_pin' => 'nullable|numeric|digits:6|confirmed|unique:users,pin,'.$user->id,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle profile image upload
        $imagePath = null;
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($patientProfile && $patientProfile->image_path) {
                \Storage::disk('public')->delete($patientProfile->image_path);
            }

            // Store new image
            $imagePath = $request->file('profile_image')->store('patient-profiles', 'public');
        }

        // Update user table
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Handle user PIN update
        if ($request->filled('user_pin')) {
            $user->setPin($request->user_pin);
        }

        // Update patient profile if exists
        if ($patientProfile) {
            $profileData = [
                'phone' => $request->phone,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'civil_status' => $request->civil_status,
                'address' => $request->address,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
            ];

            // Only update image_path if a new image was uploaded
            if ($imagePath) {
                $profileData['image_path'] = $imagePath;
            }

            $patientProfile->update($profileData);
        }

        return redirect()->route('patient.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Get available employees for a service and date
     */
    public function getAvailableEmployees(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date|after:today',
        ]);

        $date = Carbon::parse($request->appointment_date);
        $dayOfWeek = $date->dayOfWeekIso;

        // Get all employees with user_type 'employee'
        $availableEmployees = User::where('user_type', 'employee')
            ->join('employee_profiles', 'users.id', '=', 'employee_profiles.employee_id')
            ->where('employee_profiles.status', 'active')
            ->select('users.id', 'users.name', 'employee_profiles.position')
            ->get();

        return response()->json($availableEmployees);
    }

    /**
     * Get available time slots for an employee
     */
    public function getAvailableTimeSlots(Request $request)
    {
        // Add detailed logging for debugging
        \Log::channel('daily')->info('getAvailableTimeSlots called', [
            'request_data' => $request->all(),
            'timestamp' => now(),
            'user_id' => auth()->id(),
        ]);

        try {
            $request->validate([
                'employee_id' => 'required|exists:users,id',
                'service_id' => 'required|exists:services,id',
                'appointment_date' => 'required|date|after:today',
            ]);

            \Log::channel('daily')->info('Validation passed', [
                'employee_id' => $request->employee_id,
                'service_id' => $request->service_id,
                'appointment_date' => $request->appointment_date,
            ]);

            $employee = User::findOrFail($request->employee_id);
            \Log::channel('daily')->info('Employee found', ['employee_id' => $employee->id, 'employee_name' => $employee->name]);

            // Verify employee has proper relationships
            if (!$employee->employeeProfile) {
                \Log::channel('daily')->error('Employee profile missing', ['employee_id' => $employee->id]);
                return response()->json(['error' => 'Employee profile not found'], 500);
            }

            $service = Service::findOrFail($request->service_id);
            \Log::channel('daily')->info('Service found', ['service_id' => $service->id, 'service_name' => $service->name, 'duration' => $service->duration_minutes]);

            // Check if service has valid duration
            if (!$service->duration_minutes || $service->duration_minutes <= 0) {
                \Log::channel('daily')->error('Invalid service duration', ['service_id' => $service->id, 'duration' => $service->duration_minutes]);
                return response()->json(['error' => 'Service duration is invalid'], 500);
            }

            $date = Carbon::parse($request->appointment_date);
            $dayOfWeek = $date->dayOfWeekIso;

            \Log::channel('daily')->info('Date parsed', [
                'appointment_date' => $request->appointment_date,
                'parsed_date' => $date->toDateString(),
                'day_of_week' => $dayOfWeek,
            ]);

            // Check if employee has schedules relationship
            if (!$employee->schedules()) {
                \Log::channel('daily')->error('Employee schedules relationship not accessible', ['employee_id' => $employee->id]);
                return response()->json(['error' => 'Employee schedules not accessible'], 500);
            }

            $schedule = $employee->schedules()
                ->where('day_of_week', $dayOfWeek)
                ->first();

            \Log::channel('daily')->info('Schedule query result', [
                'schedule_found' => !is_null($schedule),
                'schedule_id' => $schedule ? $schedule->id : null,
                'start_time' => $schedule ? $schedule->start_time : null,
                'end_time' => $schedule ? $schedule->end_time : null,
            ]);

            if (! $schedule) {
                \Log::channel('daily')->info('No schedule found for employee on requested day', [
                    'employee_id' => $employee->id,
                    'day_of_week' => $dayOfWeek,
                    'requested_date' => $request->appointment_date
                ]);
                return response()->json([]);
            }

            // Validate schedule times
            if (!$schedule->start_time || !$schedule->end_time) {
                \Log::channel('daily')->error('Invalid schedule times', [
                    'schedule_id' => $schedule->id,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time
                ]);
                return response()->json(['error' => 'Invalid schedule times'], 500);
            }

            try {
                // Extract time from schedule times using helper method
                $startTimeString = $this->extractTime($schedule->start_time);
                $endTimeString = $this->extractTime($schedule->end_time);

                $workStart = Carbon::parse($date->toDateString().' '.$startTimeString);
                $workEnd = Carbon::parse($date->toDateString().' '.$endTimeString);
            } catch (\Exception $e) {
                \Log::channel('daily')->error('Failed to parse work hours', [
                    'error' => $e->getMessage(),
                    'date' => $date->toDateString(),
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'parsed_start_time' => isset($startTimeString) ? $startTimeString : 'N/A',
                    'parsed_end_time' => isset($endTimeString) ? $endTimeString : 'N/A'
                ]);
                return response()->json(['error' => 'Invalid work hours format'], 500);
            }

            // Validate work hours are reasonable
            if ($workEnd->lte($workStart)) {
                \Log::channel('daily')->error('Invalid work hours - end time is not after start time', [
                    'work_start' => $workStart->format('Y-m-d H:i:s'),
                    'work_end' => $workEnd->format('Y-m-d H:i:s'),
                ]);
                return response()->json(['error' => 'Invalid work hours'], 500);
            }

            \Log::channel('daily')->info('Work hours calculated', [
                'work_start' => $workStart->format('Y-m-d H:i:s'),
                'work_end' => $workEnd->format('Y-m-d H:i:s'),
            ]);

            $duration = $service->duration_minutes;
            \Log::channel('daily')->info('Service duration', ['duration_minutes' => $duration]);

            // Check if service duration fits within reasonable work hours (max 12 hours)
            if ($duration > 720) { // 12 hours in minutes
                \Log::channel('daily')->error('Service duration too long', ['duration_minutes' => $duration]);
                return response()->json(['error' => 'Service duration is unreasonably long'], 500);
            }

            $appointments = Appointment::where('employee_id', $employee->id)
                ->whereDate('appointment_datetime', $date)
                ->where('status', '!=', 'cancelled')
                ->get();

            \Log::channel('daily')->info('Existing appointments found', [
                'appointment_count' => $appointments->count(),
                'appointments' => $appointments->pluck('appointment_datetime')->map(function($dt) {
                    return $dt->format('Y-m-d H:i:s');
                })->toArray(),
            ]);

             $slots = [];
             $current = $workStart->copy();
             $iterationCount = 0;

             while ($current->copy()->addMinutes($duration)->lte($workEnd)) {
                 $iterationCount++;
                 if ($iterationCount > 100) { // Prevent infinite loop
                     \Log::channel('daily')->error('Infinite loop detected in time slot generation');
                     break;
                 }

                 $slotEnd = $current->copy()->addMinutes($duration);

                 $overlaps = $appointments->contains(function ($appt) use ($current, $slotEnd) {
                     $apptStart = Carbon::parse($appt->appointment_datetime);
                     $apptEnd = $apptStart->copy()->addMinutes($appt->duration_in_minutes);

                     return $current < $apptEnd && $slotEnd > $apptStart;
                 });

                 $slots[] = [
                     'time' => $current->format('H:i'),
                     'display' => $current->format('g:i A'),
                     'available' => ! $overlaps
                 ];

                 $current->addMinutes(30); // slot interval
             }

             \Log::channel('daily')->info('Time slots generated', [
                 'total_slots' => count($slots),
                 'available_slots' => count(array_filter($slots, fn($slot) => $slot['available'])),
                 'iterations' => $iterationCount,
                 'slots' => $slots,
             ]);

             return response()->json($slots);

        } catch (\Exception $e) {
            \Log::channel('daily')->error('Exception in getAvailableTimeSlots', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return a more helpful error response for debugging
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Store new appointment
     */
    public function storeAppointment(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'employee_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
            'patient_notes' => 'nullable|string|max:1000',
        ]);

        $service = Service::findOrFail($request->service_id);

        $newStart = Carbon::createFromFormat(
            'Y-m-d H:i',
            $request->appointment_date.' '.$request->appointment_time
        );

        $newEnd = $newStart->copy()->addMinutes($service->duration_minutes);

        // Validate employee schedule
        $employee = User::findOrFail($request->employee_id);
        $dayOfWeek = $newStart->dayOfWeekIso;

        $schedule = $employee->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (! $schedule) {
            return back()->withErrors([
                'appointment_time' => 'Employee is not available on this day.',
            ]);
        }

        $scheduleStart = Carbon::parse($newStart->toDateString().' '.$this->extractTime($schedule->start_time));
        $scheduleEnd = Carbon::parse($newStart->toDateString().' '.$this->extractTime($schedule->end_time));

        if ($newStart->lt($scheduleStart) || $newEnd->gt($scheduleEnd)) {
            return back()->withErrors([
                'appointment_time' => 'Selected time is outside working hours.',
            ]);
        }

        // Overlap check
        $overlapExists = Appointment::where('employee_id', $employee->id)
            ->where('status', '!=', 'cancelled')
            ->whereDate('appointment_datetime', $newStart->toDateString())
            ->whereRaw(
                '? < DATE_ADD(appointment_datetime, INTERVAL duration_in_minutes MINUTE)
                AND ? > appointment_datetime',
                [$newStart, $newEnd]
            )
            ->exists();

        if ($overlapExists) {
            return back()->withErrors([
                'appointment_time' => 'This time slot is no longer available.',
            ]);
        }

        $appointment = Appointment::create([
            'patient_id' => auth()->id(),
            'employee_id' => $employee->id,
            'service_id' => $service->id,
            'appointment_datetime' => $newStart,
            'end_time' => $newEnd,
            'duration_in_minutes' => $service->duration_minutes,
            'patient_notes' => $request->patient_notes,
            'status' => 'scheduled',
        ]);

        event(new \App\Events\AppointmentBooked($appointment));

        return redirect()
            ->route('patient.appointments')
            ->with('success', 'Appointment booked successfully!');
    }

    /**
     * Show edit appointment form
     */
    public function editAppointment($appointmentId)
    {
        $user = auth()->user();
        $appointment = Appointment::where('patient_id', $user->id)
            ->where('id', $appointmentId)
            ->where('status', 'scheduled')
            ->with(['service', 'employee'])
            ->firstOrFail();

        $services = Service::all();

        return view('patient.edit-appointment', compact('appointment', 'services', 'user'));
    }

    /**
     * Update appointment
     */
    public function updateAppointment(Request $request, $appointmentId)
    {
        $user = auth()->user();
        $appointment = Appointment::where('patient_id', $user->id)
            ->where('id', $appointmentId)
            ->where('status', 'scheduled')
            ->firstOrFail();

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'employee_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
            'patient_notes' => 'nullable|string|max:1000',
        ]);

        $service = Service::findOrFail($request->service_id);
        $appointmentDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $request->appointment_date.' '.$request->appointment_time
        );

        // Double-check availability for the new time
        $existingAppointment = Appointment::where('employee_id', $request->employee_id)
            ->where('appointment_datetime', $appointmentDateTime)
            ->where('status', '!=', 'cancelled')
            ->where('id', '!=', $appointment->id) // Exclude current appointment
            ->first();

        if ($existingAppointment) {
            return back()->withErrors(['appointment_time' => 'This time slot is no longer available.']);
        }

        $appointment->update([
            'service_id' => $service->id,
            'employee_id' => $request->employee_id,
            'appointment_datetime' => $appointmentDateTime,
            'end_time' => $appointmentDateTime->copy()->addMinutes($service->duration_minutes),
            'duration_in_minutes' => $service->duration_minutes,
            'patient_notes' => $request->patient_notes,
        ]);

        return redirect()->route('patient.appointments')
            ->with('success', 'Appointment updated successfully!');
    }

    /**
     * Cancel appointment
     */
    public function cancelAppointment($appointmentId)
    {
        $user = auth()->user();
        $appointment = Appointment::where('patient_id', $user->id)
            ->where('id', $appointmentId)
            ->where('status', 'scheduled')
            ->firstOrFail();

        $appointment->update(['status' => 'cancelled']);

        if ($appointment->status === 'cancelled') {
            event(new \App\Events\AppointmentCancelled($appointment));
        }

        return redirect()->route('patient.appointments')
            ->with('success', 'Appointment cancelled successfully!');
    }

    /**
     * Show patient medical records
     */
    public function medicalRecords()
    {
        $user = auth()->user();

        // Get prenatal records
        $prenatalRecords = PrenatalRecord::where('patient_id', $user->id)
            ->with(['attendingPhysician', 'midwife'])
            ->orderBy('visit_date', 'desc')
            ->paginate(10);

        // Get postnatal records
        $postnatalRecords = \App\Models\PostnatalRecord::where('patient_id', $user->id)
            ->with('provider')
            ->orderBy('visit_date', 'desc')
            ->paginate(10);

        // Get postpartum records
        $postpartumRecords = \App\Models\PostpartumRecord::where('patient_id', $user->id)
            ->with('provider')
            ->orderBy('visit_date', 'desc')
            ->paginate(10);

        // Get delivery records
        $deliveryRecords = \App\Models\DeliveryRecord::where('patient_id', $user->id)
            ->with(['attendingProvider', 'deliveringProvider'])
            ->orderBy('delivery_date_time', 'desc')
            ->paginate(10);

        // Get prescriptions
        $prescriptions = Prescription::whereHas('treatment', function ($query) use ($user) {
            $query->where('patient_id', $user->id);
        })
            ->with(['inventory', 'prescriber', 'dispenser', 'payment', 'treatment'])
            ->where('status', '!=', 'cancelled')
            ->orderBy('dispensed_date', 'desc')
            ->paginate(10);

        // Get laboratory results
        $labResults = LaboratoryResult::where('patient_id', $user->id)
            ->with(['orderingProvider', 'performingTechnician', 'reviewingProvider'])
            ->orderBy('test_ordered_date_time', 'desc')
            ->paginate(10);

        return view('patient.medical-records', compact(
            'user',
            'prenatalRecords',
            'postnatalRecords',
            'postpartumRecords',
            'deliveryRecords',
            'prescriptions',
            'labResults'
        ));
    }

    /**
     * Show patient laboratory results
     */
    public function laboratoryResults()
    {
        $user = auth()->user();

        $labResults = LaboratoryResult::where('patient_id', $user->id)
            ->with(['orderingProvider', 'performingTechnician', 'reviewingProvider'])
            ->orderBy('test_ordered_date_time', 'desc')
            ->paginate(15);

        return view('patient.laboratory-results', compact('user', 'labResults'));
    }

    /**
     * Show patient billing information
     */
    public function billing()
    {
        $user = auth()->user();

        // Base query for billing records
        $billingQuery = Billing::where('patient_id', $user->id)
            ->orderBy('invoice_date', 'desc');

        // Get billing statistics from full dataset
        $totalBilled = $billingQuery->sum('total_amount');
        $totalPaid = $billingQuery->sum('amount_paid');
        $totalOutstanding = $billingQuery->sum('balance_due');
        $overdueCount = $billingQuery->where('payment_status', 'overdue')->count();

        // Get paginated records for display (use fresh query to avoid where clause from above affecting results)
        $billingRecords = Billing::where('patient_id', $user->id)
            ->orderBy('invoice_date', 'desc')
            ->paginate(10);

        return view('patient.billing', compact(
            'user',
            'billingRecords',
            'totalBilled',
            'totalPaid',
            'totalOutstanding',
            'overdueCount'
        ));
    }

    /**
     * Show patient documents
     */
    public function documents()
    {
        $user = auth()->user();

        $documents = Document::where('patient_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('patient.documents', compact('user', 'documents'));
    }

    /**
     * Download a document
     */
    public function downloadDocument($documentId)
    {
        $user = auth()->user();
        $document = Document::where('patient_id', $user->id)
            ->where('id', $documentId)
            ->firstOrFail();

        // Update download count and last accessed
        $document->increment('download_count');
        $document->update([
            'last_accessed_at' => now(),
            'last_accessed_by' => $user->id,
        ]);

        return response()->download(storage_path('app/public/'.$document->file_path), $document->file_name);
    }

    /**
     * View a specific laboratory result
     */
    public function viewLabResult($resultId)
    {
        $user = auth()->user();
        $labResult = LaboratoryResult::where('patient_id', $user->id)
            ->where('id', $resultId)
            ->with(['orderingProvider', 'performingTechnician', 'reviewingProvider'])
            ->firstOrFail();

        return view('patient.lab-result-detail', compact('user', 'labResult'));
    }

    /**
     * View a specific billing record
     */
    public function viewBill($billingId)
    {
        $user = auth()->user();
        $billing = Billing::where('patient_id', $user->id)
            ->where('id', $billingId)
            ->with('patient')
            ->firstOrFail();

        return view('patient.billing-detail', compact('user', 'billing'));
    }

    /**
     * Print billing receipt
     */
    public function printReceipt($billingId)
    {
        $user = auth()->user();
        $billing = Billing::where('patient_id', $user->id)
            ->where('id', $billingId)
            ->with('patient')
            ->firstOrFail();

        return view('patient.billing-receipt', compact('user', 'billing'));
    }

    /**
     * Choose purchase location for prescription
     */
    public function choosePurchaseLocation(Request $request, $prescriptionId)
    {
        $request->validate([
            'purchase_location' => 'required|in:clinic,outside',
        ]);

        $user = auth()->user();
        $prescription = Prescription::whereHas('treatment', function ($query) use ($user) {
            $query->where('patient_id', $user->id);
        })->findOrFail($prescriptionId);

        // Ensure prescription is in prescribed state
        if ($prescription->status !== 'prescribed') {
            return redirect()->back()->with('error', 'This prescription is not eligible for location selection.');
        }

        $location = $request->purchase_location;

        if ($location === 'clinic') {
            // Check if enough stock
            if ($prescription->inventory->current_quantity < $prescription->quantity_prescribed) {
                return redirect()->back()->with('error', 'Insufficient stock in clinic for this prescription.');
            }

            // Dispense the medication
            try {
                $prescription->dispense($prescription->quantity_prescribed, null, 'Patient chose to purchase from clinic');

                // Ensure total_price is calculated and preserved
                if (! $prescription->total_price) {
                    $prescription->calculateTotalPrice();
                }

                $prescription->update([
                    'purchase_location' => 'clinic',
                    'status' => 'fully_dispensed',
                ]);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to dispense medication: '.$e->getMessage());
            }

            // Redirect to payment
            return redirect()->route('patient.prescriptions.pay', $prescription->id)
                ->with('success', 'Medication dispensed. Please proceed with payment.');
        } else {
            // External purchase
            $prescription->update([
                'purchase_location' => 'outside',
                'status' => 'external_purchase',
            ]);

            return redirect()->route('patient.medical-records')
                ->with('success', 'Prescription marked for external purchase.');
        }
    }

    /**
     * View prescription details for printing
     */
    public function viewPrescription($prescriptionId)
    {
        $user = auth()->user();
        $prescription = Prescription::whereHas('treatment', function ($query) use ($user) {
            $query->where('patient_id', $user->id);
        })
            ->with(['inventory', 'prescriber', 'treatment.patient'])
            ->findOrFail($prescriptionId);

        // Ensure prescription is accessible
        if ($prescription->purchase_location === 'clinic' && (! $prescription->payment || ! $prescription->payment->isCompleted())) {
            return redirect()->route('patient.medical-records')
                ->with('error', 'Prescription is not available for viewing until payment is completed.');
        }

        if ($prescription->purchase_location === 'outside' && $prescription->status !== 'external_purchase') {
            return redirect()->route('patient.medical-records')
                ->with('error', 'Prescription is not available for viewing.');
        }

        return view('patient.prescription-detail', compact('prescription'));
    }
}
