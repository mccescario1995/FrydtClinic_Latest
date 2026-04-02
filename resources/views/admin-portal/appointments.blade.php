@extends('admin-portal.layouts.app')

@section('title', 'Appointment Management')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"><i class="fas fa-calendar-check me-2"></i>Appointment Management</h1>
            <p class="page-subtitle">Monitor and manage all system appointments</p>
        </div>
        <div>
            <span class="badge bg-primary fs-6">{{ $appointments->total() }} Total Appointments</span>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-section">
    <form id="filterForm" method="GET" action="{{ route('admin-portal.appointments') }}" class="row g-3">
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="date_from" class="form-label">Date From</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">Date To</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <label for="search" class="form-label">Search Patient</label>
                <input type="text" name="search" id="search" class="form-control"
                        placeholder="Patient name" value="{{ request('search') }}">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('admin-portal.appointments') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Appointments Table -->
<div class="content-section">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-list me-2"></i>Appointments</h2>
        <p class="section-subtitle">Complete list of appointments with status and management options</p>
    </div>

    <div class="admin-card">
        <div class="card-body">
        @if($appointments->count() > 0)
            <div class="table-responsive">
                <table class="table admin-table table-hover">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Service</th>
                            <th>Provider</th>
                            <th>Date & Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $appointment)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                             style="width: 35px; height: 35px; font-weight: bold;">
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
                                    <div class="d-flex align-items-center">
                                        @if($appointment->employee)
                                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                 style="width: 30px; height: 30px; font-weight: bold; font-size: 12px;">
                                                {{ substr($appointment->employee->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $appointment->employee->name }}</div>
                                                <small class="text-muted">{{ $appointment->employee->email }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
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
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin-portal.appointments.show', $appointment->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($appointment->status !== 'completed' && $appointment->status !== 'cancelled')
                                            <button class="btn btn-sm btn-outline-success"
                                                    title="Mark as Completed"
                                                    onclick="updateAppointmentStatus({{ $appointment->id }}, 'completed')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger"
                                                    title="Cancel Appointment"
                                                    onclick="updateAppointmentStatus({{ $appointment->id }}, 'cancelled')">
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
                {{ $appointments->appends(request()->query())->links('vendor.pagination.admin-portal') }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h5 class="empty-title">No Appointments Found</h5>
                <p class="empty-text">No appointments match your current filter criteria.</p>
                <a href="{{ route('admin-portal.appointments') }}" class="btn btn-admin-primary">
                    <i class="fas fa-times me-1"></i>Clear Filters
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Status Update Modal -->
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
function updateAppointmentStatus(appointmentId, status) {
    const statusText = status.charAt(0).toUpperCase() + status.slice(1);

    if (confirm(`Are you sure you want to ${status} this appointment?`)) {
        // Show loading state
        const button = event.target.closest('button');
        const originalHtml = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;

        // Make AJAX call to update status
        fetch(`/admin-portal/appointments/${appointmentId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showAlert('success', `Appointment status updated to ${statusText} successfully!`);

                // Reload the page to reflect changes
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showAlert('danger', 'Failed to update appointment status.');
                button.innerHTML = originalHtml;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred while updating the appointment status.');
            button.innerHTML = originalHtml;
            button.disabled = false;
        });
    }
}

function showAlert(type, message) {
    const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', alertHtml);

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>
@endsection
