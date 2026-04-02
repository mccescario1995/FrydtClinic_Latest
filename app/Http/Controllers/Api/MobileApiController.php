<?php

namespace App\Http\Controllers\Api;

use App\Models\Appointment;
use App\Models\Billing;
use App\Models\LaboratoryResult;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MobileApiController extends \Illuminate\Routing\Controller
{
    /**
     * Authenticate user for mobile app
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('MobileApp')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'role' => $user->getRoleNames()->first(),
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    /**
     * Get patient dashboard data
     */
    public function getPatientDashboard(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('Patient')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $patientProfile = $user->patientProfile;

        // Get upcoming appointments
        $upcomingAppointments = Appointment::where('patient_id', $patientProfile->id)
            ->where('appointment_datetime', '>=', now())
            ->where('status', 'scheduled')
            ->with(['service', 'employee'])
            ->orderBy('appointment_datetime')
            ->limit(5)
            ->get();

        // Get recent lab results
        $recentLabResults = LaboratoryResult::where('patient_id', $patientProfile->id)
            ->where('result_available_date_time', '>=', now()->subDays(30))
            ->with(['orderingProvider'])
            ->orderBy('result_available_date_time', 'desc')
            ->limit(5)
            ->get();

        // Get billing summary
        $billingSummary = Billing::where('patient_id', $patientProfile->id)
            ->selectRaw('
                COUNT(*) as total_bills,
                SUM(CASE WHEN payment_status = "paid" THEN 1 ELSE 0 END) as paid_bills,
                SUM(CASE WHEN payment_status != "paid" THEN balance_due ELSE 0 END) as outstanding_balance
            ')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'patient' => $patientProfile,
                'upcoming_appointments' => $upcomingAppointments,
                'recent_lab_results' => $recentLabResults,
                'billing_summary' => $billingSummary,
            ]
        ]);
    }

    /**
     * Get patient appointments
     */
    public function getPatientAppointments(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('Patient')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $patientProfile = $user->patientProfile;

        $appointments = Appointment::where('patient_id', $patientProfile->id)
            ->with(['service', 'employee'])
            ->orderBy('appointment_datetime', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }

    /**
     * Get patient lab results
     */
    public function getPatientLabResults(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('Patient')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $patientProfile = $user->patientProfile;

        $labResults = LaboratoryResult::where('patient_id', $patientProfile->id)
            ->with(['orderingProvider', 'performingTechnician'])
            ->orderBy('test_ordered_date_time', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $labResults
        ]);
    }

    /**
     * Get patient billing history
     */
    public function getPatientBilling(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('Patient')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $patientProfile = $user->patientProfile;

        $billings = Billing::where('patient_id', $patientProfile->id)
            ->orderBy('invoice_date', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $billings
        ]);
    }

    /**
     * Book appointment (for patients)
     */
    public function bookAppointment(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('Patient')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'employee_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $patientProfile = $user->patientProfile;

        // Check if slot is available
        $appointmentDateTime = $request->appointment_date . ' ' . $request->appointment_time;

        $existingAppointment = Appointment::where('employee_id', $request->employee_id)
            ->where('appointment_datetime', $appointmentDateTime)
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existingAppointment) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot is not available'
            ], 409);
        }

        $appointment = Appointment::create([
            'patient_id' => $patientProfile->id,
            'employee_id' => $request->employee_id,
            'service_id' => $request->service_id,
            'appointment_datetime' => $appointmentDateTime,
            'status' => 'scheduled',
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment booked successfully',
            'data' => $appointment->load(['service', 'employee'])
        ]);
    }

    /**
     * Get available services
     */
    public function getServices(Request $request)
    {
        $services = \App\Models\Service::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'base_price', 'philhealth_price', 'duration_minutes']);

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Get available doctors/employees
     */
    public function getDoctors(Request $request)
    {
        $doctors = User::role(['Doctor', 'Employee'])
            ->with('employee_profile')
            ->get(['id', 'name', 'email']);

        return response()->json([
            'success' => true,
            'data' => $doctors
        ]);
    }

    /**
     * Update patient profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('Patient')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $patientProfile = $user->patientProfile;

        if ($patientProfile) {
            $patientProfile->update($request->only([
                'phone', 'address', 'emergency_contact_name', 'emergency_contact_phone'
            ]));
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $patientProfile
        ]);
    }
}
