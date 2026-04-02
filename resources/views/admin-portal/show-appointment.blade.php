@extends('admin-portal.layouts.app')

@section('title', 'Appointment Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0"><i class="fas fa-calendar-check me-2"></i>Appointment Details</h1>
                <p class="text-muted mb-0">Complete appointment information and management</p>
            </div>
            <div>
                <a href="{{ route('admin-portal.appointments') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Appointments
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Appointment Status Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="admin-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-1">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>
                            {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('l, M d, Y') }}
                        </h4>
                        <p class="mb-2">
                            <i class="fas fa-clock me-1"></i>
                            {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('h:i A') }}
                            ({{ $appointment->duration_in_minutes ?? 0 }} minutes)
                        </p>
                        <div class="d-flex align-items-center gap-3">
                            <span><i class="fas fa-user me-1"></i>{{ $appointment->patient->name ?? 'Unknown Patient' }}</span>
                            <span><i class="fas fa-user-md me-1"></i>{{ $appointment->employee->name ?? 'Unassigned' }}</span>
                            <span><i class="fas fa-stethoscope me-1"></i>{{ $appointment->service->name ?? 'No Service' }}</span>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        @php
                            $statusClass = match($appointment->status) {
                                'scheduled' => 'warning',
                                'confirmed' => 'info',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'secondary'
                            };
                            $statusIcon = match($appointment->status) {
                                'scheduled' => 'clock',
                                'confirmed' => 'check-circle',
                                'completed' => 'check-double',
                                'cancelled' => 'times-circle',
                                default => 'question-circle'
                            };
                        @endphp
                        <span class="badge bg-{{ $statusClass }} fs-6 px-3 py-2">
                            <i class="fas fa-{{ $statusIcon }} me-1"></i>
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Appointment Details -->
    <div class="col-lg-8 mb-4">
        <div class="admin-card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Appointment Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Appointment ID</label>
                            <p class="mb-0">#{{ $appointment->id }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Date & Time</label>
                            <p class="mb-0">
                                {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, Y h:i A') }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Duration</label>
                            <p class="mb-0">{{ $appointment->duration_in_minutes ?? 0 }} minutes</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $statusClass }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Service</label>
                            <p class="mb-0">
                                @if($appointment->service)
                                    {{ $appointment->service->name }}
                                    <br><small class="text-muted">{{ $appointment->service->service_type ?? 'N/A' }}</small>
                                @else
                                    <span class="text-muted">No service assigned</span>
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Created</label>
                            <p class="mb-0">{{ $appointment->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Last Updated</label>
                            <p class="mb-0">{{ $appointment->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                @if($appointment->notes)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Notes</label>
                        <p class="mb-0">{{ $appointment->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Payment Details -->
        <div class="admin-card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Details</h5>
            </div>
            <div class="card-body">
                @if($appointment->payments && $appointment->payments->count() > 0)
                    @foreach($appointment->payments as $payment)
                        <div class="border rounded p-3 mb-3 {{ !$loop->last ? 'mb-3' : '' }}">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="mb-0 me-3">Payment #{{ $payment->payment_reference }}</h6>
                                        @php
                                            $paymentStatusClass = match($payment->status) {
                                                'completed' => 'success',
                                                'successful' => 'success',
                                                'pending' => 'warning',
                                                'awaiting_approval' => 'info',
                                                'failed' => 'danger',
                                                'cancelled' => 'secondary',
                                                default => 'secondary'
                                            };
                                            $paymentStatusIcon = match($payment->status) {
                                                'completed' => 'check-circle',
                                                'successful' => 'check-circle',
                                                'pending' => 'clock',
                                                'awaiting_approval' => 'hourglass-half',
                                                'failed' => 'times-circle',
                                                'cancelled' => 'ban',
                                                default => 'question-circle'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $paymentStatusClass }}">
                                            <i class="fas fa-{{ $paymentStatusIcon }} me-1"></i>
                                            {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                                        </span>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <small class="text-muted d-block">Amount</small>
                                            <strong>{{ $payment->formatted_amount }}</strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <small class="text-muted d-block">Method</small>
                                            <strong>
                                                @if($payment->payment_method === 'gcash')
                                                    <i class="fab fa-google-pay text-success me-1"></i>GCash
                                                @elseif($payment->payment_method === 'paypal')
                                                    <i class="fab fa-paypal text-primary me-1"></i>PayPal
                                                @elseif($payment->payment_method === 'cash')
                                                    <i class="fas fa-money-bill-wave text-success me-1"></i>Cash
                                                @else
                                                    {{ ucfirst($payment->payment_method) }}
                                                @endif
                                            </strong>
                                        </div>
                                    </div>
                                    @if($payment->paid_amount > 0)
                                        <div class="row mt-2">
                                            <div class="col-sm-6">
                                                <small class="text-muted d-block">Paid Amount</small>
                                                <strong class="text-success">{{ $payment->formatted_paid_amount }}</strong>
                                            </div>
                                            @if($payment->remaining_balance > 0)
                                                <div class="col-sm-6">
                                                    <small class="text-muted d-block">Remaining Balance</small>
                                                    <strong class="text-warning">{{ $payment->formatted_remaining_balance }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    @if($payment->gcash_reference)
                                        <div class="mt-2">
                                            <small class="text-muted d-block">GCash Reference</small>
                                            <code class="bg-light px-2 py-1 rounded">{{ $payment->gcash_reference }}</code>
                                        </div>
                                    @endif
                                    @if($payment->paid_at)
                                        <div class="mt-2">
                                            <small class="text-muted d-block">Payment Date</small>
                                            <span>{{ $payment->paid_at->format('M d, Y H:i') }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="btn-group-vertical" role="group">
                                        <a href="{{ route('admin-portal.payments.show', $payment->id) }}"
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                        @if($payment->status === 'awaiting_approval')
                                            <form method="POST" action="{{ route('admin-portal.payments.approve', $payment->id) }}"
                                                  class="d-inline" onsubmit="return confirm('Are you sure you want to approve this payment?')">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success btn-sm">
                                                    <i class="fas fa-check me-1"></i>Approve
                                                </button>
                                            </form>
                                            <button class="btn btn-outline-danger btn-sm"
                                                    onclick="rejectPayment({{ $payment->id }})">
                                                <i class="fas fa-times me-1"></i>Reject
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No Payment Records</h6>
                        <p class="text-muted mb-0">No payments have been made for this appointment yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Status Update -->
        @if($appointment->appointment_datetime > now() && $appointment->status !== 'cancelled')
            <div class="admin-card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Update Status</h5>
                </div>
                <div class="card-body">
                    <form id="statusUpdateForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <label for="status" class="form-label fw-bold">New Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="scheduled" {{ $appointment->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="confirmed" {{ $appointment->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">&nbsp;</label>
                                <button type="submit" class="btn btn-warning w-100" id="updateStatusBtn">
                                    <i class="fas fa-save me-1"></i>Update Status
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <!-- Patient & Employee Information -->
    <div class="col-lg-4">
        <!-- Patient Information -->
        <div class="admin-card mb-4">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="fas fa-user-injured me-2"></i>Patient Information</h6>
            </div>
            <div class="card-body text-center">
                @if($appointment->patient)
                    <div class="mb-3">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                             style="width: 60px; height: 60px; font-weight: bold; font-size: 20px;">
                            {{ substr($appointment->patient->name, 0, 1) }}
                        </div>
                    </div>
                    <h6 class="mb-1">{{ $appointment->patient->name }}</h6>
                    <p class="text-muted mb-2">{{ $appointment->patient->email }}</p>
                    <a href="{{ route('admin-portal.users.show', $appointment->patient->id) }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-eye me-1"></i>View Patient
                    </a>
                @else
                    <div class="py-3">
                        <i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Patient information not available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Employee Information -->
        <div class="admin-card mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-user-md me-2"></i>Healthcare Provider</h6>
            </div>
            <div class="card-body text-center">
                @if($appointment->employee)
                    <div class="mb-3">
                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                             style="width: 60px; height: 60px; font-weight: bold; font-size: 20px;">
                            {{ substr($appointment->employee->name, 0, 1) }}
                        </div>
                    </div>
                    <h6 class="mb-1">{{ $appointment->employee->name }}</h6>
                    <p class="text-muted mb-2">{{ $appointment->employee->email }}</p>
                    @if($appointment->employee->employeeProfile)
                        <small class="text-muted d-block mb-2">{{ $appointment->employee->employeeProfile->position ?? 'Staff' }}</small>
                    @endif
                    <a href="{{ route('admin-portal.users.show', $appointment->employee->id) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-eye me-1"></i>View Provider
                    </a>
                @else
                    <div class="py-3">
                        <i class="fas fa-user-md fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No provider assigned</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="admin-card">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($appointment->patient)
                        <a href="{{ route('admin-portal.patients.show', $appointment->patient->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-file-medical me-1"></i>View Medical Records
                        </a>
                        <a href="{{ route('admin-portal.patients.show', $appointment->patient->id) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-calendar me-1"></i>All Patient Appointments
                        </a>
                    @endif
                    <button class="btn btn-outline-info btn-sm" onclick="printAppointment()">
                        <i class="fas fa-print me-1"></i>Print Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('statusUpdateForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const status = formData.get('status');
    const submitBtn = document.getElementById('updateStatusBtn');

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';

    fetch(`{{ route('admin-portal.appointments.update-status', $appointment->id) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showAlert('Status updated successfully!', 'success');

            // Update the status badge
            const statusBadge = document.querySelector('.badge');
            const newStatus = status.charAt(0).toUpperCase() + status.slice(1);
            statusBadge.textContent = newStatus;

            // Update badge color
            const colorMap = {
                'scheduled': 'warning',
                'confirmed': 'info',
                'completed': 'success',
                'cancelled': 'danger'
            };
            statusBadge.className = `badge bg-${colorMap[status]} fs-6 px-3 py-2`;

            // Reload page after short delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('Failed to update status. Please try again.', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred. Please try again.', 'danger');
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Update Status';
    });
});

function showAlert(message, type) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());

    // Create new alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alert);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

function printAppointment() {
    window.print();
}

function rejectPayment(paymentId) {
    const reason = prompt('Please provide a reason for rejecting this payment:');
    if (reason && reason.trim()) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin-portal/payments/${paymentId}/reject`;
        form.style.display = 'none';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrfToken);

        const notesField = document.createElement('input');
        notesField.type = 'hidden';
        notesField.name = 'notes';
        notesField.value = reason.trim();
        form.appendChild(notesField);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<style>
@media print {
    .btn, .card-header .btn, .alert {
        display: none !important;
    }
    .admin-card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
}
</style>
@endsection
