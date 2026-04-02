<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\SmsLog;

class SmsService
{
    protected $apiToken;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiToken = \App\Models\Setting::get('iprogsms_token');
        $this->apiUrl = \App\Models\Setting::get('iprogsms_url', 'https://www.iprogsms.com/api/v1/sms_messages');
    }

    /**
     * Send SMS message
     *
     * @param string $to Phone number to send to
     * @param string $message Message content
     * @param array $options Additional options (type, user_id, sent_by, metadata)
     * @return bool Success status
     */
    public function sendSms($to, $message, $options = [])
    {
        // Format phone number (ensure it starts with +)
        $formattedTo = $this->formatPhoneNumber($to);

        // Create SMS log entry
        $smsLog = SmsLog::create([
            'phone_number' => $formattedTo,
            'message' => $message,
            'sms_type' => $options['type'] ?? 'general',
            'user_id' => $options['user_id'] ?? null,
            'sent_by' => $options['sent_by'] ?? null,
            'metadata' => $options['metadata'] ?? null,
            'status' => 'pending',
        ]);

        try {
            // Check if iProgSMS is configured
            if (!$this->apiToken) {
                $error = 'iProgSMS service not configured - missing API token';
                Log::warning($error);

                $smsLog->update([
                    'status' => 'failed',
                    'error_message' => $error,
                ]);

                return false;
            }

            // Prepare iProgSMS API request
            $sendData = [
                'api_token' => $this->apiToken,
                'phone_number' => $formattedTo,
                'message' => $message,
                'sender_name' => "kaprets",
            ];

            // Send SMS via iProgSMS API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->withOptions([
                'verify' => false, // Disable SSL verification for development/testing
            ])->post($this->apiUrl, $sendData);

            if ($response->successful()) {
                $responseData = $response->json();

                // Update log with success
                $smsLog->update([
                    'status' => 'sent',
                    'twilio_sid' => $responseData['message_id'] ?? null, // PhilSMS might return message_id
                    'sent_at' => now(),
                    'metadata' => array_merge($smsLog->metadata ?? [], [
                        'api_response' => $responseData
                    ])
                ]);

                Log::info('SMS sent successfully via iProgSMS', [
                    'to' => $formattedTo,
                    'response' => $responseData,
                    'type' => $options['type'] ?? 'general'
                ]);

                return true;
            } else {
                $errorMessage = 'iProgSMS API error: ' . $response->status() . ' - ' . $response->body();

                $smsLog->update([
                    'status' => 'failed',
                    'error_message' => $errorMessage,
                ]);

                Log::error('iProgSMS API error', [
                    'to' => $formattedTo,
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'type' => $options['type'] ?? 'general'
                ]);

                return false;
            }

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            // Update log with failure
            $smsLog->update([
                'status' => 'failed',
                'error_message' => $errorMessage,
            ]);

            Log::error('SMS sending failed', [
                'to' => $formattedTo,
                'error' => $errorMessage,
                'type' => $options['type'] ?? 'general'
            ]);

            return false;
        }
    }

    /**
     * Send appointment booking SMS
     */
    public function sendAppointmentBooking($appointment)
    {
        $patient = $appointment->patient;
        $phone = $patient->patientProfile->phone ?? null;

        if (!$phone || $phone === 'Not provided') {
            Log::info('No phone number available for appointment booking SMS', ['appointment_id' => $appointment->id]);
            return false;
        }

        $message = "FRYDT Clinic: Appointment booked successfully!\n" .
                   "Date: " . $appointment->appointment_datetime->format('M d, Y h:i A') . "\n" .
                   "Service: " . $appointment->service->name . "\n" .
                   "Provider: " . $appointment->employee->name . "\n" .
                   "Status: Scheduled - Please arrive 15 minutes early.";

        return $this->sendSms($phone, $message, [
            'type' => 'appointment_booking',
            'user_id' => $patient->id,
            'metadata' => [
                'appointment_id' => $appointment->id,
                'service' => $appointment->service->name,
                'provider' => $appointment->employee->name,
                'datetime' => $appointment->appointment_datetime->toISOString(),
            ]
        ]);
    }

    /**
     * Send appointment cancellation SMS
     */
    public function sendAppointmentCancellation($appointment)
    {
        $patient = $appointment->patient;
        $phone = $patient->patientProfile->phone ?? null;

        if (!$phone || $phone === 'Not provided') {
            Log::info('No phone number available for appointment cancellation SMS', ['appointment_id' => $appointment->id]);
            return false;
        }

        $message = "Your appointment with " . $appointment->employee->name . " for " . $appointment->service->name . " on " . $appointment->appointment_datetime->format('M d, Y') . " at " . $appointment->appointment_datetime->format('g:i A') . " has been cancelled.";

        return $this->sendSms($phone, $message, [
            'type' => 'appointment_cancellation',
            'user_id' => $patient->id,
            'metadata' => [
                'appointment_id' => $appointment->id,
                'service' => $appointment->service->name,
                'provider' => $appointment->employee->name,
                'datetime' => $appointment->appointment_datetime->toISOString(),
            ]
        ]);
    }

    /**
     * Send appointment confirmation SMS
     */
    public function sendAppointmentConfirmation($appointment)
    {
        $patient = $appointment->patient;
        $phone = $patient->patientProfile->phone ?? null;

        if (!$phone || $phone === 'Not provided') {
            Log::info('No phone number available for appointment SMS', ['appointment_id' => $appointment->id]);
            return false;
        }

        $message = "FRYDT Clinic: Your appointment has been confirmed!\n" .
                   "Date: " . $appointment->appointment_datetime->format('M d, Y h:i A') . "\n" .
                   "Service: " . $appointment->service->name . "\n" .
                   "Provider: " . $appointment->employee->name;

        return $this->sendSms($phone, $message, [
            'type' => 'appointment',
            'user_id' => $patient->id,
            'metadata' => [
                'appointment_id' => $appointment->id,
                'service' => $appointment->service->name,
                'provider' => $appointment->employee->name,
                'datetime' => $appointment->appointment_datetime->toISOString(),
            ]
        ]);
    }

    /**
     * Send appointment reminder SMS
     */
    public function sendAppointmentReminder($appointment)
    {
        $patient = $appointment->patient;
        $phone = $patient->patientProfile->phone ?? null;

        if (!$phone || $phone === 'Not provided') {
            Log::info('No phone number available for appointment reminder SMS', ['appointment_id' => $appointment->id]);
            return false;
        }

        $message = "FRYDT Clinic Reminder: You have an appointment tomorrow!\n" .
                   "Date: " . $appointment->appointment_datetime->format('M d, Y h:i A') . "\n" .
                   "Service: " . $appointment->service->name . "\n" .
                   "Provider: " . $appointment->employee->name;

        return $this->sendSms($phone, $message, [
            'type' => 'reminder',
            'user_id' => $patient->id,
            'metadata' => [
                'appointment_id' => $appointment->id,
                'service' => $appointment->service->name,
                'provider' => $appointment->employee->name,
                'datetime' => $appointment->appointment_datetime->toISOString(),
            ]
        ]);
    }

    /**
     * Send payment confirmation SMS
     */
    public function sendPaymentConfirmation($payment)
    {
        $patient = $payment->patient;
        $phone = $patient->patientProfile->phone ?? null;

        if (!$phone || $phone === 'Not provided') {
            Log::info('No phone number available for payment SMS', ['payment_id' => $payment->id]);
            return false;
        }

        $message = "FRYDT Clinic: Payment received successfully!\n" .
                   "Amount: ₱" . number_format($payment->amount, 2) . "\n" .
                   "Reference: " . ($payment->payment_reference ?? 'N/A') . "\n" .
                   "Thank you for choosing FRYDT Clinic!";

        return $this->sendSms($phone, $message, [
            'type' => 'payment',
            'user_id' => $patient->id,
            'metadata' => [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'reference' => $payment->payment_reference,
            ]
        ]);
    }



    /**
     * Send lab results notification SMS
     */
    public function sendLabResultsNotification($labResult)
    {
        $patient = $labResult->patient;
        $phone = $patient->patientProfile->phone ?? null;

        if (!$phone || $phone === 'Not provided') {
            Log::info('No phone number available for lab results SMS', ['lab_result_id' => $labResult->id]);
            return false;
        }

        $message = "FRYDT Clinic: Your lab results are ready!\n" .
                   "Test: " . $labResult->test_name . "\n" .
                   "Status: " . $labResult->result_status . "\n" .
                   "Please login to view detailed results.";

        return $this->sendSms($phone, $message, [
            'type' => 'lab_result',
            'user_id' => $patient->id,
            'metadata' => [
                'lab_result_id' => $labResult->id,
                'test_name' => $labResult->test_name,
                'result_status' => $labResult->result_status,
            ]
        ]);
    }

    /**
     * Send OTP verification SMS
     */
    public function sendOtp($phone, $otp, $options = [])
    {
        if (!$phone || $phone === 'Not provided') {
            Log::info('No phone number available for OTP SMS');
            return false;
        }

        $message = "[FRYDT Clinic] OTP: {$otp} for your account. Valid for 5 mins only. Never share this code.";

        return $this->sendSms($phone, $message, array_merge($options, [
            'type' => 'otp_verification'
        ]));
    }

    /**
     * Send general notification SMS
     */
    public function sendNotification($phone, $message, $options = [])
    {
        if (!$phone || $phone === 'Not provided') {
            Log::info('No phone number available for notification SMS');
            return false;
        }

        return $this->sendSms($phone, $message, array_merge($options, [
            'type' => $options['type'] ?? 'notification'
        ]));
    }

    /**
     * Format phone number for iProgSMS API (expects format: 63xxxxxxxxx)
     */
    private function formatPhoneNumber($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);

        // Handle different input formats
        if (str_starts_with($phone, '63') && strlen($phone) === 12) {
            // Already in correct format: 63xxxxxxxxx
            return $phone;
        } elseif (str_starts_with($phone, '+63') && strlen($phone) === 13) {
            // Remove + from +63xxxxxxxxx to get 63xxxxxxxxx
            return substr($phone, 1);
        } elseif (str_starts_with($phone, '0') && strlen($phone) === 11) {
            // Convert 0xxxxxxxxx to 63xxxxxxxxx
            return '63' . substr($phone, 1);
        } elseif (strlen($phone) === 10 && str_starts_with($phone, '9')) {
            // Convert 9xxxxxxxxx to 63xxxxxxxxx
            return '63' . $phone;
        } elseif (strlen($phone) === 11 && str_starts_with($phone, '9')) {
            // Handle 09xxxxxxxxx format (11 digits starting with 9 after 0)
            return '63' . substr($phone, 1);
        } else {
            // For any other format, try to add 63 prefix if it doesn't have it
            if (!str_starts_with($phone, '63')) {
                $phone = '63' . $phone;
            }
            return $phone;
        }
    }

    /**
     * Get SMS credits from iProgSMS API
     *
     * @return array|null Returns array with 'load_balance' or null on error
     */
    public function getCredits()
    {
        // Check if iProgSMS is configured
        if (!$this->apiToken) {
            Log::warning('iProgSMS service not configured - missing API token');
            return null;
        }

        try {
            // Make API call to get credits
            $response = Http::withOptions([
                'verify' => true, // Disable SSL verification for development/testing
            ])->get($this->apiUrl . '/account/sms_credits', [
                'api_token' => $this->apiToken
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['status']) && $responseData['status'] === 'success' && isset($responseData['data']['load_balance'])) {
                    Log::info('SMS credits retrieved successfully', [
                        'credits' => $responseData['data']['load_balance']
                    ]);

                    return [
                        'load_balance' => $responseData['data']['load_balance'],
                        'status' => 'success',
                        'message' => $responseData['message'] ?? 'Credits retrieved successfully'
                    ];
                } else {
                    Log::error('Invalid response format from iProgSMS credits API', [
                        'response' => $responseData
                    ]);
                    return null;
                }
            } else {
                $errorMessage = 'iProgSMS credits API error: ' . $response->status() . ' - ' . $response->body();
                Log::error('iProgSMS credits API error', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return null;
            }

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error('SMS credits retrieval failed', [
                'error' => $errorMessage
            ]);
            return null;
        }
    }

    /**
     * Check if SMS service is configured
     */
    public function isConfigured()
    {
        return !empty($this->apiToken);
    }
}
