<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class Form extends Model
{
    use CrudTrait;
    protected $fillable = [
        'patient_id',
        'form_name',
        'form_type',
        'form_code',
        'description',
        'form_fields',
        'form_data',
        'form_template',
        'status',
        'priority',
        'assigned_to',
        'completed_by',
        'assigned_date',
        'completed_date',
        'due_date',
        'requires_signature',
        'digital_signature',
        'signed_date',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'version',
        'parent_form_id',
        'change_log',
        'is_confidential',
        'retention_policy',
        'expiration_date',
        'legal_notes',
        'auto_generate',
        'trigger_event',
        'automation_rules',
        'requires_approval',
        'approved_by',
        'approved_date',
        'approval_notes',
        'send_to_patient',
        'sent_date',
        'patient_instructions',
    ];

    protected $casts = [
        'form_fields' => 'array',
        'form_data' => 'array',
        'assigned_date' => 'datetime',
        'completed_date' => 'datetime',
        'due_date' => 'datetime',
        'signed_date' => 'datetime',
        'approved_date' => 'datetime',
        'sent_date' => 'datetime',
        'expiration_date' => 'date',
        'automation_rules' => 'array',
    ];

    // Relationships
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function completedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
