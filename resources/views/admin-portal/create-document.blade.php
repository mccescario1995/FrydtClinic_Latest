@extends('admin-portal.layouts.app')

@section('title', 'Upload Document')

@section('content')
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-upload me-2"></i>Upload Document
        </h1>
        <p class="page-subtitle">Upload a new document for a patient</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="admin-card">
                <div class="card-header">
                    <h5 class="mb-0">Document Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin-portal.documents.store') }}" enctype="multipart/form-data">
                        @csrf

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
                                                {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
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
                                            {{ old('document_type') === 'medical_record' ? 'selected' : '' }}>Medical Record
                                        </option>
                                        <option value="lab_result"
                                            {{ old('document_type') === 'lab_result' ? 'selected' : '' }}>Lab Result
                                        </option>
                                        <option value="prescription"
                                            {{ old('document_type') === 'prescription' ? 'selected' : '' }}>Prescription
                                        </option>
                                        <option value="consent_form"
                                            {{ old('document_type') === 'consent_form' ? 'selected' : '' }}>Consent Form
                                        </option>
                                        <option value="discharge_summary"
                                            {{ old('document_type') === 'discharge_summary' ? 'selected' : '' }}>Discharge
                                            Summary</option>
                                        <option value="insurance_form"
                                            {{ old('document_type') === 'insurance_form' ? 'selected' : '' }}>Insurance
                                            Form</option>
                                        <option value="other" {{ old('document_type') === 'other' ? 'selected' : '' }}>
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
                                name="title" value="{{ old('title') }}" required placeholder="Enter document title">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="3" placeholder="Optional description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                        <option value="">Select category</option>
                                        <option value="prenatal" {{ old('category') === 'prenatal' ? 'selected' : '' }}>Prenatal</option>
                                        <option value="labor_delivery" {{ old('category') === 'labor_delivery' ? 'selected' : '' }}>Labor & Delivery</option>
                                        <option value="postnatal" {{ old('category') === 'postnatal' ? 'selected' : '' }}>Postnatal</option>
                                        <option value="pediatric" {{ old('category') === 'pediatric' ? 'selected' : '' }}>Pediatric</option>
                                        <option value="general_medicine" {{ old('category') === 'general_medicine' ? 'selected' : '' }}>General Medicine</option>
                                        <option value="surgical" {{ old('category') === 'surgical' ? 'selected' : '' }}>Surgical</option>
                                        <option value="emergency" {{ old('category') === 'emergency' ? 'selected' : '' }}>Emergency</option>
                                        <option value="administrative" {{ old('category') === 'administrative' ? 'selected' : '' }}>Administrative</option>
                                        <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Other</option>
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
                                        id="document_date" name="document_date" value="{{ old('document_date') }}">
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
                                        id="expiration_date" name="expiration_date" value="{{ old('expiration_date') }}"
                                        min="{{ date('Y-m-d') }}">
                                    @error('expiration_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="is_confidential"
                                            name="is_confidential" value="1"
                                            {{ old('is_confidential') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_confidential">
                                            Mark as Confidential
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="document_file" class="form-label">Document File <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('document_file') is-invalid @enderror"
                                id="document_file" name="document_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                required>
                            <div class="form-text">Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG. Maximum size: 10MB.
                            </div>
                            @error('document_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-admin-primary">
                                <i class="fas fa-upload me-1"></i>Upload Document
                            </button>
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
