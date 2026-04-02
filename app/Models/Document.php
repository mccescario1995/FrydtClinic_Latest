<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Backpack\CRUD\app\Models\Traits\CrudTrait;


class Document extends Model
{

    use CrudTrait;

    protected $fillable = [
        'patient_id',
        'uploaded_by',
        'title',
        'description',
        'document_type',
        'category',
        'file_name',
        'original_file_name',
        'file_path',
        'file_url',
        'mime_type',
        'file_size',
        'file_extension',
        'document_date',
        'document_number',
        'issuing_authority',
        'expiration_date',
        'access_level',
        'is_confidential',
        'access_restrictions',
        'version',
        'parent_document_id',
        'change_log',
        'is_digitally_signed',
        'digital_signature',
        'signed_at',
        'signed_by',
        'status',
        'status_notes',
        'requires_review',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'retention_years',
        'retention_expiry_date',
        'retention_policy',
        'legal_hold_reason',
        'tags',
        'keywords',
        'download_count',
        'last_accessed_at',
        'last_accessed_by',
    ];

    protected $casts = [
        'document_date' => 'date',
        'expiration_date' => 'date',
        'signed_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'retention_expiry_date' => 'date',
        'tags' => 'array',
        'is_confidential' => 'boolean',
        'is_digitally_signed' => 'boolean',
        'requires_review' => 'boolean',
    ];

    // Relationships
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function signer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function lastAccessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_accessed_by');
    }
}
