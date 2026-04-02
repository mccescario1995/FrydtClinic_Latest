@extends(backpack_view('blank'))

@section('header')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Laboratory Reports</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ backpack_url() }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ backpack_url('reports') }}">Reports</a></li>
                        <li class="breadcrumb-item active">Laboratory Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Lab Statistics Overview -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $test_volume_by_category->sum('count') }}</h3>
                    <p>Total Tests (6 Months)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-flask"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $result_distribution->where('result_status', 'normal')->first()->count ?? 0 }}</h3>
                    <p>Normal Results</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $result_distribution->where('result_status', 'abnormal')->first()->count ?? 0 }}</h3>
                    <p>Abnormal Results</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $result_distribution->where('result_status', 'critical')->first()->count ?? 0 }}</h3>
                    <p>Critical Results</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Volume by Category -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Test Volume by Category</h3>
                    <div class="card-tools">
                        <span class="text-muted small">Export functionality temporarily disabled</span>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="testVolumeChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Turnaround Times -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Average Turnaround Times</h3>
                </div>
                <div class="card-body">
                    <canvas id="turnaroundChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Result Status Distribution -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Result Status Distribution</h3>
                </div>
                <div class="card-body">
                    <canvas id="resultStatusChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Critical Results -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Critical Results</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Test</th>
                                    <th>Result</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\LaboratoryResult::with('patient.user')
                                    ->where('result_status', 'critical')
                                    ->where('result_available_date_time', '>=', now()->subDays(30))
                                    ->orderBy('result_available_date_time', 'desc')
                                    ->limit(5)
                                    ->get() as $result)
                                <tr>
                                    <td>{{ $result->patient->name ?? 'N/A' }}</td>
                                    <td>{{ $result->test_name }}</td>
                                    <td>
                                        <span class="badge text-bg-danger">{{ $result->result_value }}</span>
                                    </td>
                                    <td>{{ $result->result_available_date_time ? $result->result_available_date_time->format('M j') : 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No critical results in the last 30 days</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Tests Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Most Requested Tests (Last 6 Months)</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Test Category</th>
                                    <th>Number of Tests</th>
                                    <th>Avg Turnaround (Hours)</th>
                                    <th>Success Rate (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($test_volume_by_category as $category)
                                <tr>
                                    <td>{{ $category->test_category ?? 'N/A' }}</td>
                                    <td>{{ $category->count }}</td>
                                    <td>
                                        @php
                                            $turnaround = $turnaround_times->where('test_category', $category->test_category)->first();
                                        @endphp
                                        {{ $turnaround ? number_format($turnaround->avg_turnaround_hours, 1) : 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge text-bg-success">98.5%</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No test data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('after_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Test Volume Chart
    const volumeCtx = document.getElementById('testVolumeChart').getContext('2d');
    const volumeData = @json($test_volume_by_category);

    new Chart(volumeCtx, {
        type: 'bar',
        data: {
            labels: volumeData.map(item => item.test_category || 'Other'),
            datasets: [{
                label: 'Number of Tests',
                data: volumeData.map(item => item.count),
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Turnaround Times Chart
    const turnaroundCtx = document.getElementById('turnaroundChart').getContext('2d');
    const turnaroundData = @json($turnaround_times);

    new Chart(turnaroundCtx, {
        type: 'horizontalBar',
        data: {
            labels: turnaroundData.map(item => item.test_category || 'Other'),
            datasets: [{
                label: 'Average Hours',
                data: turnaroundData.map(item => item.avg_turnaround_hours),
                backgroundColor: 'rgba(255, 159, 64, 0.8)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });

    // Result Status Distribution Chart
    const statusCtx = document.getElementById('resultStatusChart').getContext('2d');
    const statusData = @json($result_distribution);

    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: statusData.map(item => item.result_status || 'Unknown'),
            datasets: [{
                data: statusData.map(item => item.count),
                backgroundColor: [
                    'rgba(75, 192, 192, 0.8)',  // normal
                    'rgba(255, 205, 86, 0.8)',  // abnormal
                    'rgba(255, 99, 132, 0.8)',  // critical
                    'rgba(153, 102, 255, 0.8)', // other
                ],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
        }
    });
</script>
@endpush
@endsection
