@extends('admin-portal.layouts.app')

@section('title', 'Payroll Management')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-money-check me-2"></i>Payroll Management
    </h1>
    <p class="page-subtitle">Manage employee payroll, generate pay slips, and track payments</p>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="admin-card p-3 text-center">
            <div class="text-primary mb-2">
                <i class="fas fa-file-invoice-dollar fa-2x"></i>
            </div>
            <h4 class="mb-1">{{ number_format($totalPayrolls) }}</h4>
            <p class="text-muted mb-0">Total Payrolls</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card p-3 text-center">
            <div class="text-warning mb-2">
                <i class="fas fa-clock fa-2x"></i>
            </div>
            <h4 class="mb-1">{{ number_format($pendingPayrolls) }}</h4>
            <p class="text-muted mb-0">Pending</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card p-3 text-center">
            <div class="text-info mb-2">
                <i class="fas fa-cogs fa-2x"></i>
            </div>
            <h4 class="mb-1">{{ number_format($processedPayrolls) }}</h4>
            <p class="text-muted mb-0">Processed</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card p-3 text-center">
            <div class="text-success mb-2">
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
            <h4 class="mb-1">₱{{ number_format($totalAmountPaid, 2) }}</h4>
            <p class="text-muted mb-0">Total Paid</p>
        </div>
    </div>
</div>

<!-- Generate Payroll Button -->
<div class="mb-4">
    <button type="button" class="btn btn-admin-primary" data-bs-toggle="modal" data-bs-target="#generatePayrollModal">
        <i class="fas fa-plus me-1"></i>Generate Payroll
    </button>
    <a href="{{ route('admin-portal.payroll-reports') }}" class="btn btn-outline-info">
        <i class="fas fa-chart-bar me-1"></i>Payroll Reports
    </a>
</div>

<!-- Filters -->
<div class="filter-section">
    <form method="GET" action="{{ route('admin-portal.payroll') }}" class="row g-3">
        <div class="col-md-3">
            <label for="employee_id" class="form-label">Employee</label>
            <select name="employee_id" id="employee_id" class="form-select">
                <option value="">All Employees</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processed" {{ request('status') === 'processed' ? 'selected' : '' }}>Processed</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="pay_period_start" class="form-label">Period Start</label>
            <input type="date" name="pay_period_start" id="pay_period_start" class="form-control"
                   value="{{ request('pay_period_start') }}">
        </div>
        <div class="col-md-2">
            <label for="pay_period_end" class="form-label">Period End</label>
            <input type="date" name="pay_period_end" id="pay_period_end" class="form-control"
                   value="{{ request('pay_period_end') }}">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-search me-1"></i>Filter
            </button>
            <a href="{{ route('admin-portal.payroll') }}" class="btn btn-outline-secondary" title="Clear all filters">
                <i class="fas fa-times me-1"></i>Clear
            </a>
        </div>
    </form>
</div>

