@extends('admin-portal.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h1>
            <p class="page-subtitle">System overview and key metrics</p>
        </div>
        <div class="text-muted">
            <small>Last updated: {{ now()->format('M d, Y H:i') }}</small>
        </div>
    </div>
</div>

<!-- Inventory Alerts -->
@if($expiringItems->count() > 0)
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Items Expiring Soon:</strong> {{ $expiringItems->count() }} item(s) will expire within the alert period.
    <a href="{{ route('admin-portal.inventory') }}?item_type=&category=&stock_status=expiring_soon&search=" class="alert-link">View Inventory</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($expiredItems->count() > 0)
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Expired Items:</strong> {{ $expiredItems->count() }} item(s) have expired.
    <a href="{{ route('admin-portal.inventory') }}?item_type=&category=&stock_status=expired&search=" class="alert-link">View Inventory</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($lowStockItems->count() > 0)
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Low Stock Items:</strong> {{ $lowStockItems->count() }} item(s) are running low on stock.
    <a href="{{ route('admin-portal.inventory') }}?item_type=&category=&stock_status=low_stock&search=" class="alert-link">View Inventory</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($outOfStockItems->count() > 0)
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Out of Stock Items:</strong> {{ $outOfStockItems->count() }} item(s) are currently out of stock.
    <a href="{{ route('admin-portal.inventory') }}" class="alert-link">View Inventory</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stats-card">
        <div class="card-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="card-title">Total Users</div>
        <div class="card-value">{{ number_format($totalUsers) }}</div>
    </div>
    <div class="stats-card">
        <div class="card-icon">
            <i class="fas fa-user-injured"></i>
        </div>
        <div class="card-title">Patients</div>
        <div class="card-value">{{ number_format($totalPatients) }}</div>
    </div>
    <div class="stats-card">
        <div class="card-icon">
            <i class="fas fa-user-md"></i>
        </div>
        <div class="card-title">Employees</div>
        <div class="card-value">{{ number_format($totalEmployees) }}</div>
    </div>
    <div class="stats-card">
        <div class="card-icon">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="card-title">Total Appointments</div>
        <div class="card-value">{{ number_format($totalAppointments) }}</div>
    </div>
    <div class="stats-card" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
        <div class="card-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="card-title">Upcoming Appointments</div>
        <div class="card-value">{{ number_format($upcomingAppointments) }}</div>
    </div>
    <div class="stats-card" style="background: linear-gradient(135deg, #3498db, #5dade2);">
        <div class="card-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="card-title">Completed Appointments</div>
        <div class="card-value">{{ number_format($completedAppointments) }}</div>
    </div>
</div>

<!-- Main Content -->
<div class="content-section">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-calendar-alt me-2"></i>Recent Appointments</h2>
        <p class="section-subtitle">Latest appointment activity in the system</p>
    </div>

    <div class="row">
        <!-- Recent Appointments -->
        <div class="col-lg-8 mb-4">
            <div class="admin-card">
                <div class="card-body">
                @if($recentAppointments->count() > 0)
                    <div class="table-responsive">
                        <table class="table admin-table table-hover">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Service</th>
                                    <th>Provider</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAppointments as $appointment)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px; font-weight: bold;">
                                                    {{ substr($appointment->patient->name ?? 'N/A', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $appointment->patient->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $appointment->patient->email ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $appointment->service->name ?? 'N/A' }}</td>
                                        <td>{{ $appointment->employee->name ?? 'N/A' }}</td>
                                        <td>
                                            <div>{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('h:i A') }}</small>
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
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($appointment->status) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin-portal.appointments.show', $appointment->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('admin-portal.appointments') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-1"></i>View All Appointments
                        </a>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <h5 class="empty-title">No Recent Appointments</h5>
                        <p class="empty-text">Appointment data will appear here once appointments are scheduled.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

        <!-- Quick Stats & Actions -->
        <div class="col-lg-4">
            <!-- Medical Records Stats -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-file-medical me-2"></i>Medical Records</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="h4 mb-0 text-primary">{{ number_format($prenatalRecords) }}</div>
                            <small class="text-muted">Prenatal</small>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h4 mb-0 text-success">{{ number_format($postnatalRecords) }}</div>
                            <small class="text-muted">Postnatal</small>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h4 mb-0 text-warning">{{ number_format($postpartumRecords) }}</div>
                            <small class="text-muted">Postpartum</small>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h4 mb-0 text-danger">{{ number_format($deliveryRecords) }}</div>
                            <small class="text-muted">Delivery</small>
                        </div>
                    </div>
                    <div class="text-center mb-3">
                        <div class="h4 mb-0 text-info">{{ number_format($labResults) }}</div>
                        <small class="text-muted">Lab Results</small>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('admin-portal.medical-records') }}" class="btn btn-admin-primary">
                            <i class="fas fa-chart-bar me-1"></i>View Details
                        </a>
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
                        <a href="{{ route('admin-portal.users') }}" class="btn btn-admin-primary">
                            <i class="fas fa-users me-2"></i>Manage Users
                        </a>
                        <a href="{{ route('admin-portal.appointments') }}" class="btn btn-admin-primary">
                            <i class="fas fa-calendar-check me-2"></i>Manage Appointments
                        </a>
                        <a href="{{ route('admin-portal.reports') }}" class="btn btn-admin-primary">
                            <i class="fas fa-chart-line me-2"></i>View Reports
                        </a>
                        {{-- <a href="{{ route('admin-portal.settings') }}" class="btn btn-admin-primary">
                            <i class="fas fa-cogs me-2"></i>System Settings
                        </a> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
