@extends('admin-portal.layouts.app')

@section('header')
    <section class="container-fluid d-flex justify-content-between align-items-center">
        <div>
            <h2>Payment Details</h2>
            <p class="text-muted">Payment reference: {{ $payment->payment_reference }}</p>
        </div>
        <div>
            <a href="{{ backpack_url('payments') }}" class="btn btn-outline-secondary">
                <i class="la la-arrow-left"></i> Back to Payments
            </a>
        </div>
    </section>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Payment Information -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Payment Information</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Payment Reference:</strong><br>
                        <span class="badge bg-info">{{ $payment->payment_reference }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong><br>
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
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Amount:</strong><br>
                        <span class="h4 text-primary">₱{{ number_format($payment->amount, 2) }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Payment Method:</strong><br>
                        @if($payment->payment_method === 'paypal')
                            <span class="badge bg-primary">PayPal</span>
                        @elseif($payment->payment_method === 'gcash')
                            <span class="badge bg-success">GCash</span>
                        @elseif($payment->payment_method === 'cash')
                            <span class="badge bg-warning">Cash</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($payment->payment_method) }}</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Created Date:</strong><br>
                        {{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y h:i A') }}
                    </div>
                    <div class="col-md-6">
                        <strong>Paid Date:</strong><br>
                        @if($payment->paid_at)
                            {{ \Carbon\Carbon::parse($payment->paid_at)->format('M d, Y h:i A') }}
                        @else
                            <span class="text-muted">Not paid yet</span>
                        @endif
                    </div>
                </div>

                @if($payment->description)
                <div class="mb-3">
                    <strong>Description:</strong><br>
                    {{ $payment->description }}
                </div>
                @endif

                @if($payment->notes)
                <div class="mb-3">
                    <strong>Notes:</strong><br>
                    {{ $payment->notes }}
                </div>
                @endif

                @if($payment->gcash_reference)
                <div class="mb-3">
                    <strong>GCash Reference:</strong><br>
                    <span class="badge bg-success">{{ $payment->gcash_reference }}</span>
                </div>
                @endif

                @if($payment->proof_of_payment_path)
                <div class="mb-3">
                    <strong>Proof of Payment:</strong><br>
                    <img src="{{ asset('storage/app/public/' . $payment->proof_of_payment_path) }}" alt="Proof of Payment" class="img-fluid" style="max-width: 300px;">
                    @if($payment->proof_uploaded_at)
                        <br><small class="text-muted">Uploaded on: {{ $payment->proof_uploaded_at->format('M d, Y h:i A') }}</small>
                    @endif
                    @if($payment->proof_of_payment_notes)
                        <br><small class="text-muted">Notes: {{ $payment->proof_of_payment_notes }}</small>
                    @endif
                </div>
                @endif

                @if($payment->approved_by)
                <div class="mb-3">
                    <strong>Approved By:</strong><br>
                    {{ $payment->approver->name ?? 'N/A' }}
                    @if($payment->approved_at)
                        <br><small class="text-muted">Approved on: {{ $payment->approved_at->format('M d, Y h:i A') }}</small>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Payment Items -->
        <div class="card mt-4">
            <div class="card-header">
                <h4 class="card-title">Payment Items</h4>
            </div>
            <div class="card-body">
                @if($payment->items->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payment->items as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->service_name }}</strong>
                                            @if($item->service)
                                                <br><small class="text-muted">{{ $item->service->description }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                        <td><strong>₱{{ number_format($item->total_price, 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                    <td><strong class="text-primary">₱{{ number_format($payment->amount, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No payment items found.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Patient Information -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="card-title">Patient Information</h4>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                         style="width: 60px; height: 60px; font-weight: bold; font-size: 24px;">
                        {{ substr($payment->patient->name ?? 'N/A', 0, 1) }}
                    </div>
                </div>
                <h5 class="text-center mb-3">{{ $payment->patient->name ?? 'N/A' }}</h5>
                <p><strong>Email:</strong> {{ $payment->patient->email ?? 'N/A' }}</p>
                <p><strong>Phone:</strong> {{ $payment->patient->patientProfile->phone ?? 'N/A' }}</p>
                <p><strong>Address:</strong> {{ $payment->patient->patientProfile->address ?? 'N/A' }}</p>
                <a href="{{ route('admin-portal.patients.show', $payment->patient->id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye me-1"></i>View Patient Details
                </a>
            </div>
        </div>

        <!-- Appointment Information -->
        @if($payment->appointment)
        <div class="card mt-4">
            <div class="card-header bg-warning text-white">
                <h4 class="card-title">Related Appointment</h4>
            </div>
            <div class="card-body">
                <p><strong>Service:</strong> {{ $payment->appointment->service->name ?? 'N/A' }}</p>
                <p><strong>Date & Time:</strong><br>
                {{ \Carbon\Carbon::parse($payment->appointment->appointment_datetime)->format('M d, Y h:i A') }}</p>
                <p><strong>Status:</strong>
                    <span class="badge bg-{{ $payment->appointment->status === 'confirmed' ? 'success' : ($payment->appointment->status === 'scheduled' ? 'primary' : 'secondary') }}">
                        {{ ucfirst($payment->appointment->status) }}
                    </span>
                </p>
            </div>
        </div>
        @endif

        <!-- Payment Actions -->
        @if($payment->status === 'awaiting_approval' || $payment->status === 'pending')
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title">Payment Actions</h4>
            </div>
            <div class="card-body">
                <!-- GCash Proof of Payment Actions -->
                <form method="POST" action="{{ route('admin-portal.payments.approve', $payment->id) }}" class="mb-3">
                    @csrf
                    <div class="mb-3">
                        <label for="approve_notes" class="form-label">Approval Notes (optional)</label>
                        <textarea class="form-control" id="approve_notes" name="notes" rows="3" placeholder="Add any notes about the approval..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-check me-1"></i>Approve Payment
                    </button>
                </form>

                <form method="POST" action="{{ route('admin-portal.payments.reject', $payment->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="reject_notes" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_notes" name="notes" rows="3" placeholder="Please provide a reason for rejection..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-times me-1"></i>Reject Payment
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
