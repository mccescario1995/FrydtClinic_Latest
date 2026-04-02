<?php

namespace App\Services;

use App\Models\User;
use App\Models\Appointment;
use App\Models\LaboratoryResult;
use App\Models\Billing;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send appointment reminder to patient
     */
    public function sendAppointmentReminder(Appointment $appointment)
    {
        try {
            $patient = $appointment->patient;

            Mail::to($patient->email)->send(new \App\Mail\AppointmentReminder($appointment));

            Log::info('Appointment reminder sent', [
                'appointment_id' => $appointment->id,
                'patient_id' => $patient->id,
                'patient_email' => $patient->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send appointment reminder', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send lab results notification to patient
     */
    public function sendLabResultsNotification(LaboratoryResult $labResult)
    {
        try {
            $patient = $labResult->patient;

            Mail::to($patient->email)->send(new \App\Mail\LabResultsAvailable($labResult));

            Log::info('Lab results notification sent', [
                'lab_result_id' => $labResult->id,
                'patient_id' => $patient->id,
                'patient_email' => $patient->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send lab results notification', [
                'lab_result_id' => $labResult->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send billing statement to patient
     */
    public function sendBillingStatement(Billing $billing)
    {
        try {
            $patient = $billing->patient;

            Mail::to($patient->email)->send(new \App\Mail\BillingStatement($billing));

            Log::info('Billing statement sent', [
                'billing_id' => $billing->id,
                'patient_id' => $patient->id,
                'patient_email' => $patient->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send billing statement', [
                'billing_id' => $billing->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send payment confirmation
     */
    public function sendPaymentConfirmation(Billing $billing)
    {
        try {
            $patient = $billing->patient;

            Mail::to($patient->email)->send(new \App\Mail\PaymentConfirmation($billing));

            Log::info('Payment confirmation sent', [
                'billing_id' => $billing->id,
                'patient_id' => $patient->id,
                'patient_email' => $patient->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation', [
                'billing_id' => $billing->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send overdue payment reminder
     */
    public function sendOverduePaymentReminder(Billing $billing)
    {
        try {
            $patient = $billing->patient;

            Mail::to($patient->email)->send(new \App\Mail\OverduePaymentReminder($billing));

            Log::info('Overdue payment reminder sent', [
                'billing_id' => $billing->id,
                'patient_id' => $patient->id,
                'patient_email' => $patient->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send overdue payment reminder', [
                'billing_id' => $billing->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send welcome email to new patient
     */
    public function sendWelcomeEmail(User $user)
    {
        try {
            Mail::to($user->email)->send(new \App\Mail\WelcomeEmail($user));

            Log::info('Welcome email sent', [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send welcome email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send staff notification for urgent matters
     */
    public function sendStaffNotification($subject, $message, $recipients = null)
    {
        try {
            $staff = $recipients ?: User::role(['Doctor', 'Employee'])->get();

            foreach ($staff as $member) {
                Mail::to($member->email)->send(new \App\Mail\StaffNotification($subject, $message, $member));
            }

            Log::info('Staff notification sent', [
                'subject' => $subject,
                'recipient_count' => $staff->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send staff notification', [
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
        }
    }
}
