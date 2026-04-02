@extends('employee.layouts.app')

@section('title', 'Create Payment - ' . $patient->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card employee-card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Create Payment
                    </h4>
                    <small class="text-white-50">Create a new payment for {{ $patient->name }}</small>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-credit-card me-2"></i>Payment Details
                                    </h6>
                                </div>
                                <div class="card-body">
                <!-- Patient Information -->
                <div class="mb-4 p-3 bg-light rounded">
                    <h6 class="mb-2"><i class="fas fa-user me-2"></i>Patient Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Name:</strong> {{ $patient->name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Email:</strong> {{ $patient->email }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <strong>Phone:</strong> {{ $patient->patientProfile->phone ?? 'N/A' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Address:</strong> {{ $patient->patientProfile->address ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('employee.patients.store-payment', $patient->id) }}">
                    @csrf

                    <!-- Appointment Selection (Optional) -->
                    <div class="mb-3">
                        <label for="appointment_id" class="form-label">Related Appointment (Optional)</label>
                        <select name="appointment_id" id="appointment_id" class="form-select">
                            <option value="">Select an appointment (optional)</option>
                            @foreach($appointments as $appointment)
                                <option value="{{ $appointment->id }}"
                                        data-service="{{ $appointment->service->name }}"
                                        data-price="{{ $appointment->service->price }}">
                                    {{ $appointment->service->name }} - {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, Y h:i A') }}
                                    (₱{{ number_format($appointment->service->price, 2) }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">If this payment is for a specific appointment, select it here.</div>
                    </div>

                    <!-- Services Selection -->
                    <div class="mb-3">
                        <label class="form-label">Services <span class="text-danger">*</span></label>
                        <div id="services-container">
                            <div class="service-item mb-2 p-3 border rounded">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <select name="services[]" class="form-select service-select" required>
                                            <option value="">Select a service</option>
                                            @foreach($services as $service)
                                                <option value="{{ $service->id }}"
                                                        data-price="{{ $service->price }}"
                                                        data-name="{{ $service->name }}">
                                                    {{ $service->name }} - ₱{{ number_format($service->price, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="quantities[]" class="form-control quantity-input"
                                               placeholder="Qty" value="1" min="1" required readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="service-price fw-bold">₱0.00</span>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-service"
                                                style="display: none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-service" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add Another Service
                        </button>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="">Select payment method</option>
                            <option value="cash">Cash</option>
                            <option value="paypal">PayPal</option>
                            <option value="gcash">GCash</option>
                        </select>
                    </div>

                    <!-- Total Amount Display -->
                    <div class="mb-3">
                        <label class="form-label">Total Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="text" class="form-control fw-bold" id="total-amount" value="0.00" readonly>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3"
                                  placeholder="Any additional notes about this payment..."></textarea>
                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>Create Payment
                                        </button>
                                        <a href="{{ route('employee.patients.show', $patient->id) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i>Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const servicesContainer = document.getElementById('services-container');
    const addServiceBtn = document.getElementById('add-service');
    const appointmentSelect = document.getElementById('appointment_id');
    let serviceIndex = 1;

    // Service template
    const serviceTemplate = `
        <div class="service-item mb-2 p-3 border rounded">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <select name="services[]" class="form-select service-select" required>
                        <option value="">Select a service</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}"
                                    data-price="{{ $service->price }}"
                                    data-name="{{ $service->name }}">
                                {{ $service->name }} - ₱{{ number_format($service->price, 2) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="quantities[]" class="form-control quantity-input"
                           placeholder="Qty" value="1" min="1" required readonly>
                </div>
                <div class="col-md-2">
                    <span class="service-price fw-bold">₱0.00</span>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-service">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;

    // Add service button
    addServiceBtn.addEventListener('click', function() {
        servicesContainer.insertAdjacentHTML('beforeend', serviceTemplate);
        serviceIndex++;
        updateTotalAmount();
    });

    // Remove service
    servicesContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-service') || e.target.closest('.remove-service')) {
            e.target.closest('.service-item').remove();
            updateTotalAmount();
        }
    });

    // Service selection change
    servicesContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('service-select')) {
            updateServicePrice(e.target);
            updateTotalAmount();
        }
    });

    // Appointment selection change
    appointmentSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const serviceName = selectedOption.getAttribute('data-service');
            const servicePrice = selectedOption.getAttribute('data-price');

            // Clear existing services and add the appointment service
            servicesContainer.innerHTML = serviceTemplate;

            // Set the first service select to the appointment service
            const firstServiceSelect = servicesContainer.querySelector('.service-select');
            const optionToSelect = firstServiceSelect.querySelector(`option[value="${selectedOption.value}"]`);
            if (optionToSelect) {
                optionToSelect.selected = true;
                updateServicePrice(firstServiceSelect);
            }

            updateTotalAmount();
        }
    });

    // Update service price display
    function updateServicePrice(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const price = selectedOption.getAttribute('data-price') || 0;
        const priceElement = selectElement.closest('.service-item').querySelector('.service-price');
        priceElement.textContent = '₱' + parseFloat(price).toFixed(2);
    }

    // Update total amount
    function updateTotalAmount() {
        let total = 0;
        const priceElements = servicesContainer.querySelectorAll('.service-price');

        priceElements.forEach(function(priceElement) {
            const priceText = priceElement.textContent.replace('₱', '');
            total += parseFloat(priceText) || 0;
        });

        document.getElementById('total-amount').value = total.toFixed(2);
    }

    // Initialize on page load
    updateTotalAmount();
});
</script>
@endsection
