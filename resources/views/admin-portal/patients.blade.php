@extends('admin-portal.layouts.app')

@section('title', 'Patient Management')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-user-injured me-2"></i>Patient Management
    </h1>
    <p class="page-subtitle">Manage patient records and information</p>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <form method="GET" action="{{ route('admin-portal.patients') }}" class="row g-3">
        <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-md-6">
            <label for="search" class="form-label">Search</label>
            <input type="text" name="search" id="search" class="form-control"
                   placeholder="Search by name or email..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-admin-primary me-2">
                <i class="fas fa-search me-1"></i>Filter
            </button>
            <a href="{{ route('admin-portal.patients') }}" class="btn btn-outline-secondary">
                <i class="fas fa-times me-1"></i>Clear
            </a>
        </div>
    </form>
</div>

<!-- Action Buttons -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <span class="text-muted">Showing {{ $patients->count() }} of {{ $patients->total() }} patients</span>
    </div>
    <div>
        <a href="{{ route('admin-portal.patients.create') }}" class="btn btn-admin-primary">
            <i class="fas fa-plus me-1"></i>Add New Patient
        </a>
    </div>
</div>

<!-- Patients Table -->
@if($patients->count() > 0)
    <div class="admin-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Patients</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="admin-table table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Birth Date</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($patient->patientProfile && $patient->patientProfile->image_path)
                                            <img src="{{ asset('storage/app/public/' . $patient->patientProfile->image_path) }}"
                                                 alt="Profile Image" class="rounded-circle me-3"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="avatar-circle bg-primary text-white me-3">
                                                {{ substr($patient->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $patient->name }}</div>
                                            <small class="text-muted">ID: {{ $patient->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $patient->email }}</td>
                                <td>{{ $patient->patientProfile->phone ?? 'N/A' }}</td>
                                <td>
                                    @if($patient->patientProfile && $patient->patientProfile->birth_date)
                                        {{ \Carbon\Carbon::parse($patient->patientProfile->birth_date)->format('M d, Y') }}
                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($patient->patientProfile->birth_date)->age }} years old</small>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge bg-{{ $patient->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($patient->status) }}
                                    </span>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($patient->created_at)->format('M d, Y') }}
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($patient->created_at)->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin-portal.patients.show', $patient->id) }}"
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin-portal.patients.edit', $patient->id) }}"
                                           class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin-portal.patients.delete', $patient->id) }}"
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this patient? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="card-footer">
                {{ $patients->appends(request()->query())->links('vendor.pagination.admin-portal') }}
            </div>
        </div>
    </div>
@else
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-user-injured"></i>
        </div>
        <h4 class="empty-title">No Patients Found</h4>
        <p class="empty-text">
            @if(request()->hasAny(['status', 'search']))
                No patients match your current filters. Try adjusting your search criteria.
            @else
                There are no patients registered in the system yet.
            @endif
        </p>
        @if(request()->hasAny(['status', 'search']))
            <a href="{{ route('admin-portal.patients') }}" class="btn btn-admin-primary">
                <i class="fas fa-times me-1"></i>Clear Filters
            </a>
        @else
            <a href="{{ route('admin-portal.patients.create') }}" class="btn btn-admin-primary">
                <i class="fas fa-plus me-1"></i>Add First Patient
            </a>
        @endif
    </div>
@endif
@endsection

@section('scripts')
<script>
// Avatar circle styles
document.head.insertAdjacentHTML('beforeend', `
    <style>
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
        }
    </style>
`);
</script>
@endsection
