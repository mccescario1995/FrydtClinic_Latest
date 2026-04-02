<?php

namespace App\Listeners;

use App\Events\AppointmentCancelled;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAppointmentCancellationSms implements ShouldQueue
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
    public function handle(AppointmentCancelled $event): void
    {
        $this->smsService->sendAppointmentCancellation($event->appointment);
    }
}