<!-- Payroll Table -->
<div class="content-section">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-list me-2"></i>Payroll Records</h2>
        <p class="section-subtitle">Employee payroll information and payment status</p>
    </div>

    <div class="admin-card">
        <div class="card-body">
            @if($payrolls->count() > 0)
                <div class="table-responsive">
                    <table class="table admin-table table-hover">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Pay Period</th>
                                <th>Hours Worked</th>
                                <th>Gross Pay</th>
                                <th>Deductions</th>
                                <th>Net Pay</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payrolls as $payroll)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                 style="width: 40px; height: 40px; font-weight: bold; font-size: 16px;">
                                                {{ substr($payroll->employee->name ?? 'N/A', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $payroll->employee->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $payroll->employee->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('M d') }} - {{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('M d, Y') }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ number_format($payroll->total_hours_worked, 1) }}h</span>
                                        <br><small class="text-muted">Reg: {{ number_format($payroll->regular_hours, 1) }}h, OT: {{ number_format($payroll->overtime_hours, 1) }}h</small>
                                    </td>
                                    <td>
                                        <span class="text-success fw-bold">₱{{ number_format($payroll->gross_pay, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-danger">₱{{ number_format($payroll->deductions, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-primary fw-bold">₱{{ number_format($payroll->net_pay, 2) }}</span>
                                    </td>
                                    <td>
                                        @if($payroll->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($payroll->status === 'processed')
                                            <span class="badge bg-info">Processed</span>
                                        @elseif($payroll->status === 'paid')
                                            <span class="badge bg-success">Paid</span>
                                            @if($payroll->payment_date)
                                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($payroll->payment_date)->format('M d, Y') }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin-portal.payroll.pay-slip', $payroll->id) }}" class="btn btn-sm btn-outline-primary" title="View Pay Slip" target="_blank">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                            @if($payroll->status === 'pending')
                                                <button class="btn btn-sm btn-outline-info" title="Process Payroll"
                                                        onclick="processPayroll({{ $payroll->id }}, '{{ $payroll->employee->name }}')">
                                                    <i class="fas fa-cogs"></i>
                                                </button>
                                            @elseif($payroll->status === 'processed')
                                                <button class="btn btn-sm btn-outline-success" title="Mark as Paid"
                                                        onclick="markAsPaid({{ $payroll->id }}, '{{ $payroll->employee->name }}')">
                                                    <i class="fas fa-check"></i>
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
                    {{ $payrolls->appends(request()->query())->links('vendor.pagination.admin-portal') }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-money-check"></i>
                    </div>
                    <h5 class="empty-title">No Payroll Records Found</h5>
                    <p class="empty-text">No payroll records match your current filter criteria.</p>
                    <button type="button" class="btn btn-admin-primary" data-bs-toggle="modal" data-bs-target="#generatePayrollModal">
                        <i class="fas fa-plus me-1"></i>Generate Payroll
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Generate Payroll Modal -->
<div class="modal fade" id="generatePayrollModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Payroll</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin-portal.payroll.generate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pay_period_start" class="form-label">Pay Period Start</label>
                                <input type="date" class="form-control" id="pay_period_start" name="pay_period_start" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pay_period_end" class="form-label">Pay Period End</label>
                                <input type="date" class="form-control" id="pay_period_end" name="pay_period_end" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="overtime_rate" class="form-label">Overtime Rate (₱)</label>
                                <input type="number" class="form-control" id="overtime_rate" name="overtime_rate" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="mb-3">Deductions Breakdown</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sss_deduction" class="form-label">SSS (₱)</label>
                                <input type="number" class="form-control" id="sss_deduction" name="sss_deduction" step="0.01" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="philhealth_deduction" class="form-label">PhilHealth (₱)</label>
                                <input type="number" class="form-control" id="philhealth_deduction" name="philhealth_deduction" step="0.01" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pagibig_deduction" class="form-label">Pag-IBIG (₱)</label>
                                <input type="number" class="form-control" id="pagibig_deduction" name="pagibig_deduction" step="0.01" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tax_deduction" class="form-label">Withholding Tax (₱)</label>
                                <input type="number" class="form-control" id="tax_deduction" name="tax_deduction" step="0.01" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="other_deductions" class="form-label">Other Deductions (₱)</label>
                                <input type="number" class="form-control" id="other_deductions" name="other_deductions" step="0.01" min="0" value="0">
                                <div class="form-text">Insurance, loans, or other deductions</div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Employees</label>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            @foreach($employees as $employee)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="employee_ids[]" value="{{ $employee->id }}" id="employee_{{ $employee->id }}">
                                    <label class="form-check-label" for="employee_{{ $employee->id }}">
                                        {{ $employee->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAllEmployees()">Select All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllEmployees()">Deselect All</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-admin-primary">Generate Payroll</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function selectAllEmployees() {
    document.querySelectorAll('input[name="employee_ids[]"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllEmployees() {
    document.querySelectorAll('input[name="employee_ids[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}

function processPayroll(payrollId, employeeName) {
    if (confirm(`Are you sure you want to process the payroll for ${employeeName}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin-portal/payroll/${payrollId}/process`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        document.body.appendChild(form);
        form.submit();
    }
}

function markAsPaid(payrollId, employeeName) {
    if (confirm(`Are you sure you want to mark the payroll as paid for ${employeeName}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin-portal/payroll/${payrollId}/mark-paid`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
