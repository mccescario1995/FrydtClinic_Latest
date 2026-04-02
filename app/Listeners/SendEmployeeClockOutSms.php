<?php

namespace App\Listeners;

use App\Events\EmployeeClockOut;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmployeeClockOutSms implements ShouldQueue
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
    public function handle(EmployeeClockOut $event): void
    {
        $attendance = $event->attendance;
        $employee = $attendance->employee;

        // Get admin SMS number from settings
        $adminNumber = \App\Models\Setting::get('admin_sms_number');

        if (!$adminNumber) {
            return; // No admin number configured
        }

        // Calculate total hours worked
        $durationText = 'N/A';
        if ($attendance->check_in_time && $attendance->check_out_time) {
            $start = \Carbon\Carbon::parse($attendance->check_in_time);
            $end = \Carbon\Carbon::parse($attendance->check_out_time);
            $totalSeconds = $start->diffInSeconds($end);

            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            $seconds = $totalSeconds % 60;

            $parts = [];
            if ($hours > 0) {
                $parts[] = $hours . 'hr' . ($hours > 1 ? 's' : '');
            }
            if ($minutes > 0 || $hours > 0) {
                $parts[] = $minutes . 'min' . ($minutes > 1 ? 's' : '');
            }
            if ($seconds > 0 || ($hours == 0 && $minutes == 0)) {
                $parts[] = $seconds . 'sec' . ($seconds > 1 ? 's' : '');
            }

            $durationText = implode(' ', $parts);
        }

        // Format the message
        $message = "Employee Time-Out Alert:\n" .
                  "Employee: {$employee->name}\n" .
                  "Time In: " . $attendance->check_in_time->format('H:i:s') . "\n" .
                  "Time Out: " . $attendance->check_out_time->format('H:i:s') . "\n" .
                  "Total Hours: {$durationText}\n" .
                  "Date: " . $attendance->date->format('M d, Y');

        // Send SMS to admin
        $this->smsService->sendNotification($adminNumber, $message, [
            'type' => 'employee_clock_out',
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'attendance_id' => $attendance->id,
            'clock_in_time' => $attendance->check_in_time->toISOString(),
            'clock_out_time' => $attendance->check_out_time->toISOString(),
            'duration_text' => $durationText,
            'date' => $attendance->date->toDateString(),
        ]);
    }
}
