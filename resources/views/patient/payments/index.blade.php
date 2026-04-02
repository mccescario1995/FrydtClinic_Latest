@extends('patient.layouts.app')

@section('title', 'Payments')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-receipt me-2"></i>My Payments</h2>
                    {{-- <a href="{{ route('patient.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-home me-2"></i>Back to Dashboard
                    </a> --}}
                </div>

                @if ($payments->count() > 0)
                    <div class="card shadow">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Reference</th>
                                            <th>Services</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($payments as $payment)
                                            <tr>
                                                <td>
                                                    <strong>{{ $payment->payment_reference }}</strong>
                                                    @if ($payment->appointment)
                                                        <br><small class="text-muted">
                                                            <i class="fas fa-calendar-check me-1"></i>
                                                            {{ $payment->appointment->appointment_datetime->format('M d, Y') }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @foreach ($payment->items->take(2) as $item)
                                                        <div class="small">{{ $item->service_name }}</div>
                                                    @endforeach
                                                    @if ($payment->items->count() > 2)
                                                        <small class="text-muted">+{{ $payment->items->count() - 2 }}
                                                            more</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong class="text-success">{{ $payment->formatted_amount }}</strong>
                                                </td>
                                                <td class="text-nowrap">
                                                    @if ($payment->payment_method === 'paypal')
                                                        <i class="fab fa-paypal text-primary"></i> PayPal
                                                    @elseif($payment->payment_method === 'cash')
                                                        <i class="fas fa-money-bill-wave text-success"></i> Cash
                                                    @elseif($payment->payment_method === 'gcash')
                                                        <img src="{{ asset("images/gcash-logo.png") }}" alt="GCash Icon" width="14"> GCash
                                                    @else
                                                        <i class="fas fa-credit-card text-info"></i> Card
                                                    @endif
                                                </td>

                                                <td>
                                                    <span
                                                        class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($payment->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $payment->created_at->format('M d, Y') }}
                                                    <br><small
                                                        class="text-muted">{{ $payment->created_at->format('g:i A') }}</small>
                                                </td>
                                                <td>
                                                    <a href="{{ route('patient.payments.show', $payment) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            {{ $payments->appends(request()->query())->links('vendor.pagination.admin-portal') }}
                        </div>
                    </div>
                @else
                    <div class="card shadow">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Payments Found</h5>
                            <p class="text-muted">You haven't made any payments yet.</p>
                            <a href="{{ route('patient.book-appointment') }}" class="btn btn-primary">
                                <i class="fas fa-calendar-plus me-2"></i>Book an Appointment
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection
