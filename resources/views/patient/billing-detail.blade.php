@extends('patient.layouts.app')

@section('title', 'Billing Details - FRYDT Patient Portal')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Billing Details
                    </h5>
                    <div>
                        @if($billing->balance_due > 0)
                            <a href="{{ route('patient.appointments') }}" class="btn btn-success me-2">
                                <i class="fas fa-credit-card me-1"></i> Make Payment
                            </a>
                        @endif
                        <a href="{{ route('patient.billing-print', $billing->id) }}" class="btn btn-outline-primary me-2" target="_blank">
                            <i class="fas fa-print me-1"></i> Print Receipt
                        </a>
                        <a href="{{ route('patient.billing') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Billing
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Invoice Header -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Invoice Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <td class="fw-bold">Invoice Number:</td>
                                            <td><strong>{{ $billing->invoice_number }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Invoice Date:</td>
                                            <td>{{ $billing->invoice_date->format('M j, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Due Date:</td>
                                            <td>
                                                {{ $billing->due_date->format('M j, Y') }}
                                                @if($billing->isOverdue())
                                                    <span class="badge bg-danger ms-2">Overdue</span>
                                                @elseif($billing->due_date->isPast() === false && $billing->due_date->diffInDays() <= 7)
                                                    <span class="badge bg-warning text-dark ms-2">Due Soon</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Status:</td>
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
                                            </td>
                                        </tr>
                                        @if($billing->payment_method)
                                        <tr>
                                            <td class="fw-bold">Payment Method:</td>
                                            <td>{{ ucfirst($billing->payment_method) }}</td>
                                        </tr>
                                        @endif
                                        @if($billing->payment_reference)
                                        <tr>
                                            <td class="fw-bold">Reference:</td>
                                            <td><small>{{ $billing->payment_reference }}</small></td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Patient Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <td class="fw-bold">Patient Name:</td>
                                            <td>{{ $billing->patient->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Patient ID:</td>
                                            <td>{{ $billing->patient->id }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Service Period:</td>
                                            <td>
                                                {{ $billing->service_start_date?->format('M j, Y') ?? 'N/A' }} -
                                                {{ $billing->service_end_date?->format('M j, Y') ?? 'N/A' }}
                                            </td>
                                        </tr>
                                        @if($billing->philhealth_member)
                                        <tr>
                                            <td class="fw-bold">PhilHealth:</td>
                                            <td>
                                                <span class="badge bg-info"><i class="fas fa-shield-alt me-1"></i>Covered</span>
                                                @if($billing->philhealth_number)
                                                    <br><small>Number: {{ $billing->philhealth_number }}</small>
                                                @endif
                                                @if($billing->philhealth_coverage > 0)
                                                    <br><small class="text-success">Coverage Applied: ₱{{ number_format($billing->philhealth_coverage, 2) }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td class="fw-bold">PhilHealth:</td>
                                            <td>
                                                <span class="badge bg-secondary"><i class="fas fa-times me-1"></i>Not Covered</span>
                                                <br><small class="text-muted">Patient is not a confirmed PhilHealth member</small>
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Summary -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3"><i class="fas fa-calculator me-2"></i>Billing Summary</h6>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-2">
                                            <div class="border-end">
                                                <h4 class="text-primary mb-1">₱{{ number_format($billing->subtotal_amount, 2) }}</h4>
                                                <p class="text-muted mb-0">Subtotal</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="border-end">
                                                <h4 class="text-success mb-1">-₱{{ number_format($billing->discount_amount, 2) }}</h4>
                                                <p class="text-muted mb-0">Discount</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="border-end">
                                                @if($billing->philhealth_coverage > 0)
                                                    <h4 class="text-info mb-1">-₱{{ number_format($billing->philhealth_coverage, 2) }}</h4>
                                                    <p class="text-muted mb-0">PhilHealth</p>
                                                    <small class="text-info">45% coverage applied</small>
                                                @else
                                                    <h4 class="text-muted mb-1">₱0.00</h4>
                                                    <p class="text-muted mb-0">PhilHealth</p>
                                                    <small class="text-muted">Not eligible</small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="border-end">
                                                <h4 class="text-warning mb-1">+₱{{ number_format($billing->tax_amount, 2) }}</h4>
                                                <p class="text-muted mb-0">Tax</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="border-end">
                                                <h4 class="text-primary mb-1">₱{{ number_format($billing->total_amount, 2) }}</h4>
                                                <p class="text-muted mb-0">Total Due</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <h4 class="text-{{ $billing->balance_due > 0 ? 'danger' : 'success' }} mb-1">
                                                ₱{{ number_format($billing->balance_due, 2) }}
                                            </h4>
                                            <p class="text-muted mb-0">Balance Due</p>
                                            @if($billing->balance_due <= 0)
                                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Paid</span>
                                            @else
                                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Outstanding</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between">
                                                <span><strong>Amount Paid:</strong></span>
                                                <span class="text-success fw-bold">₱{{ number_format($billing->amount_paid, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between">
                                                <span><strong>Outstanding Balance:</strong></span>
                                                <span class="{{ $billing->balance_due > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                                    ₱{{ number_format($billing->balance_due, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Services Rendered -->
                    @if($billing->services_rendered)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Services Rendered</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Service</th>
                                            <th>Date</th>
                                            <th>Provider</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($billing->services_rendered as $service)
                                        <tr>
                                            <td>{{ $service['name'] ?? 'N/A' }}</td>
                                            <td>{{ isset($service['date']) ? \Carbon\Carbon::parse($service['date'])->format('M j, Y') : 'N/A' }}</td>
                                            <td>{{ $service['provider'] ?? 'N/A' }}</td>
                                            <td>₱{{ number_format($service['amount'] ?? 0, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Insurance Information -->
                    @if($billing->has_insurance)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Insurance Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">Insurance Provider:</td>
                                    <td>{{ $billing->insurance_provider ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Policy Number:</td>
                                    <td>{{ $billing->insurance_policy_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Coverage Amount:</td>
                                    <td>₱{{ number_format($billing->insurance_coverage_amount ?? 0, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- PhilHealth Information -->
                    @if($billing->philhealth_member)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">PhilHealth Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">PhilHealth Number:</td>
                                    <td>{{ $billing->philhealth_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Benefit Amount:</td>
                                    <td>₱{{ number_format($billing->philhealth_benefit_amount ?? 0, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Payment Information -->
                    @if($billing->amount_paid > 0)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Payment Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">Payment Method:</td>
                                    <td>{{ $billing->payment_method ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Payment Reference:</td>
                                    <td>{{ $billing->payment_reference ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Amount Paid:</td>
                                    <td>₱{{ number_format($billing->amount_paid, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Responsible Party -->
                    @if($billing->responsible_party_name)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Responsible Party</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">Name:</td>
                                    <td>{{ $billing->responsible_party_name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Relationship:</td>
                                    <td>{{ $billing->responsible_party_relationship ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Contact:</td>
                                    <td>{{ $billing->responsible_party_contact ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Notes -->
                    @if($billing->billing_notes || $billing->collection_notes)
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Additional Notes</h6>
                            @if($billing->billing_notes)
                                <div class="alert alert-info">
                                    <strong>Billing Notes:</strong> {{ $billing->billing_notes }}
                                </div>
                            @endif
                            @if($billing->collection_notes)
                                <div class="alert alert-warning">
                                    <strong>Collection Notes:</strong> {{ $billing->collection_notes }}
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
