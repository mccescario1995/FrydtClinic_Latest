@extends('admin-portal.layouts.app')

@section('title', 'Service Details')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="fas fa-stethoscope me-2"></i>{{ $service->name }}
            </h1>
            <p class="page-subtitle">Service Code: {{ $service->code }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin-portal.services.edit', $service->id) }}" class="btn btn-admin-primary">
                <i class="fas fa-edit me-1"></i>Edit Service
            </a>
            <a href="{{ route('admin-portal.services') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Services
            </a>
        </div>
    </div>
</div>

<!-- Service Overview Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="admin-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-{{ $service->type === 'single' ? 'info' : 'warning' }} text-white mb-2">
                    <i class="fas fa-{{ $service->type === 'single' ? 'file-medical' : 'boxes' }}"></i>
                </div>
                <h4 class="stat-value">{{ ucfirst($service->type) }}</h4>
                <p class="stat-label text-muted">Service Type</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-success text-white mb-2">
                    <i class="fas fa-peso-sign"></i>
                </div>
                <h4 class="stat-value">{{ $service->display_price }}</h4>
                <p class="stat-label text-muted">Base Price</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-primary text-white mb-2">
                    <i class="fas fa-clock"></i>
                </div>
                <h4 class="stat-value">{{ $service->duration_minutes ?? 'N/A' }}</h4>
                <p class="stat-label text-muted">Duration (min)</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card">
            <div class="card-body text-center">
                <div class="stat-icon bg-{{ $service->status === 'active' ? 'success' : ($service->status === 'inactive' ? 'secondary' : 'danger') }} text-white mb-2">
                    <i class="fas fa-{{ $service->status === 'active' ? 'check-circle' : ($service->status === 'inactive' ? 'pause-circle' : 'times-circle') }}"></i>
                </div>
                <h4 class="stat-value">{{ ucfirst($service->status) }}</h4>
                <p class="stat-label text-muted">Status</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Service Details -->
    <div class="col-lg-8 mb-4">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Service Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Basic Details</h6>
                        <table class="table table-sm">
                            <tr>
                                <td class="fw-bold">Name:</td>
                                <td>{{ $service->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Code:</td>
                                <td>{{ $service->code }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Type:</td>
                                <td>
                                    <span class="badge bg-{{ $service->type === 'single' ? 'info' : 'warning' }}">
                                        {{ ucfirst($service->type) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Service Type:</td>
                                <td>{{ ucwords(str_replace('_', ' ', $service->service_type)) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Category:</td>
                                <td>{{ ucwords(str_replace('_', ' ', $service->category)) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Status:</td>
                                <td>
                                    <span class="badge bg-{{ $service->status === 'active' ? 'success' : ($service->status === 'inactive' ? 'secondary' : 'danger') }}">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Pricing & Duration</h6>
                        <table class="table table-sm">
                            <tr>
                                <td class="fw-bold">Base Price:</td>
                                <td>{{ $service->display_price }}</td>
                            </tr>
                            @if($service->philhealth_covered)
                                <tr>
                                    <td class="fw-bold">PhilHealth Price:</td>
                                    <td>₱{{ number_format($service->philhealth_price, 2) }}</td>
                                </tr>
                            @endif
                            @if($service->discount_percentage > 0)
                                <tr>
                                    <td class="fw-bold">Discount:</td>
                                    <td>{{ $service->discount_percentage }}%</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="fw-bold">Duration:</td>
                                <td>{{ $service->duration_minutes ? $service->duration_minutes . ' minutes' : 'Not specified' }}</td>
                            </tr>
                            @if($service->start_time && $service->end_time)
                                <tr>
                                    <td class="fw-bold">Time Range:</td>
                                    <td>{{ $service->start_time->format('H:i') }} - {{ $service->end_time->format('H:i') }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($service->description)
                    <div class="mt-3">
                        <h6>Description</h6>
                        <p class="text-muted">{{ $service->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Service Requirements -->
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Service Requirements</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Requirements</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-{{ $service->requires_appointment ? 'check text-success' : 'times text-muted' }} me-2"></i>Requires Appointment</li>
                            <li><i class="fas fa-{{ $service->available_emergency ? 'check text-success' : 'times text-muted' }} me-2"></i>Available for Emergency</li>
                            <li><i class="fas fa-{{ $service->requires_lab_results ? 'check text-success' : 'times text-muted' }} me-2"></i>Requires Lab Results</li>
                            <li><i class="fas fa-{{ $service->philhealth_covered ? 'check text-success' : 'times text-muted' }} me-2"></i>PhilHealth Covered</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Booking</h6>
                        <ul class="list-unstyled">
                            @if($service->advance_booking_days)
                                <li><i class="fas fa-calendar-check text-info me-2"></i>{{ $service->advance_booking_days }} days advance booking</li>
                            @else
                                <li><i class="fas fa-calendar-times text-muted me-2"></i>No advance booking required</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        @if($service->preparation_instructions || $service->post_service_instructions || $service->contraindications)
            <div class="admin-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-md me-2"></i>Patient Instructions</h5>
                </div>
                <div class="card-body">
                    @if($service->preparation_instructions)
                        <h6>Preparation Instructions</h6>
                        <p class="text-muted">{{ $service->preparation_instructions }}</p>
                    @endif

                    @if($service->post_service_instructions)
                        <h6>Post-Service Instructions</h6>
                        <p class="text-muted">{{ $service->post_service_instructions }}</p>
                    @endif

                    @if($service->contraindications)
                        <h6>Contraindications</h6>
                        <p class="text-muted">{{ $service->contraindications }}</p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Resource Requirements -->
        @if($service->required_equipment || $service->required_supplies || $service->staff_requirements)
            <div class="admin-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Resource Requirements</h5>
                </div>
                <div class="card-body">
                    @if($service->required_equipment)
                        <h6>Required Equipment</h6>
                        <p class="text-muted">{{ $service->required_equipment }}</p>
                    @endif

                    @if($service->required_supplies)
                        <h6>Required Supplies</h6>
                        <p class="text-muted">{{ $service->required_supplies }}</p>
                    @endif

                    @if($service->staff_requirements)
                        <h6>Staff Requirements</h6>
                        <p class="text-muted">{{ $service->staff_requirements }}</p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Quality & Compliance -->
        @if($service->quality_indicators || $service->regulatory_requirements || $service->consent_form_required || $service->documentation_requirements)
            <div class="admin-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Quality & Compliance</h5>
                </div>
                <div class="card-body">
                    @if($service->quality_indicators)
                        <h6>Quality Indicators</h6>
                        <p class="text-muted">{{ $service->quality_indicators }}</p>
                    @endif

                    @if($service->regulatory_requirements)
                        <h6>Regulatory Requirements</h6>
                        <p class="text-muted">{{ $service->regulatory_requirements }}</p>
                    @endif

                    @if($service->consent_form_required)
                        <h6>Consent Form Required</h6>
                        <p class="text-muted">{{ $service->consent_form_required }}</p>
                    @endif

                    @if($service->documentation_requirements)
                        <h6>Documentation Requirements</h6>
                        <p class="text-muted">{{ $service->documentation_requirements }}</p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Internal Notes -->
        @if($service->internal_notes)
            <div class="admin-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Internal Notes</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ $service->internal_notes }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Recent Appointments -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Recent Appointments</h6>
            </div>
            <div class="card-body">
                @if($appointments->count() > 0)
                    @foreach($appointments as $appointment)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="fw-bold">{{ $appointment->patient->name }}</div>
                                <small class="text-muted">{{ $appointment->appointment_datetime->format('M d, Y H:i') }}</small>
                            </div>
                            <span class="badge bg-{{ $appointment->status === 'completed' ? 'success' : ($appointment->status === 'scheduled' ? 'primary' : 'secondary') }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted mb-0">No appointments found for this service.</p>
                @endif
            </div>
        </div>

        <!-- Service Statistics -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Service Statistics</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Total Appointments:</strong><br>
                    <span class="text-primary">{{ $appointments->count() }}</span>
                </div>
                <div class="mb-3">
                    <strong>Created:</strong><br>
                    <span class="text-muted">{{ $service->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="mb-3">
                    <strong>Last Updated:</strong><br>
                    <span class="text-muted">{{ $service->updated_at->format('M d, Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin-portal.services.edit', $service->id) }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-1"></i>Edit Service
                    </a>
                    @if($service->appointments()->count() === 0)
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash me-1"></i>Delete Service
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($service->appointments()->count() === 0)
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the service "<strong>{{ $service->name }}</strong>"?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('admin-portal.services.delete', $service->id) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Service</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endif
@endsection
