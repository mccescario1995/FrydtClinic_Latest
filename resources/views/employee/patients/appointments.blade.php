@extends('employee.layouts.app')

@section('title', 'Patient Appointments - ' . $patient->name)

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="welcome-section mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2"><i class="fas fa-calendar-check me-2"></i>{{ $patient->name }}'s Appointments</h1>
                <p class="mb-0">Manage and track all appointments for this patient</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('employee.patients.show', $patient->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Patient
                    </a>
                    <a href="{{ route('employee.patients') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-1"></i>All Patients
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="employee-card p-3 text-center">
                <div class="text-primary mb-2">
                    <i class="fas fa-calendar fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $totalAppointments }}</h4>
                <p class="text-muted mb-0">Total Appointments</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="employee-card p-3 text-center">
                <div class="text-info mb-2">
                    <i class="fas fa-clock fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $upcomingCount }}</h4>
                <p class="text-muted mb-0">Upcoming</p>
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
                <div class="text-warning mb-2">
                    <i class="fas fa-calendar-check fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $scheduledCount }}</h4>
                <p class="text-muted mb-0">Scheduled</p>
            </div>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('employee.patients.appointments', $patient->id) }}?filter=all"
                   class="btn {{ $filter == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-list me-1"></i>All Appointments ({{ $totalAppointments }})
                </a>
                <a href="{{ route('employee.patients.appointments', $patient->id) }}?filter=upcoming"
                   class="btn {{ $filter == 'upcoming' ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="fas fa-clock me-1"></i>Upcoming ({{ $upcomingCount }})
                </a>
                <a href="{{ route('employee.patients.appointments', $patient->id) }}?filter=past"
                   class="btn {{ $filter == 'past' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                    <i class="fas fa-history me-1"></i>Past ({{ $totalAppointments - $upcomingCount }})
                </a>
            </div>
        </div>
    </div>

    <!-- Appointments List -->
    <div class="employee-card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-calendar-alt me-2"></i>
                @switch($filter)
                    @case('upcoming')
                        Upcoming Appointments
                        @break
                    @case('past')
                        Past Appointments
                        @break
                    @default
                        All Appointments
                @endswitch
            </h5>
        </div>
        <div class="card-body">
            @if($appointments->count() > 0)
                <div class="table-responsive">
                    <table class="table employee-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Service</th>
                                <th>Employee</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                                <tr>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('h:i A') }}</small>
                                            @if($appointment->appointment_datetime > now())
                                                <br><small class="text-success"><i class="fas fa-clock me-1"></i>Upcoming</small>
                                            @else
                                                <br><small class="text-muted"><i class="fas fa-check me-1"></i>Past</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            @if($appointment->service)
                                                <div class="fw-bold">{{ $appointment->service->name }}</div>
                                                <small class="text-muted">{{ $appointment->service->service_type ?? 'N/A' }}</small>
                                            @else
                                                <span class="text-muted">No service assigned</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($appointment->employee)
                                                <div class="avatar-circle me-2 bg-primary text-white">
                                                    {{ substr($appointment->employee->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $appointment->employee->name }}</div>
                                                    <small class="text-muted">{{ $appointment->employee->email }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">No employee assigned</span>
                                            @endif
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
                                            <button class="btn btn-primary me-1"
                                                    title="View Details"
                                                    onclick="viewAppointmentDetails({{ $appointment->id }})">
                                                <i class="fas fa-eye me-1"></i>View
                                            </button>
                                            @if($appointment->appointment_datetime > now() && $appointment->status !== 'cancelled')
                                                <button class="btn btn-success me-1"
                                                        title="Mark as Completed"
                                                        onclick="updateAppointmentStatus({{ $appointment->id }}, 'completed')">
                                                    <i class="fas fa-check me-1"></i>Complete
                                                </button>
                                                <button class="btn btn-danger"
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
                    {{ $appointments->appends(['filter' => $filter])->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No Appointments Found</h4>
                    <p class="text-muted">
                        @switch($filter)
                            @case('upcoming')
                                This patient has no upcoming appointments.
                                @break
                            @case('past')
                                This patient has no past appointments.
                                @break
                            @default
                                This patient has no appointments scheduled.
                        @endswitch
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Appointment Details Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appointment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="appointmentDetails">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewAppointmentDetails(appointmentId) {
    // Show loading in modal
    document.getElementById('appointmentDetails').innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p>Loading appointment details...</p></div>';
    document.getElementById('appointmentModal').querySelector('.modal-title').textContent = 'Appointment Details';

    // Show modal
    var modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
    modal.show();

    // Fetch appointment details via AJAX
    fetch(`/employee/patients/{{ $patient->id }}/appointments/${appointmentId}/details`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const appointment = data.appointment;
            const detailsHtml = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="fas fa-calendar me-2"></i>Appointment Information</h6>
                        <p><strong>Date & Time:</strong> ${appointment.appointment_datetime}</p>
                        <p><strong>Duration:</strong> ${appointment.duration_in_minutes} minutes</p>
                        <p><strong>Status:</strong> <span class="badge bg-${appointment.status.toLowerCase() === 'completed' ? 'success' : appointment.status.toLowerCase() === 'cancelled' ? 'danger' : 'primary'}">${appointment.status}</span></p>
                        <p><strong>Notes:</strong> ${appointment.notes}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success"><i class="fas fa-user-md me-2"></i>Service & Provider</h6>
                        ${appointment.service ? `
                            <p><strong>Service:</strong> ${appointment.service.name}</p>
                            <p><strong>Type:</strong> ${appointment.service.type}</p>
                            <p><strong>Description:</strong> ${appointment.service.description}</p>
                        ` : '<p class="text-muted">No service assigned</p>'}
                        <hr>
                        ${appointment.employee ? `
                            <p><strong>Provider:</strong> ${appointment.employee.name}</p>
                            <p><strong>Email:</strong> ${appointment.employee.email}</p>
                            <p><strong>Phone:</strong> ${appointment.employee.phone}</p>
                        ` : '<p class="text-muted">No provider assigned</p>'}
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-info"><i class="fas fa-user me-2"></i>Patient Information</h6>
                        <p><strong>Name:</strong> ${appointment.patient.name}</p>
                        <p><strong>Email:</strong> ${appointment.patient.email}</p>
                        <p><strong>Phone:</strong> ${appointment.patient.phone}</p>
                        <p><strong>Birth Date:</strong> ${appointment.patient.birth_date}</p>
                    </div>
                </div>
            `;
            document.getElementById('appointmentDetails').innerHTML = detailsHtml;
        } else {
            document.getElementById('appointmentDetails').innerHTML = '<div class="alert alert-danger">Failed to load appointment details.</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('appointmentDetails').innerHTML = '<div class="alert alert-danger">An error occurred while loading appointment details.</div>';
    });
}

function updateAppointmentStatus(appointmentId, status) {
    const statusText = status.charAt(0).toUpperCase() + status.slice(1);

    if (confirm(`Are you sure you want to ${status} this appointment?`)) {
        // Show loading state
        const button = event.target.closest('button');
        const originalHtml = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
        button.disabled = true;

        // Make AJAX call to update status
        fetch(`/employee/patients/{{ $patient->id }}/appointments/${appointmentId}/status`, {
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

// Avatar circle styles
document.head.insertAdjacentHTML('beforeend', `
    <style>
        .avatar-circle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
`);
</script>
@endsection
