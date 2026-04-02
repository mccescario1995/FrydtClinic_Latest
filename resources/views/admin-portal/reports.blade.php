@extends('admin-portal.layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"><i class="fas fa-chart-bar me-2"></i>Reports & Analytics</h1>
            <p class="page-subtitle">System analytics and performance metrics</p>
        </div>
        <div class="text-muted">
            <small>Report generated: {{ now()->format('M d, Y H:i') }}</small>
        </div>
    </div>
</div>

<!-- Key Metrics Summary -->
<div class="stats-grid">
    <div class="admin-card text-center">
        <div class="card-body">
            <div class="h1 text-primary mb-2">{{ number_format(collect($appointmentStats)->sum('count')) }}</div>
            <h6 class="text-muted mb-0">Total Appointments (12 months)</h6>
        </div>
    </div>
    <div class="admin-card text-center">
        <div class="card-body">
            <div class="h1 text-info mb-2">{{ $statusStats['completed'] ?? 0 }}</div>
            <h6 class="text-muted mb-0">Completed Appointments</h6>
        </div>
    </div>
    <div class="admin-card text-center">
        <div class="card-body">
            <div class="h1 text-warning mb-2">{{ $statusStats['scheduled'] ?? 0 }}</div>
            <h6 class="text-muted mb-0">Scheduled Appointments</h6>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="content-section">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-chart-area me-2"></i>Analytics Charts</h2>
        <p class="section-subtitle">Visual representation of system data and trends</p>
    </div>

    <div class="row mb-4">
    <!-- Appointment Trends -->
    <div class="col-lg-6 mb-4">
        <div class="admin-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Appointment Trends (Last 12 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="appointmentChart" width="250" height="120"></canvas>
            </div>
        </div>
    </div>

    <!-- Appointment Status Distribution -->
    <div class="col-lg-3 mb-4">
        <div class="admin-card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Appointment Status</h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart" width="120" height="120"></canvas>
                <div class="mt-3">
                    @foreach($statusStats as $status => $count)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-capitalize">{{ $status }}</span>
                            <span class="badge bg-{{ match($status) {
                                'scheduled' => 'warning',
                                'confirmed' => 'info',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'secondary'
                            } }}">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-lg-3 mb-4">
        <div class="admin-card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Quick Stats</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Avg per Month:</span>
                        <strong>{{ round(collect($appointmentStats)->avg('count'), 1) }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Peak Month:</span>
                        <strong>{{ collect($appointmentStats)->sortByDesc('count')->first()['month'] ?? 'N/A' }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Completion Rate:</span>
                        <strong>{{ $statusStats['completed'] ?? 0 }}/{{ array_sum($statusStats) }}</strong>
                    </div>
                </div>
                <div class="mb-0">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Active Users:</span>
                        <strong>{{ $activeUsers }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<!-- Detailed Statistics Table -->
<div class="content-section">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-table me-2"></i>Detailed Monthly Statistics</h2>
        <p class="section-subtitle">Comprehensive breakdown of monthly performance metrics</p>
    </div>
<div class="card">
    <div class="">
        <div class="table-responsive">
            <table class="table admin-table table-hover">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Appointments</th>
                        <th>Growth Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointmentStats as $index => $stat)
                        @php
                            $prevAppointments = $index > 0 ? $appointmentStats[$index - 1]['count'] : 0;
                            $appointmentGrowth = $prevAppointments > 0 ? (($stat['count'] - $prevAppointments) / $prevAppointments) * 100 : 0;
                        @endphp
                        <tr>
                            <td class="fw-bold">{{ $stat['month'] }}</td>
                            <td>{{ number_format($stat['count']) }}</td>
                            <td>
                                <span class="badge bg-{{ $appointmentGrowth >= 0 ? 'success' : 'danger' }}">
                                    <i class="fas fa-{{ $appointmentGrowth >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                                    {{ number_format(abs($appointmentGrowth), 1) }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Appointment Trends Chart
const appointmentCtx = document.getElementById('appointmentChart').getContext('2d');
new Chart(appointmentCtx, {
    type: 'line',
    data: {
        labels: @json(collect($appointmentStats)->pluck('month')),
        datasets: [{
            label: 'Appointments',
            data: @json(collect($appointmentStats)->pluck('count')),
            borderColor: '#3498db',
            backgroundColor: 'rgba(52, 152, 219, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: @json(array_keys($statusStats)),
        datasets: [{
            data: @json(array_values($statusStats)),
            backgroundColor: [
                '#f39c12', // scheduled
                '#3498db', // confirmed
                '#27ae60', // completed
                '#e74c3c'  // cancelled
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

</script>
@endsection
