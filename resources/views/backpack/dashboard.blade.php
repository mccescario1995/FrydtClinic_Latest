@extends(backpack_view('blank'))

@push('after_styles')
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-gray: #ecf0f1;
            --medium-gray: #bdc3c7;
            --dark-gray: #7f8c8d;
            --card-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            --card-shadow-hover: 0 4px 8px rgba(0, 0, 0, 0.15);
            --border-radius: 8px;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 3rem 0 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dashboard-header .container-fluid {
            padding: 0 2rem;
        }

        .dashboard-title {
            font-size: 2.8rem;
            font-weight: 300;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .dashboard-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            font-weight: 400;
            margin-bottom: 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--card-shadow);
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--card-shadow-hover);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--accent-color);
        }

        .stat-card.appointments::before {
            background: var(--accent-color);
        }

        .stat-card.patients::before {
            background: var(--success-color);
        }

        .stat-card.lab::before {
            background: var(--warning-color);
        }

        .stat-card.payments::before {
            background: var(--danger-color);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.8rem;
            color: white;
        }

        .stat-card.appointments .stat-icon {
            background: var(--accent-color);
        }

        .stat-card.patients .stat-icon {
            background: var(--success-color);
        }

        .stat-card.lab .stat-icon {
            background: var(--warning-color);
        }

        .stat-card.payments .stat-icon {
            background: var(--danger-color);
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .stat-label {
            font-size: 1rem;
            font-weight: 500;
            color: var(--dark-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }

        .stat-link {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .stat-link:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .chart-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .chart-header {
            background: var(--light-gray);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #dee2e6;
        }

        .chart-header h3 {
            margin: 0;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1.3rem;
        }

        .chart-body {
            padding: 2rem;
        }

        .quick-stats-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .quick-stats-header {
            background: var(--light-gray);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #dee2e6;
        }

        .quick-stats-header h4 {
            margin: 0;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1.2rem;
        }

        .quick-stats-body {
            padding: 2rem;
        }

        .stats-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .stats-row:last-child {
            margin-bottom: 0;
        }

        .stat-item {
            text-align: center;
            flex: 1;
        }

        .stat-item .value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.25rem;
        }

        .stat-item .label {
            font-size: 0.85rem;
            color: var(--dark-gray);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .activities-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .activity-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .activity-header {
            background: var(--light-gray);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .activity-header h4 {
            margin: 0;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1.2rem;
        }

        .activity-link {
            color: var(--accent-color);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .activity-link:hover {
            color: var(--primary-color);
        }

        .activity-body {
            padding: 0;
            max-height: 400px;
            overflow-y: auto;
        }

        .activity-item {
            padding: 1.25rem 2rem;
            border-bottom: 1px solid #f8f9fa;
            transition: background-color 0.2s ease;
        }

        .activity-item:hover {
            background-color: #f8f9fa;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-item .name {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.25rem;
            font-size: 1rem;
        }

        .activity-item .meta {
            color: var(--dark-gray);
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .activity-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .activity-badge.scheduled {
            background: var(--accent-color);
            color: white;
        }

        .activity-badge.completed {
            background: var(--success-color);
            color: white;
        }

        .activity-badge.cancelled {
            background: var(--danger-color);
            color: white;
        }

        .activity-badge.normal {
            background: var(--success-color);
            color: white;
        }

        .activity-badge.abnormal {
            background: var(--warning-color);
            color: white;
        }

        .staff-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .staff-header {
            background: var(--light-gray);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #dee2e6;
        }

        .staff-header h3 {
            margin: 0;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1.3rem;
        }

        .staff-grid {
            padding: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .staff-member {
            background: var(--light-gray);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
        }

        .staff-member:hover {
            transform: translateY(-2px);
            box-shadow: var(--card-shadow-hover);
            background: white;
        }

        .staff-member .name {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .staff-member .count {
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 0.5rem;
        }

        .staff-member .label {
            font-size: 0.9rem;
            color: var(--dark-gray);
            font-weight: 500;
        }

        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }

            .activities-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .dashboard-title {
                font-size: 2.2rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .staff-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 1rem;
            }

            .dashboard-header .container-fluid {
                padding: 0 1rem;
            }

            .chart-body,
            .quick-stats-body,
            .activity-body,
            .staff-grid {
                padding: 1rem;
            }
        }
    </style>
@endpush

@section('header')
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="dashboard-title">Clinic Management Dashboard</h1>
                    <p class="dashboard-subtitle">Monitor clinic operations, patient statistics, and performance metrics</p>
                </div>
                <div class="col-lg-4 text-end">
                    <div class="d-flex align-items-center justify-content-end">
                        <div class="me-3">
                            <small class="text-white-50">Last Updated</small><br>
                            <strong class="text-white">{{ now()->format('M j, Y \a\t g:i A') }}</strong>
                        </div>
                        <i class="fas fa-clock fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="dashboard-container">
        <!-- Key Performance Indicators -->
        <div class="stats-grid">
            <div class="stat-card appointments">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-value">{{ $today_appointments }}</div>
                <div class="stat-label">Today's Appointments</div>
                <a href="{{ route('appointment.index') }}" class="stat-link">
                    <i class="fas fa-arrow-right me-1"></i>View Details
                </a>
            </div>

            <div class="stat-card patients">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value">{{ $active_patients }}</div>
                <div class="stat-label">Active Patients</div>
                <a href="{{ route('patient.index') }}" class="stat-link">
                    <i class="fas fa-arrow-right me-1"></i>View Details
                </a>
            </div>

            <div class="stat-card lab">
                <div class="stat-icon">
                    <i class="fas fa-flask"></i>
                </div>
                <div class="stat-value">{{ $pending_lab_results }}</div>
                <div class="stat-label">Pending Lab Results</div>
                <a href="{{ route('laboratory-result.index') }}" class="stat-link">
                    <i class="fas fa-arrow-right me-1"></i>View Details
                </a>
            </div>

            <div class="stat-card payments">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-value">₱{{ number_format($pending_payments, 0) }}</div>
                <div class="stat-label">Pending Payments</div>
                <a href="{{ route('billing.index') }}" class="stat-link">
                    <i class="fas fa-arrow-right me-1"></i>View Details
                </a>
            </div>
        </div>

        <!-- Analytics & Performance Section -->
        <div class="content-grid">
            <!-- Monthly Trends Chart -->
            <div class="chart-section">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-line me-2"></i>Performance Analytics</h3>
                </div>
                <div class="chart-body">
                    <canvas id="monthlyTrendsChart" style="height: 350px;"></canvas>
                </div>
            </div>

            <!-- Quick Statistics Summary -->
            <div class="quick-stats-section">
                <div class="quick-stats-header">
                    <h4><i class="fas fa-chart-bar me-2"></i>Key Metrics</h4>
                </div>
                <div class="quick-stats-body">
                    <div class="stats-row">
                        <div class="stat-item">
                            <div class="value">{{ $total_patients }}</div>
                            <div class="label">Total Patients</div>
                        </div>
                        <div class="stat-item">
                            <div class="value">{{ $active_prenatal }}</div>
                            <div class="label">Prenatal Cases</div>
                        </div>
                    </div>
                    <div class="stats-row">
                        <div class="stat-item">
                            <div class="value">₱{{ number_format($total_revenue, 0) }}</div>
                            <div class="label">Monthly Revenue</div>
                        </div>
                        <div class="stat-item">
                            <div class="value">₱{{ number_format($overdue_payments, 0) }}</div>
                            <div class="label">Overdue Payments</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities & Updates -->
        <div class="activities-grid">
            <!-- Recent Appointments -->
            <div class="activity-section">
                <div class="activity-header">
                    <h4><i class="fas fa-calendar-check me-2"></i>Recent Appointments</h4>
                    <a href="{{ route('appointment.index') }}" class="activity-link">View All</a>
                </div>
                <div class="activity-body">
                    @forelse($recent_appointments as $appointment)
                        <div class="activity-item">
                            <div class="name">{{ $appointment->patient->name }}</div>
                            <div class="meta">
                                <i
                                    class="fas fa-clock me-1"></i>{{ $appointment->appointment_datetime->format('M j, Y \a\t g:i A') }}
                            </div>
                            <span class="activity-badge {{ $appointment->status }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="activity-item text-center">
                            <div class="text-muted py-3">
                                <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                <div>No recent appointments</div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Lab Results -->
            <div class="activity-section">
                <div class="activity-header">
                    <h4><i class="fas fa-flask me-2"></i>Recent Lab Results</h4>
                    <a href="{{ route('laboratory-result.index') }}" class="activity-link">View All</a>
                </div>
                <div class="activity-body">
                    @forelse($recent_lab_results as $result)
                        <div class="activity-item">
                            <div class="name">{{ $result->patient->name }}</div>
                            <div class="meta">
                                <i class="fas fa-vial me-1"></i>{{ $result->test_name }}
                            </div>
                            <span class="activity-badge {{ $result->result_status }}">
                                {{ ucfirst($result->result_status) }}
                            </span>
                        </div>
                    @empty
                        <div class="activity-item text-center">
                            <div class="text-muted py-3">
                                <i class="fas fa-flask fa-2x mb-2"></i>
                                <div>No recent lab results</div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Staff Performance Overview -->
        <div class="staff-section">
            <div class="staff-header">
                <h3><i class="fas fa-users-cog me-2"></i>Staff Performance Overview</h3>
            </div>
            <div class="staff-grid">
                @forelse($staff_performance as $staff)
                    <div class="staff-member">
                        <div class="name">{{ $staff->name }}</div>
                        <div class="count">{{ $staff->employee_appointments_count }}</div>
                        <div class="label">Appointments This Month</div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <div class="text-muted py-4">
                            <i class="fas fa-users fa-3x mb-3 opacity-50"></i>
                            <div>No staff performance data available</div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
</div>

@push('after_scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Enhanced Monthly Trends Chart
        const ctx = document.getElementById('monthlyTrendsChart').getContext('2d');
        const monthlyData = @json($monthly_trends);

        // Create gradients for better visual appeal
        const gradient1 = ctx.createLinearGradient(0, 0, 0, 300);
        gradient1.addColorStop(0, 'rgba(52, 152, 219, 0.3)');
        gradient1.addColorStop(1, 'rgba(52, 152, 219, 0.1)');

        const gradient2 = ctx.createLinearGradient(0, 0, 0, 300);
        gradient2.addColorStop(0, 'rgba(46, 204, 113, 0.3)');
        gradient2.addColorStop(1, 'rgba(46, 204, 113, 0.1)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyData.map(item => item.month),
                datasets: [{
                    label: 'New Patients',
                    data: monthlyData.map(item => item.patients),
                    borderColor: '#3498db',
                    backgroundColor: gradient1,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3498db',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#3498db',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3
                }, {
                    label: 'Appointments',
                    data: monthlyData.map(item => item.appointments),
                    borderColor: '#2ecc71',
                    backgroundColor: gradient2,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#2ecc71',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#2ecc71',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 13,
                                weight: '600',
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(44, 62, 80, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255,255,255,0.2)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        usePointStyle: true,
                        titleFont: {
                            size: 14,
                            weight: '600'
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#7f8c8d'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(127, 140, 141, 0.1)',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#7f8c8d'
                        }
                    }
                },
                elements: {
                    point: {
                        hoverBorderWidth: 3
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    </script>
@endpush
{{-- @endsection --}}
