@extends('employee.layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="welcome-section mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2"><i class="fas fa-calendar-check me-2"></i>My Appointments</h1>
                <p class="mb-0">Manage your scheduled appointments and patient sessions</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex justify-content-end gap-2">
                    <div class="text-white">
                        <h4 class="mb-0">{{ $upcomingCount }}</h4>
                        <small>Upcoming</small>
                    </div>
                    <div class="text-white ms-4">
                        <h4 class="mb-0">{{ $completedCount }}</h4>
                        <small>Completed</small>
                    </div>
                    <div class="text-white ms-4">
                        <h4 class="mb-0">{{ $totalAppointments }}</h4>
                        <small>Total</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="employee-card p-3 text-center">
                <div class="text-primary mb-2">
                    <i class="fas fa-calendar-plus fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $upcomingCount }}</h4>
                <p class="text-muted mb-0">Upcoming Appointments</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="employee-card p-3 text-center">
                <div class="text-success mb-2">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $completedCount }}</h4>
                <p class="text-muted mb-0">Completed</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="employee-card p-3 text-center">
                <div class="text-danger mb-2">
                    <i class="fas fa-times-circle fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $cancelledCount }}</h4>
                <p class="text-muted mb-0">Cancelled</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="employee-card p-3 text-center">
                <div class="text-info mb-2">
                    <i class="fas fa-calendar fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $totalAppointments }}</h4>
                <p class="text-muted mb-0">Total Appointments</p>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="employee-card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Appointments</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('employee.appointments') }}" id="filterForm">
                <div class="row g-3">
                    <!-- Search Filter -->
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search Patient</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Patient name or email">
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <!-- Date Filter -->
                    <div class="col-md-2">
                        <label for="date_filter" class="form-label">Date Range</label>
                        <select class="form-select" id="date_filter" name="date_filter">
                            <option value="">All Dates</option>
                            <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="tomorrow" {{ request('date_filter') == 'tomorrow' ? 'selected' : '' }}>Tomorrow</option>
                            <option value="this_week" {{ request('date_filter') == 'this_week' ? 'selected' : '' }}>This Week</option>
                            <option value="next_week" {{ request('date_filter') == 'next_week' ? 'selected' : '' }}>Next Week</option>
                            <option value="this_month" {{ request('date_filter') == 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="custom" {{ request('date_filter') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>

                    <!-- Payment Status Filter -->
                    <div class="col-md-2">
                        <label for="payment_status" class="form-label">Payment</label>
                        <select class="form-select" id="payment_status" name="payment_status">
                            <option value="">All Payments</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>

                    <!-- Service Type Filter -->
                    <div class="col-md-2">
                        <label for="service_type" class="form-label">Service Type</label>
                        <select class="form-select" id="service_type" name="service_type">
                            <option value="">All Services</option>
                            @foreach($serviceTypes as $serviceType)
                                <option value="{{ $serviceType }}" {{ request('service_type') == $serviceType ? 'selected' : '' }}>
                                    {{ ucfirst($serviceType) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort Filter -->
                    <div class="col-md-1">
                        <label for="sort_by" class="form-label">Sort By</label>
                        <select class="form-select" id="sort_by" name="sort_by">
                            <option value="appointment_datetime" {{ request('sort_by', 'appointment_datetime') == 'appointment_datetime' ? 'selected' : '' }}>Date</option>
                            <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Created</option>
                        </select>
                    </div>
                </div>

                <!-- Custom Date Range (shown when custom is selected) -->
                <div class="row g-3 mt-2 {{ request('date_filter') == 'custom' ? '' : 'd-none' }}" id="customDateRange">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                               value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                               value="{{ request('end_date') }}">
                    </div>
                </div>

                <!-- Filter Actions -->
                <div class="row g-3 mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i>Apply Filters
                        </button>
                        <a href="{{ route('employee.appointments') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Clear Filters
                        </a>
                        @if(request()->anyFilled(['search', 'status', 'date_filter', 'payment_status', 'service_type']))
                            <span class="badge bg-info ms-2">
                                <i class="fas fa-filter me-1"></i>Filtered
                            </span>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Appointments List -->
    <div class="employee-card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Appointments</h5>
            <span class="badge bg-light text-primary">{{ $appointments->total() }} results</span>
        </div>
        <div class="card-body">
            @if($appointments->count() > 0)
                <div class="table-responsive">
                    <table class="table employee-table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Service</th>
                                <th>Date & Time</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2 bg-primary text-white">
                                                {{ substr($appointment->patient->name ?? 'N/A', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $appointment->patient->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $appointment->patient->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $appointment->service->name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $appointment->service->service_type ?? '' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('h:i A') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $appointment->duration_in_minutes ?? 0 }} min</span>
                                    </td>
                                    <td>
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
                                        <span class="badge bg-{{ $statusClass }}">
                                            <i class="fas fa-{{ $statusIcon }} me-1"></i>
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($appointment->payments && $appointment->payments->count() > 0)
                                            @php
                                                $payment = $appointment->payments->first();
                                                $paymentStatusClass = match($payment->status) {
                                                    'completed' => 'success',
                                                    'pending' => 'warning',
                                                    'failed' => 'danger',
                                                    'cancelled' => 'secondary',
                                                    default => 'secondary'
                                                };
                                                $paymentMethodIcon = match($payment->payment_method) {
                                                    'gcash' => 'fa-mobile-alt',
                                                    'paypal' => 'fa-paypal',
                                                    'cash' => 'fa-money-bill-wave',
                                                    default => 'fa-credit-card'
                                                };
                                            @endphp
                                            <div class="d-flex flex-column align-items-start">
                                                <span class="badge bg-{{ $paymentStatusClass }} mb-1">
                                                    <i class="fas {{ $paymentMethodIcon }} me-1"></i>
                                                    {{ ucfirst($payment->payment_method) }}
                                                </span>
                                                <small class="text-muted">₱{{ number_format($payment->amount, 2) }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('employee.patients.show', $appointment->patient_id) }}"
                                               class="btn btn-outline-primary px-3 py-2"
                                               title="View Patient">
                                                <i class="fas fa-user me-1"></i>View Patient
                                            </a>
                                            @if($appointment->payments && $appointment->payments->count() > 0)
                                                @php
                                                    $payment = $appointment->payments->first();
                                                @endphp
                                                <a href="{{ route('employee.payments.show', $payment->id) }}"
                                                   class="btn btn-outline-info px-3 py-2"
                                                   title="View Payment Details">
                                                    <i class="fas fa-credit-card me-1"></i>View Payment
                                                </a>
                                            @endif
                                            @if($appointment->appointment_datetime > now() && $appointment->status !== 'cancelled' && $appointment->status !== "completed")
                                                @if($appointment->payments && $appointment->payments->count() > 0)
                                                    @php
                                                        $payment = $appointment->payments->first();
                                                    @endphp
                                                    <button class="btn btn-outline-success px-3
                                                    @if ($payment->paid_at != null)

                                                    d-block
                                                    @else

                                                    d-none

                                                    @endif
                                                    py-2"
                                                            title="Mark as Completed"
                                                            onclick="updateAppointmentStatus({{ $appointment->id }}, 'completed')">
                                                        <i class="fas fa-check me-1"></i>Complete
                                                    </button>
                                                @endif


                                                <button class="btn btn-outline-danger px-3 py-2"
                                                        title="Cancel Appointment"
                                                        onclick="updateAppointmentStatus({{ $appointment->id }}, 'cancelled')">
                                                    <i class="fas fa-times me-1"></i>Cancel
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
                <div class="d-flex justify-content-center mt-4">
                    {{ $appointments->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No Appointments Found</h4>
                    <p class="text-muted">You don't have any appointments scheduled yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Update Appointment Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Appointment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to update this appointment status?</p>
                <div id="appointmentDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStatusUpdate">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-submit form when filter changes (except search)
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const dateFilter = document.getElementById('date_filter');
    const customDateRange = document.getElementById('customDateRange');
    const searchInput = document.getElementById('search');

    // Show/hide custom date range based on selection
    function toggleCustomDateRange() {
        if (dateFilter.value === 'custom') {
            customDateRange.classList.remove('d-none');
        } else {
            customDateRange.classList.add('d-none');
            // Clear custom date values when hiding
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
        }
    }

    // Initial state
    toggleCustomDateRange();

    // Listen for date filter changes
    dateFilter.addEventListener('change', function() {
        toggleCustomDateRange();
        // Auto-submit for non-search filters
        filterForm.submit();
    });

    // Auto-submit form when other filters change
    const autoSubmitFilters = ['status', 'payment_status', 'service_type', 'sort_by'];
    autoSubmitFilters.forEach(function(filterId) {
        const element = document.getElementById(filterId);
        if (element) {
            element.addEventListener('change', function() {
                filterForm.submit();
            });
        }
    });

    // Search with debounce
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            filterForm.submit();
        }, 500); // 500ms delay
    });

    // Form validation for custom date range
    filterForm.addEventListener('submit', function(e) {
        if (dateFilter.value === 'custom') {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            if (!startDate || !endDate) {
                e.preventDefault();
                showToast('error', 'Please select both start and end dates for custom range');
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                e.preventDefault();
                showToast('error', 'Start date cannot be after end date');
                return;
            }
        }
    });

    // Add loading state to submit button
    const submitButton = filterForm.querySelector('button[type="submit"]');
    filterForm.addEventListener('submit', function() {
        if (submitButton) {
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Loading...';
            submitButton.disabled = true;
        }
    });
});

function updateAppointmentStatus(appointmentId, status) {
    // Show loading state
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';

    // Make AJAX call to update appointment status
    fetch(`/employee/appointments/${appointmentId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showToast('success', data.message);

            // Update the status badge in the table
            const row = button.closest('tr');
            const statusCell = row.querySelector('td:nth-child(5) .badge');

            // Update badge class and text
            let statusClass, statusIcon, statusText;
            switch(status) {
                case 'completed':
                    statusClass = 'success';
                    statusIcon = 'check-double';
                    statusText = 'Completed';
                    break;
                case 'cancelled':
                    statusClass = 'danger';
                    statusIcon = 'times-circle';
                    statusText = 'Cancelled';
                    break;
                default:
                    statusClass = 'secondary';
                    statusIcon = 'question-circle';
                    statusText = 'Unknown';
            }

            statusCell.className = `badge bg-${statusClass}`;
            statusCell.innerHTML = `<i class="fas fa-${statusIcon} me-1"></i>${statusText}`;

            // Hide the action buttons if appointment is now cancelled
            if (status === 'cancelled') {
                const actionButtons = row.querySelectorAll('td:last-child .btn-outline-success, td:last-child .btn-outline-danger');
                actionButtons.forEach(btn => {
                    if (!btn.classList.contains('btn-outline-primary')) {
                        btn.style.display = 'none';
                    }
                });
            }

            // Reload the page after a short delay to update counts
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast('error', 'Failed to update appointment status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'An error occurred while updating the appointment');
    })
    .finally(() => {
        // Restore button state
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

function showToast(type, message) {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    toastContainer.appendChild(toast);

    // Initialize and show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

// Additional styles for filter interface
document.head.insertAdjacentHTML('beforeend', `
    <style>
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
        }
        .toast-container {
            z-index: 9999;
        }
        .filter-section {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            color: #495057;
        }
        .badge {
            font-size: 0.75rem;
        }
        #customDateRange {
            transition: all 0.3s ease;
        }
    </style>
`);
</script>
@endsection
