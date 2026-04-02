@extends('admin-portal.layouts.app')

@section('title', 'Edit Document - ' . $document->title)

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-edit me-2"></i>Edit Document
    </h1>
    <p class="page-subtitle">Update document information for {{ $document->patient->name }}</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0">Document Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin-portal.documents.update', $document->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="patient_id" class="form-label">Patient <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('patient_id') is-invalid @enderror" id="patient_id"
                                    name="patient_id" required>
                                    <option value="">Select a patient</option>
                                    @foreach ($patients as $patient)
                                        <option value="{{ $patient->id }}"
                                            {{ old('patient_id', $document->patient_id) == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->name }} ({{ $patient->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('patient_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="document_type" class="form-label">Document Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('document_type') is-invalid @enderror"
                                    id="document_type" name="document_type" required>
                                    <option value="">Select type</option>
                                    <option value="medical_record"
                                        {{ old('document_type', $document->document_type) === 'medical_record' ? 'selected' : '' }}>Medical Record
                                    </option>
                                    <option value="lab_result"
                                        {{ old('document_type', $document->document_type) === 'lab_result' ? 'selected' : '' }}>Lab Result
                                    </option>
                                    <option value="prescription"
                                        {{ old('document_type', $document->document_type) === 'prescription' ? 'selected' : '' }}>Prescription
                                    </option>
                                    <option value="consent_form"
                                        {{ old('document_type', $document->document_type) === 'consent_form' ? 'selected' : '' }}>Consent Form
                                    </option>
                                    <option value="discharge_summary"
                                        {{ old('document_type', $document->document_type) === 'discharge_summary' ? 'selected' : '' }}>Discharge
                                        Summary</option>
                                    <option value="insurance_form"
                                        {{ old('document_type', $document->document_type) === 'insurance_form' ? 'selected' : '' }}>Insurance
                                        Form</option>
                                    <option value="other" {{ old('document_type', $document->document_type) === 'other' ? 'selected' : '' }}>
                                        Other</option>
                                </select>
                                @error('document_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                            name="title" value="{{ old('title', $document->title) }}" required placeholder="Enter document title">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                            rows="3" placeholder="Optional description">{{ old('description', $document->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category">
                                    <option value="">Select category</option>
                                    <option value="prenatal" {{ old('category', $document->category) === 'prenatal' ? 'selected' : '' }}>Prenatal</option>
                                    <option value="labor_delivery" {{ old('category', $document->category) === 'labor_delivery' ? 'selected' : '' }}>Labor & Delivery</option>
                                    <option value="postnatal" {{ old('category', $document->category) === 'postnatal' ? 'selected' : '' }}>Postnatal</option>
                                    <option value="pediatric" {{ old('category', $document->category) === 'pediatric' ? 'selected' : '' }}>Pediatric</option>
                                    <option value="general_medicine" {{ old('category', $document->category) === 'general_medicine' ? 'selected' : '' }}>General Medicine</option>
                                    <option value="surgical" {{ old('category', $document->category) === 'surgical' ? 'selected' : '' }}>Surgical</option>
                                    <option value="emergency" {{ old('category', $document->category) === 'emergency' ? 'selected' : '' }}>Emergency</option>
                                    <option value="administrative" {{ old('category', $document->category) === 'administrative' ? 'selected' : '' }}>Administrative</option>
                                    <option value="other" {{ old('category', $document->category) === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="document_date" class="form-label">Document Date</label>
                                <input type="date" class="form-control @error('document_date') is-invalid @enderror"
                                    id="document_date" name="document_date" value="{{ old('document_date', $document->document_date) }}">
                                @error('document_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expiration_date" class="form-label">Expiration Date</label>
                                <input type="date"
                                    class="form-control @error('expiration_date') is-invalid @enderror"
                                    id="expiration_date" name="expiration_date" value="{{ old('expiration_date', $document->expiration_date) }}"
                                    min="{{ date('Y-m-d') }}">
                                @error('expiration_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status', $document->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $document->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="expired" {{ old('status', $document->status) === 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="archived" {{ old('status', $document->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_confidential"
                                name="is_confidential" value="1"
                                {{ old('is_confidential', $document->is_confidential) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_confidential">
                                Mark as Confidential
                            </label>
                        </div>
                    </div>

                    <!-- Current File Information -->
                    <div class="mb-3">
                        <label class="form-label">Current File</label>
                        <div class="border rounded p-3 bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="mb-2">
                                        <strong>{{ $document->original_file_name }}</strong>
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fas fa-file me-1"></i>{{ strtoupper($document->file_extension) }} •
                                        <i class="fas fa-weight me-1"></i>{{ number_format($document->file_size / 1024, 2) }} KB •
                                        <i class="fas fa-calendar me-1"></i>Uploaded {{ $document->created_at->format('M d, Y H:i') }}
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="{{ route('admin-portal.documents.download', $document->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-text">
                            To replace the file, you would need to delete this document and upload a new one.
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-admin-primary">
                            <i class="fas fa-save me-1"></i>Update Document
                        </button>
                        <a href="{{ route('admin-portal.documents.show', $document->id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-eye me-1"></i>View Document
                        </a>
                        <a href="{{ route('admin-portal.documents') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
