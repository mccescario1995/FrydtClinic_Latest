@extends(backpack_view('blank'))

@section('header')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Patient Reports</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ backpack_url() }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ backpack_url('reports') }}">Reports</a></li>
                        <li class="breadcrumb-item active">Patient Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Patient Statistics -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Patient Demographics</h3>
                    <div class="card-tools">
                        <span class="text-muted small">Export functionality temporarily disabled</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Total Patients: {{ $total_patients }}</h4>
                            <h4>Active Patients: {{ $patients_by_gender->sum('count') ?? 0 }}</h4>
                        </div>
                        <div class="col-md-6">
                            <h5>Gender Distribution</h5>
                            <canvas id="genderChart" style="max-height: 200px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Age Groups -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Age Groups</h3>
                </div>
                <div class="card-body">
                    <canvas id="ageChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Patient Registrations -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Patient Registrations</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Gender</th>
                                    <th>Age</th>
                                    <th>Registration Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\PatientProfile::with('user')->latest()->limit(10)->get() as $patient)
                                <tr>
                                    <td>{{ $patient->name ?? 'N/A' }}</td>
                                    <td>{{ $patient->gender ?? 'N/A' }}</td>
                                    <td>
                                        @if($patient->birth_date)
                                            {{ $patient->birth_date->age }} years
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $patient->created_at ? $patient->created_at->format('M j, Y') : 'N/A' }}</td>
                                    <td>
                                        <span class="badge text-bg-success">Active</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No patients found</td>
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
    // Gender Distribution Chart
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    const genderData = @json($patients_by_gender);

    new Chart(genderCtx, {
        type: 'pie',
        data: {
            labels: genderData.map(item => item.gender || 'Not Specified'),
            datasets: [{
                data: genderData.map(item => item.count),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 205, 86, 0.8)',
                ],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
        }
    });

    // Age Groups Chart
    const ageCtx = document.getElementById('ageChart').getContext('2d');
    const ageData = @json($patients_by_age_group);

    new Chart(ageCtx, {
        type: 'bar',
        data: {
            labels: ageData.map(item => item.age_group),
            datasets: [{
                label: 'Number of Patients',
                data: ageData.map(item => item.count),
                backgroundColor: 'rgba(75, 192, 192, 0.8)',
                borderColor: 'rgba(75, 192, 192, 1)',
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
</script>
@endpush
@endsection
