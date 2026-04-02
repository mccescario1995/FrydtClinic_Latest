@extends('patient.layouts.app')

@section('title', 'My Appointments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-calendar-check me-2"></i>My Appointments</h2>
    <a href="{{ route('patient.book-appointment') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Book New Appointment
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($appointments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Service</th>
                            <th>Doctor/Staff</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $appointment)
                        <tr>
                            <td>
                                <strong>{{ $appointment->appointment_datetime->format('M j, Y') }}</strong><br>
                                <small class="text-muted">{{ $appointment->appointment_datetime->format('g:i A') }}</small>
                            </td>
                            <td>{{ $appointment->service->name }}</td>
                            <td>{{ $appointment->employee->name }}</td>
                            <td>{{ $appointment->duration_in_minutes }} mins</td>
                            <td>
                                <span class="badge bg-{{ $appointment->status === 'completed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'info') }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td>
                                @if($appointment->patient_notes)
                                    <small>{{ Str::limit($appointment->patient_notes, 50) }}</small>
                                @else
                                    <small class="text-muted">No notes</small>
                                @endif
                            </td>
                            <td>
                                @if($appointment->status === 'scheduled')
                                    @php
                                        $hasPayment = $appointment->payments()->where('status', 'completed')->exists();
                                    @endphp
                                    <div class="btn-group" role="group">
                                        @if(!$hasPayment)
                                            <a href="{{ route('patient.payments.create', ['appointment_id' => $appointment->id]) }}"
                                               class="btn btn-sm btn-success">
                                                <i class="fas fa-credit-card me-1"></i>Pay
                                            </a>
                                        @else
                                            <span class="badge bg-success me-1">
                                                <i class="fas fa-check me-1"></i>Paid
                                            </span>
                                        @endif
                                        <button class="btn btn-sm btn-outline-primary"
                                                onclick="editAppointment({{ $appointment->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger"
                                                onclick="cancelAppointment({{ $appointment->id }}, '{{ $appointment->appointment_datetime->format('M j, Y g:i A') }}')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @elseif($appointment->status === 'completed')
                                    <span class="badge bg-info">
                                        <i class="fas fa-check-circle me-1"></i>Completed
                                    </span>
                                @elseif($appointment->status === 'cancelled')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i>Cancelled
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $appointments->links() }}
        @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No appointments found</h5>
                <a href="{{ route('patient.book-appointment') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus me-1"></i>Book Your First Appointment
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function editAppointment(appointmentId) {
    // Redirect to edit appointment page
    window.location.href = `/patient/appointments/${appointmentId}/edit`;
}

function cancelAppointment(appointmentId, appointmentDateTime) {
    if (confirm(`Are you sure you want to cancel your appointment on ${appointmentDateTime}? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/patient/appointments/${appointmentId}/cancel`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
