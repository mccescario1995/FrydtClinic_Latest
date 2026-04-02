<?php

namespace App\Providers;

use App\Events\AppointmentBooked;
use App\Events\AppointmentCancelled;
use App\Events\AppointmentConfirmed;
use App\Events\AppointmentReminder;
use App\Events\EmployeeClockIn;
use App\Events\EmployeeClockOut;
use App\Events\LabResultsReady;
use App\Events\PaymentCompleted;
use App\Listeners\SendAppointmentBookingSms;
use App\Listeners\SendAppointmentCancellationSms;
use App\Listeners\SendAppointmentConfirmationSms;
use App\Listeners\SendAppointmentReminderSms;
use App\Listeners\SendEmployeeClockInSms;
use App\Listeners\SendEmployeeClockOutSms;
use App\Listeners\SendLabResultsSms;
use App\Listeners\SendPaymentConfirmationSms;
use App\Listeners\ResetPinVerificationOnLogout;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        AppointmentBooked::class => [
            SendAppointmentBookingSms::class,
        ],

        AppointmentCancelled::class => [
            SendAppointmentCancellationSms::class,
        ],

        AppointmentConfirmed::class => [
            SendAppointmentConfirmationSms::class,
        ],

        PaymentCompleted::class => [
            SendPaymentConfirmationSms::class,
        ],

        LabResultsReady::class => [
            SendLabResultsSms::class,
        ],

        AppointmentReminder::class => [
            SendAppointmentReminderSms::class,
        ],

        EmployeeClockIn::class => [
            SendEmployeeClockInSms::class,
        ],

        EmployeeClockOut::class => [
            SendEmployeeClockOutSms::class,
        ],

        Logout::class => [
            ResetPinVerificationOnLogout::class,
        ],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
