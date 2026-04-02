<?php

namespace App\Listeners;

use App\Events\AppointmentReminder;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAppointmentReminderSms implements ShouldQueue
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
    public function handle(AppointmentReminder $event): void
    {
        $this->smsService->sendAppointmentReminder($event->appointment);
    }
}
