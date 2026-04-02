@extends('admin-portal.layouts.app')

@section('title', 'Medical Records Overview')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title"><i class="fas fa-file-medical me-2"></i>Medical Records Overview</h1>
                <p class="page-subtitle">System-wide medical records statistics and recent activity</p>
            </div>
            <div>
                <span
                    class="badge bg-primary fs-6">{{ number_format($stats['prenatal'] + $stats['postnatal'] + $stats['postpartum'] + $stats['delivery'] + $stats['lab']) }}
                    Total Records</span>
            </div>
        </div>
    </div>

    <!-- Medical Records Statistics -->
    <div class="stats-grid">
        <div class="admin-card text-center">
            <div class="card-body">
                <div class="h1 text-primary mb-2">{{ number_format($stats['prenatal']) }}</div>
                <h6 class="text-muted mb-0">Prenatal Records</h6>
                <small class="text-primary">
                    <i class="fas fa-baby me-1"></i>Pregnancy Care
                </small>
            </div>
        </div>
        <div class="admin-card text-center">
            <div class="card-body">
                <div class="h1 text-success mb-2">{{ number_format($stats['postnatal']) }}</div>
                <h6 class="text-muted mb-0">Postnatal Records</h6>
                <small class="text-success">
                    <i class="fas fa-child me-1"></i>Post-Birth Care
                </small>
            </div>
        </div>
        <div class="admin-card text-center">
            <div class="card-body">
                <div class="h1 text-warning mb-2">{{ number_format($stats['postpartum']) }}</div>
                <h6 class="text-muted mb-0">Postpartum Records</h6>
                <small class="text-warning">
                    <i class="fas fa-female me-1"></i>Recovery Care
                </small>
            </div>
        </div>
        <div class="admin-card text-center">
            <div class="card-body">
                <div class="h1 text-danger mb-2">{{ number_format($stats['delivery']) }}</div>
                <h6 class="text-muted mb-0">Delivery Records</h6>
                <small class="text-danger">
                    <i class="fas fa-hospital me-1"></i>Birth Records
                </small>
            </div>
        </div>
        <div class="admin-card text-center">
            <div class="card-body">
                <div class="h1 text-info mb-2">{{ number_format($stats['lab']) }}</div>
                <h6 class="text-muted mb-0">Lab Results</h6>
                <small class="text-info">
                    <i class="fas fa-flask me-1"></i>Test Results
                </small>
            </div>
        </div>
        <div class="admin-card text-center">
            <div class="card-body">
                <div class="h1 text-secondary mb-2">{{ number_format(array_sum($stats)) }}</div>
                <h6 class="text-muted mb-0">Total Records</h6>
                <small class="text-secondary">
                    <i class="fas fa-database me-1"></i>All Records
                </small>
            </div>
        </div>
    </div>

    <!-- Recent Medical Records -->
    <div class="row">
        <div class="col-12 col-md-6">
            <!-- Recent Prenatal Records -->
            <div class="row mb-4">
                <div class="admin-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-baby me-2"></i>Recent Prenatal Records</h5>
                    </div>
                    <div class="card-body">
                        @if ($recentPrenatal->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($recentPrenatal as $record)
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $record->patient->name ?? 'Unknown Patient' }}</h6>
                                                <small class="text-muted">
                                                    <i
                                                        class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') }}
                                                    <i class="fas fa-hashtag ms-2 me-1"></i>Visit
                                                    {{ $record->visit_number }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span
                                                    class="badge bg-primary">{{ $record->attendingPhysician->name ?? $record->midwife->name ?? 'Unknown' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="text-center mt-3">
                                <a href="{{ route('admin-portal.prenatal-records') }}"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-list me-1"></i>View All Prenatal Records
                                </a>
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-baby"></i>
                                </div>
                                <h5 class="empty-title">No Recent Prenatal Records</h5>
                                <p class="empty-text">Recent prenatal records will appear here once they are added to the
                                    system.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Lab Results -->
            <div class="row mb-4">
                <div class="admin-card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-flask me-2"></i>Recent Lab Results</h5>
                    </div>
                    <div class="card-body">
                        @if ($recentLab->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($recentLab as $result)
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $result->test_name ?? 'Lab Test' }}</h6>
                                                <small class="text-muted">
                                                    <i
                                                        class="fas fa-user me-1"></i>{{ $result->patient->name ?? 'Unknown Patient' }}
                                                    <i
                                                        class="fas fa-calendar ms-2 me-1"></i>{{ \Carbon\Carbon::parse($result->test_ordered_date_time)->format('M d, Y') }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span
                                                    class="badge bg-{{ $result->result_status === 'completed' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($result->result_status ?? 'pending') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="text-center mt-3">
                                <a href="{{ route('admin-portal.medical-records.lab-results') }}"
                                    class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-list me-1"></i>View All Lab Results
                                </a>
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-flask"></i>
                                </div>
                                <h5 class="empty-title">No Recent Lab Results</h5>
                                <p class="empty-text">Recent lab results will appear here once they are added to the system.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Postnatal Records -->
            <div class="row mb-4">
                <div class="admin-card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-child me-2"></i>Recent Postnatal Records</h5>
                    </div>
                    <div class="card-body">
                        @if ($recentPostnatal->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($recentPostnatal as $record)
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $record->patient->name ?? 'Unknown Patient' }}</h6>
                                                <small class="text-muted">
                                                    <i
                                                        class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') }}
                                                    <i class="fas fa-hashtag ms-2 me-1"></i>Visit
                                                    {{ $record->visit_number }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span
                                                    class="badge bg-success">{{ $record->provider->name ?? 'Unknown' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="text-center mt-3">
                                <a href="{{ route('admin-portal.medical-records.postnatal') }}"
                                    class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-list me-1"></i>View All Postnatal Records
                                </a>
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-child"></i>
                                </div>
                                <h5 class="empty-title">No Recent Postnatal Records</h5>
                                <p class="empty-text">Recent postnatal records will appear here once they are added to the
                                    system.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Postpartum Records -->
            <div class="row mb-4">
                <div class="admin-card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="fas fa-female me-2"></i>Recent Postpartum Records</h5>
                    </div>
                    <div class="card-body">
                        @if ($recentPostpartum->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($recentPostpartum as $record)
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $record->patient->name ?? 'Unknown Patient' }}</h6>
                                                <small class="text-muted">
                                                    <i
                                                        class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') }}
                                                    <i class="fas fa-hashtag ms-2 me-1"></i>Visit
                                                    {{ $record->visit_number }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span
                                                    class="badge bg-warning">{{ $record->provider->name ?? 'Unknown' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="text-center mt-3">
                                <a href="{{ route('admin-portal.medical-records.postpartum') }}"
                                    class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-list me-1"></i>View All Postpartum Records
                                </a>
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-female"></i>
                                </div>
                                <h5 class="empty-title">No Recent Postpartum Records</h5>
                                <p class="empty-text">Recent postpartum records will appear here once they are added to the
                                    system.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Delivery Records -->
            <div class="row mb-4">
                <div class="admin-card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-hospital me-2"></i>Recent Delivery Records</h5>
                    </div>
                    <div class="card-body">
                        @if ($recentDelivery->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($recentDelivery as $record)
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $record->patient->name ?? 'Unknown Patient' }}</h6>
                                                <small class="text-muted">
                                                    <i
                                                        class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($record->delivery_date_time)->format('M d, Y') }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span
                                                    class="badge bg-danger">{{ $record->attendingProvider->name ?? 'Unknown' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="text-center mt-3">
                                <a href="{{ route('admin-portal.medical-records.delivery') }}"
                                    class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-list me-1"></i>View All Delivery Records
                                </a>
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-hospital"></i>
                                </div>
                                <h5 class="empty-title">No Recent Delivery Records</h5>
                                <p class="empty-text">Recent delivery records will appear here once they are added to the
                                    system.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 ">
            <div class="content-section min-vh-100">
                <div class="section-header">
                    <h2 class="section-title"><i class="fas fa-chart-pie me-2"></i>Medical Records Distribution</h2>
                    <p class="section-subtitle">Visual breakdown of medical record types across the system</p>
                </div>

                <div class=" ">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <canvas id="medicalRecordsChart" width="250" height="125"></canvas>
                            </div>
                            <div class="col-md-4">
                                <div class="mt-4">
                                    <h6>Record Types</h6>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span><i class="fas fa-baby text-primary me-2"></i>Prenatal</span>
                                            <span class="badge bg-primary">{{ number_format($stats['prenatal']) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span><i class="fas fa-child text-success me-2"></i>Postnatal</span>
                                            <span class="badge bg-success">{{ number_format($stats['postnatal']) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span><i class="fas fa-female text-warning me-2"></i>Postpartum</span>
                                            <span
                                                class="badge bg-warning">{{ number_format($stats['postpartum']) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span><i class="fas fa-hospital text-danger me-2"></i>Delivery</span>
                                            <span class="badge bg-danger">{{ number_format($stats['delivery']) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span><i class="fas fa-flask text-info me-2"></i>Lab Results</span>
                                            <span class="badge bg-info">{{ number_format($stats['lab']) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Records Distribution Chart -->


        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Medical Records Distribution Chart
            const medicalCtx = document.getElementById('medicalRecordsChart').getContext('2d');
            new Chart(medicalCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Prenatal', 'Postnatal', 'Postpartum', 'Delivery', 'Lab Results'],
                    datasets: [{
                        data: {{ json_encode([$stats['prenatal'], $stats['postnatal'], $stats['postpartum'], $stats['delivery'], $stats['lab']]) }},
                        backgroundColor: [
                            '#3498db', // prenatal
                            '#27ae60', // postnatal
                            '#f39c12', // postpartum
                            '#e74c3c', // delivery
                            '#9b59b6' // lab
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        </script>
    @endsection
