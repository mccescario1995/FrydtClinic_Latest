<?php

namespace App\Listeners;

use App\Events\AppointmentBooked;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAppointmentBookingSms implements ShouldQueue
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
    public function handle(AppointmentBooked $event): void
    {
        $this->smsService->sendAppointmentBooking($event->appointment);
    }
}
