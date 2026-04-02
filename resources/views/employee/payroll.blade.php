@extends('employee.layouts.app')

@section('title', 'My Payroll Records')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card employee-card">
                <div class="card-header bg-success text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-money-check me-2"></i>My Payroll Records
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="text-primary">{{ $totalPayrolls }}</h5>
                                    <small class="text-muted">Total Payrolls</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success">
                                <div class="card-body text-center text-white">
                                    <h5>{{ $paidPayrolls }}</h5>
                                    <small>Paid</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning">
                                <div class="card-body text-center text-white">
                                    <h5>{{ $pendingPayrolls }}</h5>
                                    <small>Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info">
                                <div class="card-body text-center text-white">
                                    <h5>₱{{ number_format($totalEarnings, 2) }}</h5>
                                    <small>Total Earnings</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @if($payrollRecords->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped employee-table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-calendar me-1"></i>Pay Period</th>
                                        <th><i class="fas fa-clock me-1"></i>Hours Worked</th>
                                        <th><i class="fas fa-dollar-sign me-1"></i>Gross Pay</th>
                                        <th><i class="fas fa-minus-circle me-1"></i>Deductions</th>
                                        <th><i class="fas fa-wallet me-1"></i>Net Pay</th>
                                        <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                        <th><i class="fas fa-sticky-note me-1"></i>Notes</th>
                                        <th><i class="fas fa-cogs me-1"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payrollRecords as $payroll)
                                        <tr>
                                            <td>
                                                <strong>{{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('M d') }} - {{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('M d, Y') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('M') }} {{ \Carbon\Carbon::parse($payroll->pay_period_start)->year }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ number_format($payroll->total_hours_worked, 1) }}h
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    Reg: {{ number_format($payroll->regular_hours, 1) }}h |
                                                    OT: {{ number_format($payroll->overtime_hours, 1) }}h
                                                </small>
                                            </td>
                                            <td>
                                                <strong class="text-success">₱{{ number_format($payroll->gross_pay, 2) }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    Rate: ₱{{ number_format($payroll->hourly_rate, 2) }}/hr
                                                    @if($payroll->overtime_hours > 0)
                                                        | OT: ₱{{ number_format($payroll->overtime_rate, 2) }}/hr
                                                    @endif
                                                </small>
                                            </td>
                                            <td>
                                                <span class="text-danger">₱{{ number_format($payroll->deductions, 2) }}</span>
                                                <br>
                                                <small class="text-muted">
                                                    SSS: ₱{{ number_format($payroll->sss_deduction, 2) }}<br>
                                                    PhilHealth: ₱{{ number_format($payroll->philhealth_deduction, 2) }}<br>
                                                    Pag-IBIG: ₱{{ number_format($payroll->pagibig_deduction, 2) }}<br>
                                                    Tax: ₱{{ number_format($payroll->tax_deduction, 2) }}<br>
                                                    Other: ₱{{ number_format($payroll->other_deductions, 2) }}
                                                </small>
                                            </td>
                                            <td>
                                                <strong class="text-primary">₱{{ number_format($payroll->net_pay, 2) }}</strong>
                                                @if($payroll->payment_date)
                                                    <br>
                                                    <small class="text-muted">Paid: {{ \Carbon\Carbon::parse($payroll->payment_date)->format('M d, Y') }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($payroll->status)
                                                    @case('paid')
                                                        <span class="status-badge status-active">
                                                            <i class="fas fa-check-circle me-1"></i>Paid
                                                        </span>
                                                        @break
                                                    @case('processed')
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-cog me-1"></i>Processed
                                                        </span>
                                                        @break
                                                    @case('pending')
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-clock me-1"></i>Pending
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($payroll->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($payroll->notes)
                                                    <small class="text-muted">{{ $payroll->notes }}</small>
                                                @else
                                                    <small class="text-muted">No notes</small>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('employee.payroll.pay-slip', $payroll->id) }}" class="btn btn-sm btn-outline-primary" title="View Pay Slip" target="_blank">
                                                    <i class="fas fa-file-invoice"></i> View
                                                </a>
                                                {{ $payroll->employee_id !== auth()->user()->id }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $payrollRecords->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-money-check fa-3x text-muted"></i>
                            </div>
                            <h4 class="text-muted">No Payroll Records</h4>
                            <p class="text-muted">Your payroll records will appear here once your payroll has been processed.</p>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Note: Payroll processing is managed by your administrator. Contact them if you have questions.
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
