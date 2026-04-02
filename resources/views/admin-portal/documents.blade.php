@extends('admin-portal.layouts.app')

@section('title', 'Documents Management')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-file-alt me-2"></i>Documents Management
    </h1>
    <p class="page-subtitle">Manage patient documents and medical records</p>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="admin-card p-3 text-center">
            <div class="text-primary mb-2">
                <i class="fas fa-file-alt fa-2x"></i>
            </div>
            <h4 class="mb-1">{{ $documents->total() }}</h4>
            <p class="text-muted mb-0">Total Documents</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card p-3 text-center">
            <div class="text-success mb-2">
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
            <h4 class="mb-1">{{ $documents->where('status', 'active')->count() }}</h4>
            <p class="text-muted mb-0">Active Documents</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card p-3 text-center">
            <div class="text-warning mb-2">
                <i class="fas fa-clock fa-2x"></i>
            </div>
            <h4 class="mb-1">{{ $documents->where('is_confidential', true)->count() }}</h4>
            <p class="text-muted mb-0">Confidential</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card p-3 text-center">
            <div class="text-info mb-2">
                <i class="fas fa-download fa-2x"></i>
            </div>
            <h4 class="mb-1">{{ $documents->sum('download_count') }}</h4>
            <p class="text-muted mb-0">Total Downloads</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-section">
    <form method="GET" action="{{ route('admin-portal.documents') }}" class="row g-3">
        <div class="col-md-3">
            <label for="patient_id" class="form-label">Patient</label>
            <select name="patient_id" id="patient_id" class="form-select">
                <option value="">All Patients</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                        {{ $patient->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="document_type" class="form-label">Type</label>
            <select name="document_type" id="document_type" class="form-select">
                <option value="">All Types</option>
                <option value="medical_record" {{ request('document_type') === 'medical_record' ? 'selected' : '' }}>Medical Record</option>
                <option value="lab_result" {{ request('document_type') === 'lab_result' ? 'selected' : '' }}>Lab Result</option>
                <option value="prescription" {{ request('document_type') === 'prescription' ? 'selected' : '' }}>Prescription</option>
                <option value="consent_form" {{ request('document_type') === 'consent_form' ? 'selected' : '' }}>Consent Form</option>
                <option value="discharge_summary" {{ request('document_type') === 'discharge_summary' ? 'selected' : '' }}>Discharge Summary</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="search" class="form-label">Search</label>
            <input type="text" name="search" id="search" class="form-control"
                   value="{{ request('search') }}" placeholder="Title or description...">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-admin-primary me-2">
                <i class="fas fa-search me-1"></i>Filter
            </button>
            <a href="{{ route('admin-portal.documents') }}" class="btn btn-outline-secondary" title="Clear all filters">
                <i class="fas fa-times me-1"></i>Clear
            </a>
        </div>
    </form>
</div>

<!-- Documents Table -->
<div class="content-section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="section-title"><i class="fas fa-list me-2"></i>Documents</h2>
            <p class="section-subtitle">Manage patient documents and files</p>
        </div>
        <a href="{{ route('admin-portal.documents.create') }}" class="btn btn-admin-primary">
            <i class="fas fa-plus me-1"></i>Upload Document
        </a>
    </div>

    <div class="admin-card">
        <div class="card-body">
            @if($documents->count() > 0)
                <div class="table-responsive">
                    <table class="table admin-table table-hover">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Confidential</th>
                                <th>Uploaded</th>
                                <th>Downloads</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                 style="width: 40px; height: 40px; font-weight: bold; font-size: 16px;">
                                                {{ substr($document->patient->name ?? 'N/A', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $document->patient->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $document->patient->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $document->title }}</div>
                                        <small class="text-muted">{{ Str::limit($document->description, 50) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $document->document_type)) }}</span>
                                    </td>
                                    <td>
                                        @if($document->status === 'active')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($document->status === 'inactive')
                                            <span class="badge bg-warning">Inactive</span>
                                        @elseif($document->status === 'expired')
                                            <span class="badge bg-danger">Expired</span>
                                        @else
                                            <span class="badge bg-secondary">Archived</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($document->is_confidential)
                                            <span class="badge bg-warning">
                                                <i class="fas fa-lock me-1"></i>Yes
                                            </span>
                                        @else
                                            <span class="text-muted">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $document->created_at->format('M j, Y') }}</div>
                                        <small class="text-muted">{{ $document->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $document->download_count }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin-portal.documents.show', $document->id) }}"
                                               class="btn btn-sm btn-outline-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin-portal.documents.download', $document->id) }}"
                                               class="btn btn-sm btn-outline-success" title="Download" target="_blank">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="{{ route('admin-portal.documents.edit', $document->id) }}"
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger"
                                                    title="Delete Document"
                                                    onclick="deleteDocument({{ $document->id }}, '{{ $document->title }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $documents->appends(request()->query())->links('vendor.pagination.admin-portal') }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h5 class="empty-title">No Documents Found</h5>
                    <p class="empty-text">No documents match your current filter criteria.</p>
                    <a href="{{ route('admin-portal.documents.create') }}" class="btn btn-admin-primary">
                        <i class="fas fa-plus me-1"></i>Upload First Document
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function deleteDocument(documentId, title) {
    if (confirm(`Are you sure you want to delete the document "${title}"? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin-portal/documents/${documentId}`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
