<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppointmentBookingController extends Controller
{
    /**
     * Show the appointment booking form.
     *
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        // Get all available services
        $services = Service::all();
        $userRole     = backpack_auth()->user();
        $user = Auth::id();

        // Check if user has patient role
        if (! $userRole->hasRole('Patient')) {
            return redirect()->route('dashboard')->with('error', 'Only patients can book appointments through this form.');
        }

        return view('appointments.index', compact('services', 'user'));
    }

    /**
     * Get available employees based on service and date.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableEmployees(Request $request)
    {
        try {
            $request->validate([
                'service_id'       => 'required|exists:services,id',
                'appointment_date' => 'required|date|after:today',
            ]);

            $service   = Service::findOrFail($request->service_id);
            $date      = Carbon::parse($request->appointment_date);
            $dayOfWeek = $date->dayOfWeekIso; // 1 for Monday, 7 for Sunday

            // Find employees with a schedule on the selected day
            $availableEmployees = User::role(['Employee', 'Doctor'], 'web')
                ->whereHas('schedules', function ($query) use ($dayOfWeek) {
                    $query->where('day_of_week', $dayOfWeek);
                })
                ->with(['employee_profile', 'schedules' => function ($query) use ($dayOfWeek) {
                    $query->where('day_of_week', $dayOfWeek);
                }])
                ->get()
                ->map(function ($employee) {
                    return [
                        'id'       => $employee->id,
                        'name'     => $employee->name,
                        'email'    => $employee->email,
                        'position' => $employee->employee_profile->position ?? 'Healthcare Provider',
                        'schedule' => $employee->schedules->first(),
                    ];
                });

            return response()->json($availableEmployees);

        } catch (\Exception $e) {
            Log::error('Error getting available employees: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to load available providers.'], 500);
        }
    }

    /**
     * Get available time slots for a specific employee and date.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableTimes(Request $request)
    {
        try {
            $request->validate([
                'employee_id'      => 'required|exists:users,id',
                'service_id'       => 'required|exists:services,id',
                'appointment_date' => 'required|date|after:today',
            ]);

            $employee  = User::findOrFail($request->employee_id);
            $service   = Service::findOrFail($request->service_id);
            $date      = Carbon::parse($request->appointment_date);
            $dayOfWeek = $date->dayOfWeekIso;

            // Get employee's schedule for the day
            $schedule = $employee->schedules()->where('day_of_week', $dayOfWeek)->first();

            if (! $schedule) {
                return response()->json(['error' => 'Employee not available on this day.'], 404);
            }

            $startTime           = Carbon::parse($schedule->start_time);
            $endTime             = Carbon::parse($schedule->end_time);
            $appointmentDuration = $service->duration_minutes;

            // Get existing appointments for the employee on that date
            $existingAppointments = Appointment::where('employee_id', $employee->id)
                ->whereDate('appointment_datetime', $date->toDateString())
                ->where('status', '!=', 'cancelled')
                ->get();

            $availableTimes = [];
            $currentTime    = $startTime->copy();

            // Generate time slots based on service duration
            while ($currentTime->copy()->addMinutes($appointmentDuration)->lte($endTime)) {
                $slotStart       = $currentTime->copy();
                $slotEnd         = $currentTime->copy()->addMinutes($appointmentDuration);
                $isSlotAvailable = true;

                // Check for overlap with existing appointments
                foreach ($existingAppointments as $appointment) {
                    $existingStart = Carbon::parse($appointment->appointment_datetime);
                    $existingEnd   = $existingStart->copy()->addMinutes($appointment->duration_in_minutes);

                    // Check if there's any overlap
                    if (
                        ($slotStart->gte($existingStart) && $slotStart->lt($existingEnd)) ||
                        ($slotEnd->gt($existingStart) && $slotEnd->lte($existingEnd)) ||
                        ($slotStart->lt($existingStart) && $slotEnd->gt($existingEnd))
                    ) {
                        $isSlotAvailable = false;
                        break;
                    }
                }

                if ($isSlotAvailable) {
                    $availableTimes[] = [
                        'time'     => $slotStart->format('H:i'),
                        'display'  => $slotStart->format('g:i A'),
                        'end_time' => $slotEnd->format('H:i'),
                    ];
                }

                // Move to next slot (30-minute intervals)
                $currentTime->addMinutes(30);
            }

            return response()->json($availableTimes);

        } catch (\Exception $e) {
            Log::error('Error getting available times: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to load available times.'], 500);
        }
    }

    /**
     * Store a newly created appointment in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'service_id'       => 'required|exists:services,id',
                'employee_id'      => 'required|exists:users,id',
                'appointment_date' => 'required|date|after:today',
                'appointment_time' => 'required|date_format:H:i',
                'patient_notes'    => 'nullable|string|max:1000',
            ], [
                'service_id.required'          => 'Please select a service.',
                'employee_id.required'         => 'Please select a healthcare provider.',
                'appointment_date.required'    => 'Please select an appointment date.',
                'appointment_date.after'       => 'Appointment date must be in the future.',
                'appointment_time.required'    => 'Please select an appointment time.',
                'appointment_time.date_format' => 'Invalid time format.',
            ]);

            // Start database transaction
            DB::beginTransaction();

            $service  = Service::findOrFail($validatedData['service_id']);
            $employee = User::findOrFail($validatedData['employee_id']);

            // Combine date and time
            $appointmentDateTime = Carbon::createFromFormat(
                'Y-m-d H:i',
                $validatedData['appointment_date'] . ' ' . $validatedData['appointment_time']
            );

            // Check if the appointment is at least 1 hour in the future
            if ($appointmentDateTime->lte(Carbon::now()->addHour())) {
                return back()->withErrors([
                    'appointment_time' => 'Appointment must be at least 1 hour in the future.',
                ])->withInput();
            }

            // Verify employee is available at this time
            $dayOfWeek        = $appointmentDateTime->dayOfWeekIso;
            $employeeSchedule = $employee->schedules()
                ->where('day_of_week', $dayOfWeek)
                ->first();

            if (! $employeeSchedule) {
                return back()->withErrors([
                    'employee_id' => 'Selected healthcare provider is not available on this day.',
                ])->withInput();
            }

            // Check if time falls within employee's schedule
            $scheduleStart  = Carbon::parse($employeeSchedule->start_time);
            $scheduleEnd    = Carbon::parse($employeeSchedule->end_time);
            $appointmentEnd = $appointmentDateTime->copy()->addMinutes($service->duration_minutes);

            if (
                $appointmentDateTime->format('H:i') < $scheduleStart->format('H:i') ||
                $appointmentEnd->format('H:i') > $scheduleEnd->format('H:i')
            ) {
                return back()->withErrors([
                    'appointment_time' => 'Selected time is outside provider\'s working hours.',
                ])->withInput();
            }

            // Check for conflicts with existing appointments
            $existingAppointment = Appointment::where('employee_id', $validatedData['employee_id'])
                ->whereDate('appointment_datetime', $appointmentDateTime->toDateString())
                ->where('status', '!=', 'cancelled')
                ->get();

            foreach ($existingAppointment as $appointment) {
                $existingStart = Carbon::parse($appointment->appointment_datetime);
                $existingEnd   = $existingStart->copy()->addMinutes($appointment->duration_in_minutes);

                if (
                    ($appointmentDateTime->gte($existingStart) && $appointmentDateTime->lt($existingEnd)) ||
                    ($appointmentEnd->gt($existingStart) && $appointmentEnd->lte($existingEnd)) ||
                    ($appointmentDateTime->lt($existingStart) && $appointmentEnd->gt($existingEnd))
                ) {
                    return back()->withErrors([
                        'appointment_time' => 'The selected time slot is already booked. Please choose another time.',
                    ])->withInput();
                }
            }

            // Check if patient already has an appointment on the same day
            $patientExistingAppointment = Appointment::where('patient_id', Auth::id())
                ->whereDate('appointment_datetime', $appointmentDateTime->toDateString())
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($patientExistingAppointment) {
                return back()->withErrors([
                    'appointment_date' => 'You already have an appointment scheduled for this date.',
                ])->withInput();
            }

            // Calculate end time
            $endTime = $appointmentDateTime->copy()->addMinutes($service->duration_minutes);

            // Create the appointment
            $appointment = Appointment::create([
                'patient_id'           => Auth::id(),
                'employee_id'          => $validatedData['employee_id'],
                'service_id'           => $validatedData['service_id'],
                'appointment_datetime' => $appointmentDateTime,
                'end_time'             => $endTime,
                'duration_in_minutes'  => $service->duration_minutes,
                'patient_notes'        => $validatedData['patient_notes'],
                'status'               => 'scheduled',
                'type'                 => 'new_patient', // Default type
            ]);

            // Commit the transaction
            DB::commit();

            // Log the successful booking
            Log::info('Appointment booked successfully', [
                'appointment_id'       => $appointment->id,
                'patient_id'           => Auth::id(),
                'employee_id'          => $validatedData['employee_id'],
                'service_id'           => $validatedData['service_id'],
                'appointment_datetime' => $appointmentDateTime->toDateTimeString(),
            ]);

            return redirect()->route('appointment.booking.form')->with('success',
                'Appointment successfully booked! Your appointment is scheduled for ' .
                $appointmentDateTime->format('F j, Y \a\t g:i A') . ' with ' . $employee->name . '.'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error booking appointment: ' . $e->getMessage(), [
                'user_id'      => Auth::id(),
                'request_data' => $request->all(),
            ]);

            return back()->with('error',
                'There was an error booking your appointment. Please try again or contact support.'
            )->withInput();
        }
    }

    /**
     * Show patient's appointments
     *
     * @return \Illuminate\View\View
     */
    public function myAppointments()
    {
        $appointments = Appointment::where('patient_id', Auth::id())
            ->with(['employee', 'service'])
            ->orderBy('appointment_datetime', 'desc')
            ->paginate(10);

        return view('appointments.my_appointments', compact('appointments'));
    }

    /**
     * Cancel an appointment
     *
     * @param Request $request
     * @param int $appointmentId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Request $request, $appointmentId)
    {
        try {
            $appointment = Appointment::where('id', $appointmentId)
                ->where('patient_id', Auth::id())
                ->where('status', 'scheduled')
                ->firstOrFail();

            // Check if appointment can be cancelled (at least 24 hours before)
            $appointmentTime = Carbon::parse($appointment->appointment_datetime);
            if ($appointmentTime->lte(Carbon::now()->addHours(24))) {
                return back()->with('error',
                    'Appointments can only be cancelled at least 24 hours in advance.'
                );
            }

            // Update appointment status
            $appointment->update([
                'status'        => 'cancelled',
                'patient_notes' => ($appointment->patient_notes ?? '') . "\n\nCancelled by patient on " . now()->format('Y-m-d H:i:s'),
            ]);

            Log::info('Appointment cancelled by patient', [
                'appointment_id' => $appointmentId,
                'patient_id'     => Auth::id(),
            ]);

            return back()->with('success', 'Appointment cancelled successfully.');

        } catch (\Exception $e) {
            Log::error('Error cancelling appointment: ' . $e->getMessage());
            return back()->with('error', 'Unable to cancel appointment. Please contact support.');
        }
    }

    /**
     * Get appointment details for AJAX requests
     *
     * @param int $appointmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAppointmentDetails($appointmentId)
    {
        try {
            $appointment = Appointment::where('id', $appointmentId)
                ->where('patient_id', Auth::id())
                ->with(['employee', 'service'])
                ->firstOrFail();

            return response()->json([
                'success'     => true,
                'appointment' => [
                    'id'                   => $appointment->id,
                    'service_name'         => $appointment->service->name,
                    'employee_name'        => $appointment->employee->name,
                    'appointment_datetime' => $appointment->appointment_datetime->format('Y-m-d H:i:s'),
                    'duration'             => $appointment->duration_in_minutes,
                    'status'               => $appointment->status,
                    'patient_notes'        => $appointment->patient_notes,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found',
            ], 404);
        }
    }
}
