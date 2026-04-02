@extends('patient.layouts.app')

@section('title', 'Billing - FRYDT Patient Portal')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Billing Statistics -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body stat-card">
                            <div class="stat-number text-primary">₱{{ number_format($totalBilled, 2) }}</div>
                            <div class="stat-label">Total Billed</div>
                            <i class="fas fa-file-invoice-dollar fa-2x text-primary opacity-25 mt-2"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body stat-card">
                            <div class="stat-number text-success">₱{{ number_format($totalPaid, 2) }}</div>
                            <div class="stat-label">Total Paid</div>
                            <i class="fas fa-check-circle fa-2x text-success opacity-25 mt-2"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body stat-card">
                            <div class="stat-number {{ $totalOutstanding > 0 ? 'text-warning' : 'text-muted' }}">₱{{ number_format($totalOutstanding, 2) }}</div>
                            <div class="stat-label">Outstanding</div>
                            <i class="fas fa-clock fa-2x {{ $totalOutstanding > 0 ? 'text-warning' : 'text-muted' }} opacity-25 mt-2"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body stat-card">
                            <div class="stat-number {{ $overdueCount > 0 ? 'text-danger' : 'text-muted' }}">{{ $overdueCount }}</div>
                            <div class="stat-label">Overdue Bills</div>
                            <i class="fas fa-exclamation-triangle fa-2x {{ $overdueCount > 0 ? 'text-danger' : 'text-muted' }} opacity-25 mt-2"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @if($totalOutstanding > 0)
            <div class="alert alert-warning mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle me-2"></i>
                    <div>
                        <strong>Outstanding Balance:</strong> You have ₱{{ number_format($totalOutstanding, 2) }} remaining to pay.
                        <a href="{{ route('patient.appointments') }}" class="alert-link ms-2">Make a Payment</a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Billing Records -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>My Billing Records
                    </h5>
                </div>
                <div class="card-body">
                    @if($billingRecords->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="fas fa-hashtag me-1"></i>Invoice #</th>
                                        <th><i class="fas fa-calendar me-1"></i>Date</th>
                                        <th><i class="fas fa-file-alt me-1"></i>Description</th>
                                        <th><i class="fas fa-peso-sign me-1"></i>Total</th>
                                        <th><i class="fas fa-check me-1"></i>Paid</th>
                                        <th><i class="fas fa-balance-scale me-1"></i>Balance</th>
                                        <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                        <th><i class="fas fa-cogs me-1"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($billingRecords as $billing)
                                    <tr class="{{ $billing->isOverdue() ? 'table-warning' : '' }}">
                                        <td>
                                            <strong>{{ $billing->invoice_number }}</strong>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $billing->invoice_date->format('M j, Y') }}</div>
                                            <small class="text-muted">Due: {{ $billing->due_date->format('M j, Y') }}</small>
                                        </td>
                                        <td>
                                            <div>{{ Str::limit($billing->billing_description, 40) }}</div>
                                            @if($billing->services_rendered && count($billing->services_rendered) > 0)
                                                <small class="text-muted">{{ count($billing->services_rendered) }} service(s)</small>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-primary">₱{{ number_format($billing->total_amount, 2) }}</td>
                                        <td class="text-success">₱{{ number_format($billing->amount_paid, 2) }}</td>
                                        <td class="{{ $billing->balance_due > 0 ? 'text-warning fw-bold' : 'text-muted' }}">
                                            ₱{{ number_format($billing->balance_due, 2) }}
                                        </td>
                                        <td>
                                            @if($billing->payment_status === 'paid')
                                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Paid</span>
                                            @elseif($billing->payment_status === 'overdue')
                                                <span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i>Overdue</span>
                                            @elseif($billing->payment_status === 'partial')
                                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Partial</span>
                                            @else
                                                <span class="badge bg-secondary"><i class="fas fa-clock me-1"></i>{{ ucfirst($billing->payment_status) }}</span>
                                            @endif
                                            @if($billing->philhealth_member)
                                                <br><small class="text-info"><i class="fas fa-shield-alt me-1"></i>PhilHealth</small>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('patient.billing-print', $billing->id) }}"
                                               class="btn btn-sm btn-outline-primary" title="Print Receipt" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @if($billing->balance_due > 0)
                                                <a href="{{ route('patient.appointments') }}"
                                                   class="btn btn-sm btn-outline-success ms-1" title="Make Payment">
                                                    <i class="fas fa-credit-card"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $billingRecords->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No billing records found</h6>
                            <p class="text-muted">Your billing history will appear here once services are rendered.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
