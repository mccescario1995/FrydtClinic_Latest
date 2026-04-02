@extends('admin-portal.layouts.app')

@section('header')
    <section class="container-fluid d-flex justify-content-between align-items-center">
        <div>
            <h2>Payment Management</h2>
            <p class="text-muted">Manage all patient payments and transactions</p>
        </div>
        <div>
            <a href="{{ backpack_url('payments') }}" class="btn btn-primary">
                <i class="la la-refresh"></i> Refresh
            </a>
        </div>
    </section>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">app
                        <h4 class="card-title">Payment Records</h4>
                        <p class="text-muted">Detailed payment records with patient and service information</p>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin-portal.payments') }}" class="btn btn-sm btn-outline-secondary {{ !request('status') ? 'active' : '' }}">All</a>
                            <a href="{{ route('admin-portal.payments', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-warning {{ request('status') === 'pending' ? 'active' : '' }}">Pending</a>
                            <a href="{{ route('admin-portal.payments', ['status' => 'awaiting_approval']) }}" class="btn btn-sm btn-outline-info {{ request('status') === 'awaiting_approval' ? 'active' : '' }}">Awaiting Approval</a>
                            <a href="{{ route('admin-portal.payments', ['status' => 'successful']) }}" class="btn btn-sm btn-outline-success {{ request('status') === 'successful' ? 'active' : '' }}">Successful</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <div class="text-primary mb-2">
                                    <i class="fas fa-file-invoice-dollar fa-2x"></i>
                                </div>
                                <h5 class="text-primary mb-1">{{ $totalPayments }}</h5>
                                <small class="text-muted">Total Payments</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-success">
                            <div class="card-body text-center text-white">
                                <div class="mb-2">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <h5 class="mb-1">{{ $successfulPayments }}</h5>
                                <small>Successful</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-warning">
                            <div class="card-body text-center text-white">
                                <div class="mb-2">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                                <h5 class="mb-1">{{ $pendingPayments }}</h5>
                                <small>Pending</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-info">
                            <div class="card-body text-center text-white">
                                <div class="mb-2">
                                    <i class="fas fa-hourglass-half fa-2x"></i>
                                </div>
                                <h5 class="mb-1">{{ $awaitingApprovalPayments }}</h5>
                                <small>Awaiting Approval</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-danger">
                            <div class="card-body text-center text-white">
                                <div class="mb-2">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                                <h5 class="mb-1">{{ $cancelledPayments }}</h5>
                                <small>Cancelled</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-primary">
                            <div class="card-body text-center text-white">
                                <div class="mb-2">
                                    <i class="fas fa-peso-sign fa-2x"></i>
                                </div>
                                <h5 class="mb-1">₱{{ number_format($totalAmount, 2) }}</h5>
                                <small>Total Amount</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-filter me-2"></i>Filter Payments
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin-portal.payments') }}" class="row g-3">
                            <div class="col-md-3">
                                <label for="patient_id" class="form-label">Patient</label>
                                <select name="patient_id" id="patient_id" class="form-select">
                                    <option value="">All Patients</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="awaiting_approval" {{ request('status') === 'awaiting_approval' ? 'selected' : '' }}>Awaiting Approval</option>
                                    <option value="successful" {{ request('status') === 'successful' ? 'selected' : '' }}>Successful</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select name="payment_method" id="payment_method" class="form-select">
                                    <option value="">All Methods</option>
                                    <option value="paypal" {{ request('payment_method') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                                    <option value="gcash" {{ request('payment_method') === 'gcash' ? 'selected' : '' }}>GCash</option>
                                    <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" name="search" id="search" class="form-control"
                                       value="{{ request('search') }}" placeholder="Patient name or reference">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin-portal.payments') }}" class="btn btn-outline-secondary" title="Clear all filters">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Payments Table -->
                <div class="card">
                    <div class="card-body">
                        @if($payments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-user me-1"></i>Patient</th>
                                            <th><i class="fas fa-hashtag me-1"></i>Reference</th>
                                            <th><i class="fas fa-peso-sign me-1"></i>Amount</th>
                                            <th><i class="fas fa-credit-card me-1"></i>Method</th>
                                            <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                            <th><i class="fas fa-calendar me-1"></i>Date</th>
                                            <th><i class="fas fa-cogs me-1"></i>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payments as $payment)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                             style="width: 40px; height: 40px; font-weight: bold; font-size: 16px;">
                                                            {{ substr($payment->patient->name ?? 'N/A', 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold">{{ $payment->patient->name ?? 'N/A' }}</div>
                                                            <small class="text-muted">{{ $payment->patient->email ?? '' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
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
                                                    @if($payment->status === 'completed' || $payment->status === 'successful')
                                                        <span class="badge bg-success">{{ $payment->status === 'successful' ? 'Approved' : 'Completed' }}</span>
                                                    @elseif($payment->status === 'pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($payment->status === 'awaiting_approval')
                                                        <span class="badge bg-info">Awaiting Approval</span>
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
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin-portal.payments.show', $payment->id) }}"
                                                           class="btn btn-sm btn-outline-info" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($payment->status === 'awaiting_approval')
                                                            <button class="btn btn-sm btn-outline-success"
                                                                    title="Approve Payment"
                                                                    onclick="approvePayment({{ $payment->id }})">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger"
                                                                    title="Reject Payment"
                                                                    onclick="rejectPayment({{ $payment->id }})">
                                                                <i class="fas fa-times"></i>
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
                                {{ $payments->appends(request()->query())->links('vendor.pagination.admin-portal') }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-credit-card fa-3x text-muted"></i>
                                </div>
                                <h4 class="text-muted">No Payment Records Found</h4>
                                <p class="text-muted">No payment records match your current filter criteria.</p>
                                <div class="mt-3">
                                    <a href="{{ route('admin-portal.payments') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Clear Filters
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


<!-- Approve Payment Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Payment</h5>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to approve this payment?</p>
                    <div class="form-group">
                        <label for="approve_notes">Notes (optional)</label>
                        <textarea class="form-control" id="approve_notes" name="notes" rows="3" placeholder="Add any approval notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Payment Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Payment</h5>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to reject this payment?</p>
                    <div class="form-group">
                        <label for="reject_notes">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_notes" name="notes" rows="3" required placeholder="Please provide a reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approvePayment(paymentId) {
    document.getElementById('approveForm').action = `/admin-portal/payments/${paymentId}/approve`;
    var approveModal = new bootstrap.Modal(document.getElementById('approveModal'));
    approveModal.show();
}

function rejectPayment(paymentId) {
    document.getElementById('rejectForm').action = `/admin-portal/payments/${paymentId}/reject`;
    var rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
    rejectModal.show();
}
</script>
@endsection
