@extends('patient.layouts.app')

@section('title', 'Create Payments')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>Payment Details
                        </h4>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('patient.payments.store') }}" method="POST">
                            @csrf

                            @if ($appointment)
                                <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-calendar-check me-2"></i>Appointment Details</h6>
                                    <p class="mb-1"><strong>Date:</strong>
                                        {{ $appointment->appointment_datetime->format('F d, Y') }}</p>
                                    <p class="mb-1"><strong>Time:</strong>
                                        {{ $appointment->appointment_datetime->format('g:i A') }}</p>
                                    <p class="mb-0"><strong>Purpose:</strong> {{ $appointment->purpose }}</p>
                                </div>
                            @endif

                            @if ($prescription)
                                <input type="hidden" name="prescription_id" value="{{ $prescription->id }}">
                                <div class="alert alert-success">
                                    <h6><i class="fas fa-pills me-2"></i>Prescription Details</h6>
                                    <p class="mb-1"><strong>Prescription #:</strong> {{ $prescription->prescription_number }}</p>
                                    <p class="mb-1"><strong>Medicine:</strong> {{ $prescription->inventory->name ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Dispensed Date:</strong> {{ $prescription->dispensed_date ? $prescription->dispensed_date->format('F d, Y') : 'N/A' }}</p>
                                    <p class="mb-0"><strong>Quantity:</strong> {{ $prescription->quantity_dispensed }}</p>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="border-bottom pb-2">
                                        <i class="fas fa-list me-2"></i>Services
                                    </h6>

                                    @if ($prescription)
                                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                            <div>
                                                <strong>{{ $prescription->inventory->name ?? 'Prescription Medicine' }}</strong>
                                                <br><small class="text-muted">Prescription #{{ $prescription->prescription_number }}</small>
                                            </div>
                                            <span class="badge bg-success">₱{{ number_format($prescription->total_price, 2) }}</span>
                                        </div>
                                    @endif

                                    @foreach ($services as $service)
                                        <input type="hidden" name="services[]" value="{{ $service->id }}">
                                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                            <div>
                                                <strong>{{ $service->name }}</strong>
                                                @if ($service->description)
                                                    <br><small class="text-muted">{{ $service->description }}</small>
                                                @endif
                                            </div>
                                            <span class="badge bg-success">₱{{ number_format($service->base_price, 2) }}</span>
                                        </div>
                                    @endforeach

                                    <div
                                        class="d-flex justify-content-between align-items-center py-3 border-bottom bg-light rounded mt-3 px-3">
                                        <strong>Total Amount:</strong>
                                        <h5 class="text-success mb-0">₱{{ number_format($total, 2) }}</h5>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h6 class="border-bottom pb-2">
                                        <i class="fas fa-payment me-2"></i>Payment Method
                                    </h6>

                                    <div class="payment-methods">
                                        <div class="form-check mb-3 p-3 border rounded">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                id="paypal" value="paypal" checked>
                                            <label class="form-check-label d-flex align-items-center" for="paypal">
                                                <i class="fab fa-paypal text-primary fs-4 me-3"></i>
                                                <div>
                                                    <strong>PayPal</strong>
                                                    <br><small class="text-muted">Secure online payment</small>
                                                </div>
                                            </label>
                                        </div>

                                        <div class="form-check mb-3 p-3 border rounded">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                id="gcash" value="gcash">
                                            <label class="form-check-label d-flex align-items-center" for="gcash">
                                                <i class="fas fa-qrcode text-success fs-4 me-3"></i>
                                                <div>
                                                    <strong>GCash QR Code</strong>
                                                    <br><small class="text-muted">Scan QR code in GCash app</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-credit-card me-2"></i>Proceed to Payment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
