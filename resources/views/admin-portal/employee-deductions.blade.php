@extends('admin-portal.layouts.app')

@section('title', 'Employee Deductions')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0"><i class="fas fa-money-bill-wave me-2"></i>Employee Deductions</h1>
                    <p class="text-muted mb-0">Select an employee to manage their mandatory deductions</p>
                </div>
                <div>
                    <a href="{{ route('admin-portal.mandatory-deductions') }}" class="btn btn-admin-primary">
                        <i class="fas fa-plus me-1"></i>Manage Deductions
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid mb-4">
        <div class="stats-card">
            <div class="card-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-title">Total Employees</div>
            <div class="card-value">{{ $totalEmployees }}</div>
        </div>
        <div class="stats-card">
            <div class="card-icon">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="card-title">With Deductions</div>
            <div class="card-value">{{ $employeesWithDeductions }}</div>
        </div>
        <div class="stats-card">
            <div class="card-icon">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="card-title">Without Deductions</div>
            <div class="card-value">{{ $employeesWithoutDeductions }}</div>
        </div>
        <div class="stats-card">
            <div class="card-icon">
                <i class="fas fa-user-clock"></i>
            </div>
            <div class="card-title">Filtered Employees</div>
            <div class="card-value">{{ $employees->total() }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" action="{{ route('admin-portal.employee-deductions') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search Employee</label>
                <input type="text" class="form-control" id="search" name="search"
                       value="{{ request('search') }}"
                       placeholder="Search by name or email...">
            </div>
            <div class="col-md-3">
                <label for="position" class="form-label">Position</label>
                <select class="form-select" id="position" name="position">
                    <option value="">All Positions</option>
                    @php
                        $positions = \App\Models\User::where('user_type', 'employee')
                            ->join('employee_profiles', 'users.id', '=', 'employee_profiles.employee_id')
                            ->whereNotNull('employee_profiles.position')
                            ->distinct()
                            ->pluck('employee_profiles.position')
                            ->filter()
                            ->sort()
                            ->values();
                    @endphp
                    @foreach($positions as $pos)
                        <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>
                            {{ $pos }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="deduction_status" class="form-label">Deduction Status</label>
                <select class="form-select" id="deduction_status" name="deduction_status">
                    <option value="">All Employees</option>
                    <option value="with_deductions" {{ request('deduction_status') == 'with_deductions' ? 'selected' : '' }}>
                        Has Deductions
                    </option>
                    <option value="without_deductions" {{ request('deduction_status') == 'without_deductions' ? 'selected' : '' }}>
                        No Deductions
                    </option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-admin-primary me-2">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                @if(request()->hasAny(['search', 'position', 'deduction_status']))
                    <a href="{{ route('admin-portal.employee-deductions') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Employee Selection -->
    <div class="row">
        <div class="col-12">
            <div class="admin-card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-users me-2"></i>Select Employee to Manage</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($employees as $employee)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100 employee-card" style="cursor: pointer;" onclick="window.location.href='{{ route('admin-portal.employee-deductions.manage', $employee->id) }}'">
                                    <div class="card-body text-center">
                                        @if ($employee->employeeProfile && $employee->employeeProfile->image_path)
                                            <img src="{{ asset('storage/app/public/' . $employee->employeeProfile->image_path) }}"
                                                alt="Profile Image" class="rounded-circle mb-3"
                                                style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                                style="width: 60px; height: 60px; font-weight: bold; font-size: 24px;">
                                                {{ substr($employee->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <h6 class="card-title mb-1">{{ $employee->name }}</h6>
                                        <p class="card-text text-muted small mb-2">{{ $employee->email }}</p>
                                        <div class="mb-2">
                                            @php
                                                $deductionCount = $employee->employeeDeductions()->enabled()->count();
                                            @endphp
                                            <span class="badge bg-{{ $deductionCount > 0 ? 'success' : 'secondary' }}">
                                                {{ $deductionCount }} deduction{{ $deductionCount !== 1 ? 's' : '' }}
                                            </span>
                                        </div>
                                        <small class="text-primary">Click to manage deductions</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="empty-title">No Employees Found</div>
                                    <div class="empty-text">
                                        There are no employees in the system yet.
                                    </div>
                                    <a href="{{ route('admin-portal.users.create') }}" class="btn btn-admin-primary">
                                        <i class="fas fa-plus me-1"></i>Add First Employee
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if ($employees->hasPages())
        <div class="mt-4">
            {{ $employees->appends(request()->query())->links('vendor.pagination.admin-portal') }}
        </div>
    @endif

@endsection

@push('styles')
<style>
.employee-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.employee-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-color: #007bff;
}
</style>
@endpush
