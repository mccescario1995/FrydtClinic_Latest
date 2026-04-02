<?php

namespace App\Events;

use App\Models\LaboratoryResult;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LabResultsReady
{
    use Dispatchable, SerializesModels;

    public $labResult;

    /**
     * Create a new event instance.
     */
    public function __construct(LaboratoryResult $labResult)
    {
        $this->labResult = $labResult;
    }
}
