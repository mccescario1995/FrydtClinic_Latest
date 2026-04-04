<?php

namespace App\Services;

use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class SmsService
{
    protected $apiKey;

    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('SEMAPHORE_API_KEY');
        $this->baseUrl = 'https://api.semaphore.co/api/v4';
    }

    /**
     * Send SMS message
     *
     * @param  string  $to  Phone number to send to
     * @param  string  $message  Message content
     * @param  array  $options  Additional options (type, user_id, sent_by, metadata)
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
            // Check if Semaphore is configured
            if (! $this->apiKey) {
                $error = 'Semaphore service not configured - missing API key';
                Log::warning($error);

                $smsLog->update([
                    'status' => 'failed',
                    'error_message' => $error,
                ]);

                return false;
            }

            // Rate limiting: 120 calls per minute
            // $key = 'semaphore-sms:'.$this->apiKey;
            // if (RateLimiter::tooManyAttempts($key, 120)) {
            //     $error = 'Rate limit exceeded: 120 SMS per minute';
            //     Log::warning($error);

            //     $smsLog->update([
            //         'status' => 'failed',
            //         'error_message' => $error,
            //     ]);

            //     return false;
            // }
            // RateLimiter::hit($key, 60); // 60 seconds decay

            // Prepare Semaphore API request
            $sendData = [
                'apikey' => $this->apiKey,
                'number' => $formattedTo,
                'message' => $message,
                'sendername' => 'FRYDT'
            ];

            // Send SMS via Semaphore API
            $response = Http::asForm()
                ->withOptions(['verify' => false])
                ->post($this->baseUrl.'/messages', $sendData);

            // dd('Response: ', $response->json(), 'Phone:' , $formattedTo);

            if ($response->successful()) {
                $responseData = $response->json();

                // Semaphore returns array of message objects, get first one
                $messageData = is_array($responseData) && count($responseData) > 0 ? $responseData[0] : $responseData;

                // Map Semaphore status to our status
                $statusMap = [
                    'Queued' => 'queued',
                    'Pending' => 'pending',
                    'Sent' => 'sent',
                    'Failed' => 'failed',
                    'Refunded' => 'refunded',
                ];
                $mappedStatus = $statusMap[$messageData['status'] ?? 'Sent'] ?? 'sent';

                // Update log with success
                $smsLog->update([
                    'status' => $mappedStatus,
                    'twilio_sid' => $messageData['message_id'] ?? null,
                    'sent_at' => now(),
                    'metadata' => array_merge($smsLog->metadata ?? [], [
                        'api_response' => $responseData,
                    ]),
                ]);

                Log::info('SMS sent successfully via Semaphore', [
                    'to' => $formattedTo,
                    'response' => $responseData,
                    'type' => $options['type'] ?? 'general',
                ]);

                return true;
            } else {
                $errorMessage = 'Semaphore API error: '.$response->status().' - '.$response->body();

                $smsLog->update([
                    'status' => 'failed',
                    'error_message' => $errorMessage,
                ]);

                Log::error('Semaphore API error', [
                    'to' => $formattedTo,
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'type' => $options['type'] ?? 'general',
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
                'type' => $options['type'] ?? 'general',
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

        if (! $phone || $phone === 'Not provided') {
            Log::info('No phone number available for appointment booking SMS', ['appointment_id' => $appointment->id]);

            return false;
        }

        $message = "Appointment booked successfully!\n".
            'Date: '.$appointment->appointment_datetime->format('M d, Y h:i A')."\n".
            'Service: '.$appointment->service->name."\n".
            'Provider: '.$appointment->employee->name."\n".
            'Status: Scheduled - Please arrive 15 minutes early.';

        return $this->sendSms($phone, $message, [
            'type' => 'appointment_booking',
            'user_id' => $patient->id,
            'metadata' => [
                'appointment_id' => $appointment->id,
                'service' => $appointment->service->name,
                'provider' => $appointment->employee->name,
                'datetime' => $appointment->appointment_datetime->toISOString(),
            ],
        ]);
    }

    /**
     * Send appointment cancellation SMS
     */
    public function sendAppointmentCancellation($appointment)
    {
        $patient = $appointment->patient;
        $phone = $patient->patientProfile->phone ?? null;

        if (! $phone || $phone === 'Not provided') {
            Log::info('No phone number available for appointment cancellation SMS', ['appointment_id' => $appointment->id]);

            return false;
        }

        $message = 'Your appointment with '.$appointment->employee->name.' for '.$appointment->service->name.' on '.$appointment->appointment_datetime->format('M d, Y').' at '.$appointment->appointment_datetime->format('g:i A').' has been cancelled.';

        return $this->sendSms($phone, $message, [
            'type' => 'appointment_cancellation',
            'user_id' => $patient->id,
            'metadata' => [
                'appointment_id' => $appointment->id,
                'service' => $appointment->service->name,
                'provider' => $appointment->employee->name,
                'datetime' => $appointment->appointment_datetime->toISOString(),
            ],
        ]);
    }

    /**
     * Send appointment confirmation SMS
     */
    public function sendAppointmentConfirmation($appointment)
    {
        $patient = $appointment->patient;
        $phone = $patient->patientProfile->phone ?? null;

        if (! $phone || $phone === 'Not provided') {
            Log::info('No phone number available for appointment SMS', ['appointment_id' => $appointment->id]);

            return false;
        }

        $message = "Your appointment has been confirmed!\n".
            'Date: '.$appointment->appointment_datetime->format('M d, Y h:i A')."\n".
            'Service: '.$appointment->service->name."\n".
            'Provider: '.$appointment->employee->name;

        return $this->sendSms($phone, $message, [
            'type' => 'appointment',
            'user_id' => $patient->id,
            'metadata' => [
                'appointment_id' => $appointment->id,
                'service' => $appointment->service->name,
                'provider' => $appointment->employee->name,
                'datetime' => $appointment->appointment_datetime->toISOString(),
            ],
        ]);
    }

    /**
     * Send appointment reminder SMS
     */
    public function sendAppointmentReminder($appointment)
    {
        $patient = $appointment->patient;
        $phone = $patient->patientProfile->phone ?? null;

        if (! $phone || $phone === 'Not provided') {
            Log::info('No phone number available for appointment reminder SMS', ['appointment_id' => $appointment->id]);

            return false;
        }

        $message = "Reminder: You have an appointment tomorrow!\n".
            'Date: '.$appointment->appointment_datetime->format('M d, Y h:i A')."\n".
            'Service: '.$appointment->service->name."\n".
            'Provider: '.$appointment->employee->name;

        return $this->sendSms($phone, $message, [
            'type' => 'reminder',
            'user_id' => $patient->id,
            'metadata' => [
                'appointment_id' => $appointment->id,
                'service' => $appointment->service->name,
                'provider' => $appointment->employee->name,
                'datetime' => $appointment->appointment_datetime->toISOString(),
            ],
        ]);
    }

    /**
     * Send payment confirmation SMS
     */
    public function sendPaymentConfirmation($payment)
    {
        $patient = $payment->patient;
        $phone = $patient->patientProfile->phone ?? null;

        if (! $phone || $phone === 'Not provided') {
            Log::info('No phone number available for payment SMS', ['payment_id' => $payment->id]);

            return false;
        }

        $message = "Payment received successfully!\n".
            'Amount: ₱'.number_format($payment->amount, 2)."\n".
            'Reference: '.($payment->payment_reference ?? 'N/A')."\n".
            'Thank you for choosing FRYDT Clinic!';

        return $this->sendSms($phone, $message, [
            'type' => 'payment',
            'user_id' => $patient->id,
            'metadata' => [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'reference' => $payment->payment_reference,
            ],
        ]);
    }

    /**
     * Send lab results notification SMS
     */
    public function sendLabResultsNotification($labResult)
    {
        $patient = $labResult->patient;
        $phone = $patient->patientProfile->phone ?? null;

        if (! $phone || $phone === 'Not provided') {
            Log::info('No phone number available for lab results SMS', ['lab_result_id' => $labResult->id]);

            return false;
        }

        $message = "Your lab results are ready!\n".
            'Test: '.$labResult->test_name."\n".
            'Status: '.$labResult->result_status."\n".
            'Please login to view detailed results.';

        return $this->sendSms($phone, $message, [
            'type' => 'lab_result',
            'user_id' => $patient->id,
            'metadata' => [
                'lab_result_id' => $labResult->id,
                'test_name' => $labResult->test_name,
                'result_status' => $labResult->result_status,
            ],
        ]);
    }

    /**
     * Send OTP via Semaphore OTP API
     */
    public function sendOtp($phone, $options = [])
    {
        if (! $phone || $phone === 'Not provided') {
            Log::info('No phone number available for OTP SMS');

            return false;
        }

        // Check if Semaphore is configured
        if (! $this->apiKey) {
            $error = 'Semaphore service not configured - missing API key';
            Log::warning($error);

            return false;
        }

        // Format phone number
        $formattedPhone = $this->formatPhoneNumber($phone);

        // Rate limiting: 120 calls per minute (same as SMS)
        // $key = 'semaphore-otp:'.$this->apiKey;
        // if (RateLimiter::tooManyAttempts($key, 120)) {
        //     Log::warning('OTP rate limit exceeded: 120 per minute');

        //     return false;
        // }
        // RateLimiter::hit($key, 60);

        // Prepare Semaphore OTP API request
        $sendData = [
            'apikey' => $this->apiKey,
            'number' => $formattedPhone,
        ];

        // Add message with OTP placeholder
        $message = $options['message'] ?? 'Your verification code is: {otp}';
        $sendData['message'] = 'FRYDT LYINGIN CLINIC: ' . $message;

        try {
            // Send OTP via Semaphore OTP API
            $response = Http::asForm()
                ->withOptions(['verify' => false])
                ->post($this->baseUrl.'/otp', $sendData);

            if ($response->successful()) {
                $responseData = $response->json();

                // Semaphore returns array, get first message
                $messageData = is_array($responseData) && count($responseData) > 0 ? $responseData[0] : $responseData;

                if (isset($messageData['code'])) {
                    // Update user with OTP data
                    if (! empty($options['user_id'])) {
                        $user = User::find($options['user_id']);
                        if ($user) {
                            $expiresAt = now()->addMinutes($options['expires_in_minutes'] ?? 5);
                            $user->update([
                                'otp_code' => $messageData['code'],
                                'otp_expires_at' => $expiresAt,
                                'otp_last_sent_at' => now(),
                                'otp_attempts' => 0, // Reset attempts
                            ]);
                        }
                    }

                    Log::info('OTP sent successfully via Semaphore', [
                        'to' => $formattedPhone,
                        'response' => $responseData,
                        'type' => 'otp_verification',
                    ]);

                    return true;
                } else {
                    $errorMessage = 'Invalid response from Semaphore OTP API: '.json_encode($responseData);
                    Log::error('Semaphore OTP API error', [
                        'to' => $formattedPhone,
                        'response' => $responseData,
                        'type' => 'otp_verification',
                    ]);

                    return false;
                }
            } else {
                $errorMessage = 'Semaphore OTP API error: '.$response->status().' - '.$response->body();

                Log::error('Semaphore OTP API error', [
                    'to' => $formattedPhone,
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'type' => 'otp_verification',
                ]);

                return false;
            }
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            Log::error('OTP sending failed', [
                'to' => $formattedPhone,
                'error' => $errorMessage,
                'type' => 'otp_verification',
            ]);

            return false;
        }
    }

    /**
     * Verify OTP locally (against stored code)
     */
    public function verifyOtp($phone, $otp, $userId = null)
    {
        if (! $phone || $phone === 'Not provided') {
            Log::info('No phone number available for OTP verification');

            return false;
        }

        // Find user by phone or user_id
        $user = null;
        if ($userId) {
            $user = User::find($userId);
        } else {
            // Find by phone number (assuming phone is stored in user profile)
            $formattedPhone = $this->formatPhoneNumber($phone);
            $user = User::whereHas('patientProfile', function ($query) use ($formattedPhone) {
                $query->where('phone', $formattedPhone);
            })->first();
        }

        if (! $user) {
            Log::error('User not found for OTP verification', [
                'phone' => $phone,
                'user_id' => $userId,
            ]);

            return false;
        }

        // Check if OTP is expired
        if ($user->otp_expires_at && now()->isAfter($user->otp_expires_at)) {
            Log::info('OTP expired', [
                'user_id' => $user->id,
                'expires_at' => $user->otp_expires_at,
            ]);

            return false;
        }

        // Check attempts
        if ($user->otp_attempts >= 3) {
            Log::warning('OTP verification attempts exceeded', [
                'user_id' => $user->id,
                'attempts' => $user->otp_attempts,
            ]);

            return false;
        }

        // Verify OTP code
        if ($user->otp_code && $user->otp_code == $otp) {
            // Success - clear OTP data
            $user->update([
                'otp_code' => null,
                'otp_expires_at' => null,
                'otp_verified_at' => now(),
                'otp_attempts' => 0,
            ]);

            Log::info('OTP verified successfully', [
                'user_id' => $user->id,
                'type' => 'otp_verification',
            ]);

            return true;
        } else {
            // Increment attempts
            $user->increment('otp_attempts');

            Log::warning('OTP verification failed - invalid code', [
                'user_id' => $user->id,
                'attempts' => $user->otp_attempts,
                'type' => 'otp_verification',
            ]);

            return false;
        }
    }

    /**
     * Send general notification SMS
     */
    public function sendNotification($phone, $message, $options = [])
    {
        if (! $phone || $phone === 'Not provided') {
            Log::info('No phone number available for notification SMS');

            return false;
        }

        return $this->sendSms($phone, $message, array_merge($options, [
            'type' => $options['type'] ?? 'notification',
        ]));
    }

    /**
     * Format phone number for Semaphore API (expects format: 63xxxxxxxxx)
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
            return '63'.substr($phone, 1);
        } elseif (strlen($phone) === 10 && str_starts_with($phone, '9')) {
            // Convert 9xxxxxxxxx to 63xxxxxxxxx
            return '63'.$phone;
        } elseif (strlen($phone) === 11 && str_starts_with($phone, '9')) {
            // Handle 09xxxxxxxxx format (11 digits starting with 9 after 0)
            return '63'.substr($phone, 1);
        } else {
            // For any other format, try to add 63 prefix if it doesn't have it
            if (! str_starts_with($phone, '63')) {
                $phone = '63'.$phone;
            }

            return $phone;
        }
    }

    /**
     * Get SMS credits from Semaphore API
     *
     * @return array|null Returns array with 'credit_balance' or null on error
     */
    public function getCredits()
    {
        // Check if Semaphore is configured
        if (! $this->apiKey) {
            Log::warning('Semaphore service not configured - missing API key');

            return null;
        }

        // Rate limiting: 2 calls per minute
        // $key = 'semaphore-account:'.$this->apiKey;
        // if (RateLimiter::tooManyAttempts($key, 2)) {
        //     Log::warning('Account API rate limit exceeded: 2 per minute');

        //     return null;
        // }
        // RateLimiter::hit($key, 60);

        try {
            // Make API call to get account info
            $response = Http::withOptions(['verify' => false])
                ->get($this->baseUrl.'/account', [
                    'apikey' => $this->apiKey,
                ]);
            
            Log::info('SMS credits retrieved successfully', [$response]);

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['credit_balance'])) {
                    Log::info('SMS credits retrieved successfully', [
                        'credits' => $responseData['credit_balance'],
                    ]);

                    return [
                        'credit_balance' => $responseData['credit_balance'],
                        'status' => 'success',
                        'account_name' => $responseData['account_name'] ?? null,
                        'account_status' => $responseData['status'] ?? null,
                    ];
                } else {
                    Log::error('Invalid response format from Semaphore account API', [
                        'response' => $responseData,
                    ]);

                    return null;
                }
            } else {
                $errorMessage = 'Semaphore account API error: '.$response->status().' - '.$response->body();
                Log::error('Semaphore account API error', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                return null;
            }

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error('SMS credits retrieval failed', [
                'error' => $errorMessage,
            ]);

            return null;
        }
    }

    /**
     * Check if SMS service is configured
     */
    public function isConfigured()
    {
        return ! empty($this->apiKey);
    }
}
