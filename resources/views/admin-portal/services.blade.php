@extends('admin-portal.layouts.app')

@section('title', 'Services Management')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"><i class="fas fa-stethoscope me-2"></i>Services Management</h1>
            <p class="page-subtitle">Manage clinic services, pricing, and configurations</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary fs-6">{{ $services->total() }} Total Services</span>
            <a href="{{ route('admin-portal.services.create') }}" class="btn btn-admin-primary">
                <i class="fas fa-plus me-1"></i>Create Service
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="admin-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-primary text-white mb-2">
                    <i class="fas fa-list"></i>
                </div>
                <h4 class="stat-value">{{ $totalServices }}</h4>
                <p class="stat-label text-muted">Total Services</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-success text-white mb-2">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4 class="stat-value">{{ $activeServices }}</h4>
                <p class="stat-label text-muted">Active Services</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-info text-white mb-2">
                    <i class="fas fa-file-medical"></i>
                </div>
                <h4 class="stat-value">{{ $singleServices }}</h4>
                <p class="stat-label text-muted">Single Services</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-warning text-white mb-2">
                    <i class="fas fa-boxes"></i>
                </div>
                <h4 class="stat-value">{{ $packageServices }}</h4>
                <p class="stat-label text-muted">Package Services</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-section">
    <form method="GET" action="{{ route('admin-portal.services') }}" class="row g-3">
        <div class="col-md-2">
            <label for="type" class="form-label">Type</label>
            <select name="type" id="type" class="form-select">
                <option value="">All Types</option>
                <option value="single" {{ request('type') === 'single' ? 'selected' : '' }}>Single</option>
                <option value="package" {{ request('type') === 'package' ? 'selected' : '' }}>Package</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="service_type" class="form-label">Service Type</label>
            <select name="service_type" id="service_type" class="form-select">
                <option value="">All Service Types</option>
                <option value="consultation" {{ request('service_type') === 'consultation' ? 'selected' : '' }}>Consultation</option>
                <option value="procedure" {{ request('service_type') === 'procedure' ? 'selected' : '' }}>Procedure</option>
                <option value="laboratory" {{ request('service_type') === 'laboratory' ? 'selected' : '' }}>Laboratory</option>
                <option value="imaging" {{ request('service_type') === 'imaging' ? 'selected' : '' }}>Imaging</option>
                <option value="therapy" {{ request('service_type') === 'therapy' ? 'selected' : '' }}>Therapy</option>
                <option value="vaccination" {{ request('service_type') === 'vaccination' ? 'selected' : '' }}>Vaccination</option>
                <option value="prenatal_care" {{ request('service_type') === 'prenatal_care' ? 'selected' : '' }}>Prenatal Care</option>
                <option value="delivery" {{ request('service_type') === 'delivery' ? 'selected' : '' }}>Delivery</option>
                <option value="postnatal_care" {{ request('service_type') === 'postnatal_care' ? 'selected' : '' }}>Postnatal Care</option>
                <option value="other" {{ request('service_type') === 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="category" class="form-label">Category</label>
            <select name="category" id="category" class="form-select">
                <option value="">All Categories</option>
                <option value="general_practice" {{ request('category') === 'general_practice' ? 'selected' : '' }}>General Practice</option>
                <option value="obstetrics_gynecology" {{ request('category') === 'obstetrics_gynecology' ? 'selected' : '' }}>Obstetrics & Gynecology</option>
                <option value="pediatrics" {{ request('category') === 'pediatrics' ? 'selected' : '' }}>Pediatrics</option>
                <option value="internal_medicine" {{ request('category') === 'internal_medicine' ? 'selected' : '' }}>Internal Medicine</option>
                <option value="surgery" {{ request('category') === 'surgery' ? 'selected' : '' }}>Surgery</option>
                <option value="emergency_care" {{ request('category') === 'emergency_care' ? 'selected' : '' }}>Emergency Care</option>
                <option value="preventive_care" {{ request('category') === 'preventive_care' ? 'selected' : '' }}>Preventive Care</option>
                <option value="diagnostic" {{ request('category') === 'diagnostic' ? 'selected' : '' }}>Diagnostic</option>
                <option value="therapeutic" {{ request('category') === 'therapeutic' ? 'selected' : '' }}>Therapeutic</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="discontinued" {{ request('status') === 'discontinued' ? 'selected' : '' }}>Discontinued</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="search" class="form-label">Search</label>
            <input type="text" name="search" id="search" class="form-control"
                   placeholder="Search by name, code, or description" value="{{ request('search') }}">
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-search me-1"></i>Filter
            </button>
        </div>
    </form>
</div>

<!-- Services Table -->
<div class="content-section">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-list me-2"></i>Services</h2>
        <p class="section-subtitle">Complete list of clinic services with their details and pricing</p>
    </div>

    <div class="admin-card">
        <div class="card-body">
            @if($services->count() > 0)
                <div class="table-responsive">
                    <table class="table admin-table table-hover">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                <tr>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $service->name }}</div>
                                            <small class="text-muted">Code: {{ $service->code }}</small>
                                            @if($service->description)
                                                <div class="text-truncate" style="max-width: 200px;" title="{{ $service->description }}">
                                                    {{ Str::limit($service->description, 50) }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $service->type === 'single' ? 'info' : 'warning' }}">
                                            <i class="fas fa-{{ $service->type === 'single' ? 'file-medical' : 'boxes' }} me-1"></i>
                                            {{ ucfirst($service->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ ucwords(str_replace('_', ' ', $service->category)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $service->display_price }}</div>
                                            @if($service->philhealth_covered)
                                                <small class="text-success">PhilHealth: ₱{{ number_format($service->philhealth_price, 2) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($service->duration_minutes)
                                            {{ $service->duration_minutes }} min
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $service->status === 'active' ? 'success' : ($service->status === 'inactive' ? 'secondary' : 'danger') }}">
                                            <i class="fas fa-{{ $service->status === 'active' ? 'check-circle' : ($service->status === 'inactive' ? 'pause-circle' : 'times-circle') }} me-1"></i>
                                            {{ ucfirst($service->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin-portal.services.show', $service->id) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin-portal.services.edit', $service->id) }}"
                                               class="btn btn-sm btn-outline-secondary"
                                               title="Edit Service">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($service->appointments()->count() === 0)
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="deleteService({{ $service->id }}, '{{ $service->name }}')"
                                                        title="Delete Service">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $services->appends(request()->query())->links('vendor.pagination.admin-portal') }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <h5 class="empty-title">No Services Found</h5>
                    <p class="empty-text">No services match your current filter criteria.</p>
                    <a href="{{ route('admin-portal.services') }}" class="btn btn-admin-primary">
                        <i class="fas fa-times me-1"></i>Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Hidden delete form -->
<form id="deleteServiceForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scripts')
<script>
function deleteService(serviceId, serviceName) {
    AdminUtils.confirmDelete(
        'Delete Service',
        `Are you sure you want to delete "${serviceName}"? This action cannot be undone.`,
        function() {
            // Set the form action and submit
            const form = document.getElementById('deleteServiceForm');
            form.action = `{{ url('/admin-portal/services') }}/${serviceId}`;
            form.submit();
        }
    );
}
</script>
@endsection
