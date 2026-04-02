@extends('admin-portal.layouts.app')

@section('title', 'User Management')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"><i class="fas fa-users me-2"></i>User Management</h1>
            <p class="page-subtitle">Manage system users, patients, and employees</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary fs-6">{{ $users->total() }} Total Users</span>
            <a href="{{ route('admin-portal.users.create') }}" class="btn btn-admin-primary">
                <i class="fas fa-plus me-1"></i>Create User
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-section">
    <form method="GET" action="{{ route('admin-portal.users') }}" class="row g-3">
            <div class="col-md-3">
                <label for="type" class="form-label">User Type</label>
                <select name="type" id="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="patient" {{ request('type') === 'patient' ? 'selected' : '' }}>Patients</option>
                    <option value="employee" {{ request('type') === 'employee' ? 'selected' : '' }}>Employees</option>
                    <option value="admin" {{ request('type') === 'admin' ? 'selected' : '' }}>Admins</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" class="form-control"
                       placeholder="Search by name or email" value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-1"></i>Filter
                </button>
                <a href="{{ route('admin-portal.users') }}" class="btn btn-outline-secondary" title="Clear all filters">
                    <i class="fas fa-times me-1"></i>Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="content-section">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-list me-2"></i>Users</h2>
        <p class="section-subtitle">Complete list of system users with their details and status</p>
    </div>

    <div class="admin-card">
        <div class="card-body">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table admin-table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Type</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($user->user_type === 'patient' && $user->patientProfile && $user->patientProfile->image_path)
                                            <img src="{{ asset('storage/app/public/' . $user->patientProfile->image_path) }}"
                                                 alt="Profile Image" class="rounded-circle me-3"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @elseif($user->user_type === 'employee' && $user->employeeProfile && $user->employeeProfile->image_path)
                                            <img src="{{ asset('storage/app/public/' . $user->employeeProfile->image_path) }}"
                                                 alt="Profile Image" class="rounded-circle me-3"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-{{ $user->user_type === 'patient' ? 'success' : ($user->user_type === 'employee' ? 'info' : 'warning') }} text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                 style="width: 40px; height: 40px; font-weight: bold; font-size: 16px;">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $user->name }}</div>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $typeClass = match($user->user_type) {
                                            'patient' => 'success',
                                            'employee' => 'info',
                                            'admin' => 'warning',
                                            default => 'secondary'
                                        };
                                        $typeIcon = match($user->user_type) {
                                            'patient' => 'user-injured',
                                            'employee' => 'user-md',
                                            'admin' => 'user-shield',
                                            default => 'user'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $typeClass }}">
                                        <i class="fas fa-{{ $typeIcon }} me-1"></i>{{ ucfirst($user->user_type) }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->user_type === 'patient' && $user->patientProfile)
                                        <div>
                                            <div><i class="fas fa-phone me-1"></i>{{ $user->patientProfile->phone ?? 'N/A' }}</div>
                                            @if($user->patientProfile->address)
                                                <small class="text-muted">{{ Str::limit($user->patientProfile->address, 30) }}</small>
                                            @endif
                                        </div>
                                    @elseif($user->user_type === 'employee' && $user->employeeProfile)
                                        <div>
                                            <div><i class="fas fa-phone me-1"></i>{{ $user->employeeProfile->phone ?? 'N/A' }}</div>
                                            @if($user->employeeProfile->address)
                                                <small class="text-muted">{{ Str::limit($user->employeeProfile->address, 30) }}</small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">No contact info</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                        <i class="fas fa-{{ $user->status === 'active' ? 'check-circle' : 'pause-circle' }} me-1"></i>
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div>{{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin-portal.users.show', $user->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin-portal.users.edit', $user->id) }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->user_type === 'patient')
                                            <a href="{{ route('admin-portal.patients.show', $user->id) }}"
                                               class="btn btn-sm btn-outline-success"
                                               title="Manage Patient">
                                                <i class="fas fa-user-md"></i>
                                            </a>
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
                {{ $users->appends(request()->query())->links('vendor.pagination.admin-portal') }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h5 class="empty-title">No Users Found</h5>
                <p class="empty-text">No users match your current filter criteria.</p>
                <a href="{{ route('admin-portal.users') }}" class="btn btn-admin-primary">
                    <i class="fas fa-times me-1"></i>Clear Filters
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
