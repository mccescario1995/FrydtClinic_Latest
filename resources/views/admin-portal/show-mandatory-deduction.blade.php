@extends('admin-portal.layouts.app')

@section('title', 'View Mandatory Deduction')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $deduction->name }}</h1>
            <p class="text-muted">Deduction details and employee associations</p>
        </div>
        <div>
            <a href="{{ route('admin-portal.mandatory-deductions.edit', $deduction->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                <i class="fas fa-trash"></i> Delete
            </button>
            <a href="{{ route('admin-portal.mandatory-deductions') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-xl-8">
            <!-- Deduction Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Deduction Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Deduction Type:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $deduction->deduction_type == 'sss' ? 'primary' :
                                                             ($deduction->deduction_type == 'philhealth' ? 'success' :
                                                             ($deduction->deduction_type == 'pagibig' ? 'warning' :
                                                             ($deduction->deduction_type == 'tax' ? 'danger' : 'secondary'))) }}">
                                            {{ ucfirst($deduction->deduction_type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $deduction->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $deduction->is_active ? 'success' : 'secondary' }}">
                                            {{ $deduction->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Percentage Rate:</strong></td>
                                    <td>{{ $deduction->percentage_rate > 0 ? number_format($deduction->percentage_rate, 2) . '%' : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fixed Amount:</strong></td>
                                    <td>{{ $deduction->fixed_amount > 0 ? '₱' . number_format($deduction->fixed_amount, 2) : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Minimum Base Salary:</strong></td>
                                    <td>{{ $deduction->minimum_base_salary > 0 ? '₱' . number_format($deduction->minimum_base_salary, 2) : 'No minimum' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Maximum Deduction:</strong></td>
                                    <td>{{ $deduction->maximum_deduction > 0 ? '₱' . number_format($deduction->maximum_deduction, 2) : 'No maximum' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Effective Date:</strong></td>
                                    <td>{{ $deduction->effective_date ? $deduction->effective_date->format('M d, Y') : 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $deduction->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>{{ $deduction->updated_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($deduction->description)
                        <hr>
                        <div class="mb-3">
                            <strong>Description:</strong>
                            <p class="mt-2">{{ $deduction->description }}</p>
                        </div>
                    @endif

                    @if($deduction->notes)
                        <hr>
                        <div class="mb-3">
                            <strong>Notes:</strong>
                            <p class="mt-2">{{ $deduction->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Employee Deductions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Employee Associations ({{ $employeeDeductions->total() }})</h6>
                    <a href="{{ route('admin-portal.employee-deductions.manage', $deduction->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-cog"></i> Manage Employee Deductions
                    </a>
                </div>
                <div class="card-body">
                    @if($employeeDeductions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Custom Rate</th>
                                        <th>Custom Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employeeDeductions as $employeeDeduction)
                                        <tr>
                                            <td>
                                                <strong>{{ $employeeDeduction->employee->name }}</strong>
                                                <br><small class="text-muted">{{ $employeeDeduction->employee->email }}</small>
                                            </td>
                                            <td>{{ $employeeDeduction->employee->employeeProfile->department ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $employeeDeduction->is_enabled ? 'success' : 'secondary' }}">
                                                    {{ $employeeDeduction->is_enabled ? 'Enabled' : 'Disabled' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($employeeDeduction->custom_percentage_rate)
                                                    {{ number_format($employeeDeduction->custom_percentage_rate, 2) }}%
                                                @else
                                                    <span class="text-muted">Default ({{ number_format($deduction->percentage_rate, 2) }}%)</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($employeeDeduction->custom_fixed_amount)
                                                    ₱{{ number_format($employeeDeduction->custom_fixed_amount, 2) }}
                                                @else
                                                    <span class="text-muted">Default (₱{{ number_format($deduction->fixed_amount, 2) }})</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin-portal.employee-deductions.manage', $employeeDeduction->employee->id) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing {{ $employeeDeductions->firstItem() ?? 0 }} to {{ $employeeDeductions->lastItem() ?? 0 }}
                                of {{ $employeeDeductions->total() }} entries
                            </div>
                            <div>
                                {{ $employeeDeductions->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No employee associations found</h5>
                            <p class="text-muted">This deduction is not yet assigned to any employees.</p>
                            <a href="{{ route('admin-portal.employee-deductions.manage', $deduction->id) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Assign to Employees
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin-portal.mandatory-deductions.edit', $deduction->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Deduction
                        </a>
                        <a href="{{ route('admin-portal.employee-deductions.manage', $deduction->id) }}" class="btn btn-info">
                            <i class="fas fa-users"></i> Manage Employee Deductions
                        </a>
                        <button type="button" class="btn btn-warning" onclick="toggleStatus()">
                            <i class="fas fa-toggle-{{ $deduction->is_active ? 'on' : 'off' }}"></i>
                            {{ $deduction->is_active ? 'Deactivate' : 'Activate' }} Deduction
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <h4 class="text-primary">{{ $deduction->employeeDeductions()->where('is_enabled', true)->count() }}</h4>
                                <small class="text-muted">Active Employees</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <h4 class="text-success">{{ $deduction->employeeDeductions()->where('is_enabled', false)->count() }}</h4>
                                <small class="text-muted">Inactive Employees</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="text-center">
                        <h3 class="text-info">{{ $employeeDeductions->total() }}</h3>
                        <p class="text-muted">Total Employee Associations</p>
                    </div>
                </div>
            </div>

            <!-- Calculation Preview -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Calculation Preview</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="test_salary" class="form-label">Test Salary Amount (₱)</label>
                        <input type="number" class="form-control" id="test_salary"
                               placeholder="Enter test salary" min="0" step="0.01">
                    </div>
                    <div class="preview-result">
                        <div class="alert alert-info">
                            <strong>Calculated Deduction:</strong><br>
                            <span id="preview_amount">₱0.00</span>
                        </div>
                        <small class="text-muted">
                            This calculation assumes the employee meets the minimum salary requirement.
                        </small>
                    </div>
                </div>
            </div>
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
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This action cannot be undone.
                </div>
                <p>Are you sure you want to delete the mandatory deduction "<strong>{{ $deduction->name }}</strong>"?</p>

                @if($deduction->employeeDeductions()->count() > 0)
                    <div class="alert alert-danger">
                        This deduction is currently assigned to {{ $deduction->employeeDeductions()->count() }} employees.
                        You cannot delete it while it has employee associations.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                @if($deduction->employeeDeductions()->count() == 0)
                    <form method="POST" action="{{ route('admin-portal.mandatory-deductions.delete', $deduction->id) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                @else
                    <button type="button" class="btn btn-danger" disabled>Cannot Delete</button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function toggleStatus() {
    const statusText = '{{ $deduction->is_active ? 'deactivate' : 'activate' }}';
    if (confirm(`Are you sure you want to ${statusText} this deduction?`)) {
        // This would typically be handled by a separate endpoint
        // For now, redirect to edit page where status can be changed
        window.location.href = '{{ route('admin-portal.mandatory-deductions.edit', $deduction->id) }}';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const testSalaryInput = document.getElementById('test_salary');
    const previewAmountSpan = document.getElementById('preview_amount');

    function calculatePreview() {
        const salary = parseFloat(testSalaryInput.value) || 0;
        const percentage = {{ $deduction->percentage_rate }};
        const fixedAmount = {{ $deduction->fixed_amount }};
        const minSalary = {{ $deduction->minimum_base_salary }};
        const maxDeduction = {{ $deduction->maximum_deduction ?? 0 }};

        let calculated = 0;

        if (salary >= minSalary) {
            // Calculate percentage-based deduction
            if (percentage > 0) {
                calculated += (salary * percentage) / 100;
            }

            // Add fixed amount
            if (fixedAmount > 0) {
                calculated += fixedAmount;
            }

            // Apply maximum limit if set
            if (maxDeduction > 0 && calculated > maxDeduction) {
                calculated = maxDeduction;
            }
        }

        previewAmountSpan.textContent = '₱' + calculated.toFixed(2);
    }

    testSalaryInput.addEventListener('input', calculatePreview);

    // Calculate with a default test value
    testSalaryInput.value = '25000';
    calculatePreview();
});
</script>
@endpush
