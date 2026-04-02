@extends('employee.layouts.app')

@section('title', 'Payment History - ' . $patient->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card employee-card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-credit-card me-2"></i>Payment History
                    </h4>
                    <small class="text-white-50">Payment records for {{ $patient->name }}</small>
                </div>
                <div class="card-body">
                    <!-- Patient Information -->
                    <div class="row mb-4">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                                 style="width: 60px; height: 60px; font-weight: bold; font-size: 24px;">
                                                {{ substr($patient->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h4 class="mb-1">{{ $patient->name }}</h4>
                                            <p class="text-muted mb-0">{{ $patient->email }}</p>
                                            <p class="text-muted mb-0">{{ $patient->patientProfile->phone ?? 'No phone' }}</p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <a href="{{ route('employee.patients.create-payment', $patient->id) }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-1"></i>Create Payment
                                            </a>
                                            <br>
                                            <a href="{{ route('employee.patients.medical-records', $patient->id) }}" class="btn btn-outline-secondary btn-sm mt-2">
                                                <i class="fas fa-arrow-left me-1"></i>Back
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Statistics -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-chart-line me-2"></i>Payment Summary
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="h4 mb-1 text-success">₱{{ number_format($totalPaid, 2) }}</div>
                                            <small class="text-muted">Total Paid</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="h4 mb-1 text-warning">₱{{ number_format($pendingAmount, 2) }}</div>
                                            <small class="text-muted">Pending</small>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="text-center">
                                        <div class="h5 mb-1">{{ $completedCount }}</div>
                                        <small class="text-muted">Completed Payments</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payments Table -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>Payment Records
                            </h6>
                            <small class="text-muted">All payment transactions for this patient</small>
                        </div>
                        <div class="card-body">
                            @if($payments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped employee-table">
                                        <thead>
                                            <tr>
                                                <th><i class="fas fa-hashtag me-1"></i>Reference</th>
                                                <th><i class="fas fa-peso-sign me-1"></i>Amount</th>
                                                <th><i class="fas fa-credit-card me-1"></i>Method</th>
                                                <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                                <th><i class="fas fa-calendar me-1"></i>Date</th>
                                                <th><i class="fas fa-list me-1"></i>Services</th>
                                                <th><i class="fas fa-cogs me-1"></i>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($payments as $payment)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-secondary">{{ $payment->payment_reference }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold text-primary">₱{{ number_format($payment->amount, 2) }}</span>
                                                    </td>
                                                    <td>
                                                        @if($payment->payment_method === 'paypal')
                                                            <span class="badge bg-primary">PayPal</span>
                                                        @elseif($payment->payment_method === 'gcash')
                                                            <span class="badge bg-success">GCash</span>
                                                        @elseif($payment->payment_method === 'cash')
                                                            <span class="badge bg-warning">Cash</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ ucfirst($payment->payment_method) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($payment->status === 'completed')
                                                            <span class="status-badge status-active">Completed</span>
                                                        @elseif($payment->status === 'pending')
                                                            <span class="status-badge status-inactive">Pending</span>
                                                        @elseif($payment->status === 'failed')
                                                            <span class="badge bg-danger">Failed</span>
                                                        @elseif($payment->status === 'cancelled')
                                                            <span class="badge bg-secondary">Cancelled</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ ucfirst($payment->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="fw-bold">{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y') }}</div>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($payment->created_at)->format('h:i A') }}</small>
                                                    </td>
                                                    <td>
                                                        @if($payment->items->count() > 0)
                                                            <small>
                                                                @foreach($payment->items as $item)
                                                                    {{ $item->service_name }}<br>
                                                                @endforeach
                                                            </small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('employee.payments.show', $payment->id) }}"
                                                           class="btn btn-sm btn-outline-info" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $payments->appends(request()->query())->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="fas fa-credit-card fa-3x text-muted"></i>
                                    </div>
                                    <h4 class="text-muted">No Payment Records</h4>
                                    <p class="text-muted">This patient has no payment records yet.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('employee.patients.create-payment', $patient->id) }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>Create First Payment
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
