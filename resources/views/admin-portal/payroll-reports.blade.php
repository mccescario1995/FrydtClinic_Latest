@extends('admin-portal.layouts.app')

@section('title', 'Payroll Reports')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-chart-bar me-2"></i>Payroll Reports
    </h1>
    <p class="page-subtitle">Comprehensive payroll reports and analytics</p>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="admin-card p-3 text-center">
            <div class="text-primary mb-2">
                <i class="fas fa-users fa-2x"></i>
            </div>
            <h4 class="mb-1">{{ number_format($totalEmployees) }}</h4>
            <p class="text-muted mb-0">Employees Paid</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card p-3 text-center">
            <div class="text-success mb-2">
                <i class="fas fa-dollar-sign fa-2x"></i>
            </div>
            <h4 class="mb-1">₱{{ number_format($totalGrossPay, 2) }}</h4>
            <p class="text-muted mb-0">Total Gross Pay</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card p-3 text-center">
            <div class="text-danger mb-2">
                <i class="fas fa-minus-circle fa-2x"></i>
            </div>
            <h4 class="mb-1">₱{{ number_format($totalDeductions, 2) }}</h4>
            <p class="text-muted mb-0">Total Deductions</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card p-3 text-center">
            <div class="text-info mb-2">
                <i class="fas fa-wallet fa-2x"></i>
            </div>
            <h4 class="mb-1">₱{{ number_format($totalNetPay, 2) }}</h4>
            <p class="text-muted mb-0">Total Net Pay</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-section">
    <form method="GET" action="{{ route('admin-portal.payroll-reports') }}" class="row g-3">
        <div class="col-md-4">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control"
                   value="{{ request('start_date') }}">
        </div>
        <div class="col-md-4">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control"
                   value="{{ request('end_date') }}">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-search me-1"></i>Generate Report
            </button>
            <a href="{{ route('admin-portal.payroll-reports') }}" class="btn btn-outline-secondary" title="Clear filters">
                <i class="fas fa-times me-1"></i>Clear
            </a>
        </div>
    </form>
</div>

<!-- Payroll Report Table -->
<div class="content-section">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-table me-2"></i>Payroll Report Details</h2>
        <p class="section-subtitle">Detailed breakdown of payroll payments</p>
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
                                <th>Payment Date</th>
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
                                        @if($payroll->payment_date)
                                            {{ \Carbon\Carbon::parse($payroll->payment_date)->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-dark">
                                <th colspan="3">TOTALS</th>
                                <th>₱{{ number_format($totalGrossPay, 2) }}</th>
                                <th>₱{{ number_format($totalDeductions, 2) }}</th>
                                <th>₱{{ number_format($totalNetPay, 2) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Export Options -->
                <div class="mt-4 text-center">
                    <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>Print Report
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="exportToCSV()">
                        <i class="fas fa-file-csv me-1"></i>Export to CSV
                    </button>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h5 class="empty-title">No Payroll Data Found</h5>
                    <p class="empty-text">No payroll data found for the selected date range.</p>
                    <a href="{{ route('admin-portal.payroll-reports') }}" class="btn btn-admin-primary">
                        <i class="fas fa-times me-1"></i>Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function exportToCSV() {
    // Simple CSV export functionality
    let csv = 'Employee,Pay Period Start,Pay Period End,Hours Worked,Gross Pay,Deductions,Net Pay,Payment Date\n';

    @foreach($payrolls as $payroll)
        csv += '"{{ $payroll->employee->name }}",';
        csv += '"{{ $payroll->pay_period_start }}",';
        csv += '"{{ $payroll->pay_period_end }}",';
        csv += '"{{ $payroll->total_hours_worked }}",';
        csv += '"{{ $payroll->gross_pay }}",';
        csv += '"{{ $payroll->deductions }}",';
        csv += '"{{ $payroll->net_pay }}",';
        csv += '"{{ $payroll->payment_date }}"\n';
    @endforeach

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'payroll-report-{{ now()->format("Y-m-d") }}.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}
</script>
@endsection
