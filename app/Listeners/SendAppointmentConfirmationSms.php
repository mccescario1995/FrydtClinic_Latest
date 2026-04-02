<?php

namespace App\Listeners;

use App\Events\AppointmentConfirmed;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAppointmentConfirmationSms implements ShouldQueue
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
    public function handle(AppointmentConfirmed $event): void
    {
        $this->smsService->sendAppointmentConfirmation($event->appointment);
    }
}
