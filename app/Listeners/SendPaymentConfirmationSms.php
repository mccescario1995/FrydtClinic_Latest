<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentConfirmationSms implements ShouldQueue
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
    public function handle(PaymentCompleted $event): void
    {
        $this->smsService->sendPaymentConfirmation($event->payment);
    }
}
