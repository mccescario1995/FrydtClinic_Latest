@extends('admin-portal.layouts.app')

@section('title', 'Document Details - ' . $document->title)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0"><i class="fas fa-file-alt me-2"></i>{{ $document->title }}</h1>
                <p class="text-muted mb-0">Document details and information</p>
            </div>
            <div>
                <a href="{{ route('admin-portal.documents') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Documents
                </a>
                <a href="{{ route('admin-portal.documents.edit', $document->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>Edit Document
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Document Information -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Document Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Title</label>
                            <p class="mb-0">{{ $document->title }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Document Type</label>
                            <p class="mb-0">
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $document->document_type)) }}</span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <p class="mb-0">
                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $document->category)) }}</span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $document->status === 'active' ? 'success' : ($document->status === 'archived' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($document->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Patient</label>
                            <p class="mb-0">
                                <a href="{{ route('admin-portal.patients.show', $document->patient->id) }}" class="text-decoration-none">
                                    {{ $document->patient->name }}
                                </a>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Uploaded By</label>
                            <p class="mb-0">{{ $document->uploader->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Upload Date</label>
                            <p class="mb-0">{{ $document->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">File Size</label>
                            <p class="mb-0">{{ number_format($document->file_size / 1024, 2) }} KB</p>
                        </div>
                    </div>
                </div>

                @if($document->description)
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <p class="mb-0">{{ $document->description }}</p>
                </div>
                @endif

                @if($document->document_date)
                <div class="mb-3">
                    <label class="form-label fw-bold">Document Date</label>
                    <p class="mb-0">{{ \Carbon\Carbon::parse($document->document_date)->format('M d, Y') }}</p>
                </div>
                @endif

                @if($document->expiration_date)
                <div class="mb-3">
                    <label class="form-label fw-bold">Expiration Date</label>
                    <p class="mb-0">
                        {{ \Carbon\Carbon::parse($document->expiration_date)->format('M d, Y') }}
                        @if($document->expiration_date < now())
                            <span class="badge bg-danger ms-2">Expired</span>
                        @elseif($document->expiration_date < now()->addDays(30))
                            <span class="badge bg-warning ms-2">Expiring Soon</span>
                        @endif
                    </p>
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-bold">Confidential</label>
                    <p class="mb-0">
                        @if($document->is_confidential)
                            <span class="badge bg-danger"><i class="fas fa-lock me-1"></i>Yes</span>
                        @else
                            <span class="badge bg-success"><i class="fas fa-unlock me-1"></i>No</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- File Information -->
        <div class="admin-card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-file me-2"></i>File Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Original Filename</label>
                            <p class="mb-0">{{ $document->original_file_name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">File Extension</label>
                            <p class="mb-0">{{ strtoupper($document->file_extension) }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">MIME Type</label>
                            <p class="mb-0">{{ $document->mime_type }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Download Count</label>
                            <p class="mb-0">{{ $document->download_count }}</p>
                        </div>
                        @if($document->last_accessed_at)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Last Accessed</label>
                            <p class="mb-0">{{ $document->last_accessed_at->format('M d, Y H:i') }}</p>
                        </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label fw-bold">Actions</label>
                            <div class="mb-0">
                                <a href="{{ route('admin-portal.documents.download', $document->id) }}" class="btn btn-sm btn-success" target="_blank">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                                @if($document->file_url)
                                <a href="{{ $document->file_url }}" class="btn btn-sm btn-primary ms-2" target="_blank">
                                    <i class="fas fa-external-link-alt me-1"></i>View Online
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="admin-card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin-portal.documents.download', $document->id) }}" class="btn btn-success" target="_blank">
                        <i class="fas fa-download me-2"></i>Download Document
                    </a>
                    <a href="{{ route('admin-portal.documents.edit', $document->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Document
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-2"></i>Delete Document
                    </button>
                </div>
            </div>
        </div>

        <!-- Document Stats -->
        <div class="admin-card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Document Stats</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="mb-2">
                            <i class="fas fa-download fa-2x text-primary"></i>
                        </div>
                        <h4 class="mb-0">{{ $document->download_count }}</h4>
                        <small class="text-muted">Downloads</small>
                    </div>
                    <div class="col-6">
                        <div class="mb-2">
                            <i class="fas fa-clock fa-2x text-info"></i>
                        </div>
                        <h4 class="mb-0">{{ $document->created_at->diffForHumans() }}</h4>
                        <small class="text-muted">Age</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this document? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> Deleting this document will permanently remove the file from storage.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('admin-portal.documents.delete', $document->id) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Document</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
