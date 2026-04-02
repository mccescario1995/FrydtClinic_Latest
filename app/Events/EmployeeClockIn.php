<?php

namespace App\Events;

use App\Models\EmployeeAttendance;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeClockIn
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $attendance;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeAttendance $attendance)
    {
        $this->attendance = $attendance;
    }
}
