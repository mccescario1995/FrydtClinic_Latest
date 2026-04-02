@extends('admin-portal.layouts.app')

@section('title', 'Mandatory Deductions Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Mandatory Deductions</h1>
            <p class="text-muted">Manage mandatory deductions for employees</p>
        </div>
        <div>
            <a href="{{ route('admin-portal.mandatory-deductions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Deduction
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Deductions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalDeductions) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Deductions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($activeDeductions) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Inactive Deductions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($inactiveDeductions) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Employee Deductions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\EmployeeDeduction::count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter & Search</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin-portal.mandatory-deductions') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Search deductions...">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="deduction_type" class="form-label">Deduction Type</label>
                        <select class="form-control" id="deduction_type" name="deduction_type">
                            <option value="">All Types</option>
                            @foreach($deductionTypes as $type => $label)
                                <option value="{{ $type }}" {{ request('deduction_type') == $type ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('admin-portal.mandatory-deductions') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Deductions Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Mandatory Deductions List</h6>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download"></i> Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf text-danger"></i> PDF</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel text-success"></i> Excel</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-print"></i> Print</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($deductions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">ID</th>
                                <th width="15%">Type</th>
                                <th width="20%">Name</th>
                                <th width="15%">Percentage Rate</th>
                                <th width="15%">Fixed Amount</th>
                                <th width="10%">Status</th>
                                <th width="10%">Effective Date</th>
                                <th width="10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deductions as $deduction)
                                <tr>
                                    <td>{{ $deduction->id }}</td>
                                    <td>
                                        <span class="badge bg-{{ $deduction->deduction_type == 'sss' ? 'primary' :
                                                             ($deduction->deduction_type == 'philhealth' ? 'success' :
                                                             ($deduction->deduction_type == 'pagibig' ? 'warning' :
                                                             ($deduction->deduction_type == 'tax' ? 'danger' : 'secondary'))) }}">
                                            {{ ucfirst($deduction->deduction_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $deduction->name }}</strong>
                                        @if($deduction->description)
                                            <br><small class="text-muted">{{ Str::limit($deduction->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($deduction->percentage_rate > 0)
                                            {{ number_format($deduction->percentage_rate, 2) }}%
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($deduction->fixed_amount > 0)
                                            ₱{{ number_format($deduction->fixed_amount, 2) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $deduction->is_active ? 'success' : 'secondary' }}">
                                            {{ $deduction->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $deduction->effective_date ? $deduction->effective_date->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin-portal.mandatory-deductions.show', $deduction->id) }}"
                                               class="btn btn-sm btn-outline-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin-portal.mandatory-deductions.edit', $deduction->id) }}"
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDelete({{ $deduction->id }})" title="Delete">
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
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $deductions->firstItem() ?? 0 }} to {{ $deductions->lastItem() ?? 0 }}
                        of {{ $deductions->total() }} entries
                    </div>
                    <div>
                        {{ $deductions->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No mandatory deductions found</h5>
                    <p class="text-muted">Start by creating your first mandatory deduction.</p>
                    <a href="{{ route('admin-portal.mandatory-deductions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Deduction
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this mandatory deduction? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(deductionId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `{{ url('admin-portal/mandatory-deductions') }}/${deductionId}`;

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Auto-submit filter form when select changes
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('#deduction_type, #status');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}
.btn-group .btn {
    margin-right: 2px;
}
</style>
@endpush
