<?php

namespace App\Listeners;

use App\Events\EmployeeClockIn;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmployeeClockInSms implements ShouldQueue
{
    use InteractsWithQueue;

    protected $smsService;

    /**
     * Create the event listener.
     */
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Handle the event.
     */
    public function handle(EmployeeClockIn $event): void
    {
        $attendance = $event->attendance;
        $employee = $attendance->employee;

        // Get admin SMS number from settings
        $adminNumber = \App\Models\Setting::get('admin_sms_number');

        if (!$adminNumber) {
            return; // No admin number configured
        }

        // Format the message
        $message = "Employee Clock-In Alert:\n" .
                  "Employee: {$employee->name}\n" .
                  "Time: " . $attendance->check_in_time->format('M d, Y H:i:s') . "\n" .
                  "Date: " . $attendance->date->format('M d, Y');

        // Send SMS to admin
        $this->smsService->sendNotification($adminNumber, $message, [
            'type' => 'employee_clock_in',
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'attendance_id' => $attendance->id,
            'clock_in_time' => $attendance->check_in_time->toISOString(),
            'date' => $attendance->date->toDateString(),
        ]);
    }
}
