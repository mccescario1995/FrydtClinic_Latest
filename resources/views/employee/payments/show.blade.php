@extends('employee.layouts.app')

@section('title', 'Payment Details - ' . $payment->payment_reference)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card employee-card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-credit-card me-2"></i>Payment Details
                    </h4>
                    <small class="text-white-50 ">Payment reference: {{ $payment->payment_reference }}</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Payment Information -->
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Payment Information
                                    </h6>
                                </div>
                                <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Payment Reference:</strong><br>
                        <span class="badge bg-info">{{ $payment->payment_reference }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong><br>
                        @if($payment->status === 'completed')
                            <span class="badge bg-success">Completed</span>
                        @elseif($payment->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
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
            </div>
        </div>

                                    <!-- Payment Items -->
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">
                                                <i class="fas fa-list me-2"></i>Payment Items
                                            </h6>
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

                                <!-- Patient & Appointment Information -->
                                <div class="col-lg-4">
                                    <!-- Patient Information -->
                                    <div class="card mb-4">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="card-title mb-0">
                                                <i class="fas fa-user me-2"></i>Patient Information
                                            </h6>
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
                                            <a href="{{ route('employee.patients.show', $payment->patient->id) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>View Patient Details
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Appointment Information -->
                                    @if($payment->appointment)
                                    <div class="card mb-4">
                                        <div class="card-header bg-warning text-white">
                                            <h6 class="card-title mb-0">
                                                <i class="fas fa-calendar-check me-2"></i>Related Appointment
                                            </h6>
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
                                            {{-- <a href="{{ route('employee.patients.appointments', [$payment->patient->id, 'appointment' => $payment->appointment->id]) }}" class="btn btn-outline-warning btn-sm">
                                                <i class="fas fa-eye me-1"></i>View Appointment
                                            </a> --}}
                                        </div>
                                    </div>
                                    @endif

                                </div>
                            </div>

                            <!-- Back Button -->
                            <div class="mt-4">
                                <a href="{{ route('employee.payments') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Back to Payments
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
