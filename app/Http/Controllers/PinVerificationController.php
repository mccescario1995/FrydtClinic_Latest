<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PinVerificationController extends Controller
{
    /**
     * Show OTP verification form
     */
    public function show()
    {
        if (! backpack_auth()->check()) {
            return redirect()->route('backpack.auth.login');
        }

        $user = backpack_auth()->user();

        // Check if user has a phone number
        $phone = $this->getUserPhone($user);
        if (! $phone || $phone === 'Not provided') {
            return redirect()->route('set-phone')->with('error', 'Please set your phone number first.');
        }

        // If OTP is already verified for this session, redirect to dashboard
        if ($user->otp_verified_at && $user->otp_verified_at->isToday()) {
            return $this->redirectToDashboard();
        }

        // Send OTP if not sent recently or expired
        if (! $user->otp_code || $user->isOtpExpired()) {
            if (! $user->sendOtp()) {
                return redirect()->route('set-phone')->with('error', 'Unable to send OTP. Please check your phone number.');
            }
        }

        return view('auth.verify-otp', [
            'otp_expires_at' => $user->otp_expires_at,
            'can_resend' => $user->canResendOtp(),
            'phone' => $this->maskPhoneNumber($phone),
        ]);
    }

    /**
     * Verify OTP
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $user = backpack_auth()->user();

        if ($user->isOtpExpired()) {
            return back()->withErrors(['otp' => 'OTP has expired. Please request a new one.']);
        }

        if ($user->otp_attempts >= 3) {
            return back()->withErrors(['otp' => 'Too many failed attempts. Please request a new OTP.']);
        }

        if ($user->verifyOtp($request->otp)) {
            return $this->redirectToDashboard()
                ->with('success', 'OTP verified successfully.');
        }

        return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $user = backpack_auth()->user();

        if (! $user->canResendOtp()) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait 30 seconds before requesting a new OTP.',
            ]);
        }

        if ($user->sendOtp()) {
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully.',
                'expires_at' => $user->otp_expires_at->toISOString(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to send OTP. Please try again.',
        ]);
    }

    /**
     * Show set phone form
     */
    public function showSetPhone()
    {
        if (! backpack_auth()->check()) {
            return redirect()->route('backpack.auth.login');
        }

        $user = backpack_auth()->user();

        return view('auth.set-phone', compact('user'));
    }

    /**
     * Set phone number
     */
    public function setPhone(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'phone' => 'required|philippine_phone|unique_patient_phone|unique_employee_phone',
        ]);



        $user = backpack_auth()->user();

        // Update phone in appropriate profile
        if ($user->user_type === 'patient') {

            if ($user->patientProfile) {
                $user->patientProfile->update(['phone' => $request->phone]);
            }elseif (! $user->patientProfile) {
                $user->patientProfile()->create(['phone' => $request->phone]);
            }
        } elseif ($user->user_type === 'employee') {
            if (! $user->employeeProfile) {
                $user->employeeProfile()->create(['phone' => $request->phone]);
            } else {
                $user->employeeProfile->update(['phone' => $request->phone]);
            }
        }

        return redirect()->route('verify-otp')
            ->with('success', 'Phone number set successfully. Please verify with OTP.');
    }

    /**
     * Get user's phone number
     */
    private function getUserPhone($user)
    {
        if ($user->user_type === 'patient' && $user->patientProfile) {
            return $user->patientProfile->phone;
        } elseif ($user->user_type === 'employee' && $user->employeeProfile) {
            return $user->employeeProfile->phone;
        }

        return null;
    }

    /**
     * Mask phone number for display
     */
    private function maskPhoneNumber($phone)
    {
        if (! $phone) {
            return '';
        }

        // Remove country code for display
        $phone = preg_replace('/^\+?63/', '', $phone);

        // Mask middle digits
        if (strlen($phone) >= 4) {
            return substr($phone, 0, 2).'****'.substr($phone, -2);
        }

        return $phone;
    }

    /**
     * Redirect to appropriate dashboard based on user type
     */
    private function redirectToDashboard()
    {
        $user = backpack_auth()->user();

        switch ($user->user_type) {
            case 'admin':
                return redirect()->route('admin-portal.dashboard');
            case 'employee':
                return redirect()->route('employee.dashboard');
            case 'patient':
                return redirect()->route('patient.dashboard');
            default:
                return redirect('/dashboard');
        }
    }
}
