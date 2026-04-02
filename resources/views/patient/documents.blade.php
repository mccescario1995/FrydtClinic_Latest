@extends('patient.layouts.app')

@section('title', 'My Documents - FRYDT Patient Portal')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>My Documents
                    </h5>
                </div>
                <div class="card-body">
                    @if($documents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $document)
                                    <tr>
                                        <td>
                                            <strong>{{ $document->title }}</strong>
                                            @if($document->is_confidential)
                                                <i class="fas fa-lock text-warning ms-1" title="Confidential"></i>
                                            @endif
                                        </td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $document->document_type)) }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $document->category)) }}</td>
                                        <td>{{ $document->created_at->format('M j, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{
                                                $document->status === 'active' ? 'success' :
                                                ($document->status === 'archived' ? 'secondary' :
                                                ($document->status === 'pending_review' ? 'warning' : 'danger'))
                                            }}">
                                                {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($document->status === 'active')
                                                <a href="{{ route('patient.download-document', $document->id) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   target="_blank">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $documents->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No documents found</h6>
                            <p class="text-muted">Your medical documents will appear here once they are uploaded.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
