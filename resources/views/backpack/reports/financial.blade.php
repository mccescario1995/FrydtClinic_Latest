@extends(backpack_view('blank'))

@section('header')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Financial Reports</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ backpack_url() }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ backpack_url('reports') }}">Reports</a></li>
                        <li class="breadcrumb-item active">Financial Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Financial Overview -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>₱{{ number_format($monthly_revenue->sum('revenue'), 2) }}</h3>
                    <p>Monthly Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>₱{{ number_format($pending_payments->sum('balance_due'), 2) }}</h3>
                    <p>Pending Payments</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>₱{{ number_format($overdue_payments->sum('balance_due'), 2) }}</h3>
                    <p>Overdue Payments</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $outstanding_payments->count() }}</h3>
                    <p>Outstanding Bills</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Revenue Chart -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Monthly Revenue Trend</h3>
                    <div class="card-tools">
                        <span class="text-muted small">Export functionality temporarily disabled</span>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Methods</h3>
                </div>
                <div class="card-body">
                    <canvas id="paymentMethodsChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Outstanding Payments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Outstanding Payments</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Invoice #</th>
                                    <th>Due Date</th>
                                    <th>Amount Due</th>
                                    <th>Status</th>
                                    <th>Days Overdue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($outstanding_payments as $payment)
                                <tr>
                                    <td>{{ $payment->patient->name ?? 'N/A' }}</td>
                                    <td>{{ $payment->invoice_number ?? 'N/A' }}</td>
                                    <td>{{ $payment->due_date ? $payment->due_date->format('M j, Y') : 'N/A' }}</td>
                                    <td>₱{{ number_format($payment->balance_due, 2) }}</td>
                                    <td>
                                        @if($payment->due_date && $payment->due_date->isPast())
                                            <span class="badge text-bg-danger">Overdue</span>
                                        @else
                                            <span class="badge text-bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->due_date && $payment->due_date->isPast())
                                            {{ $payment->due_date->diffInDays(now()) }} days
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No outstanding payments</td>
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
    // Monthly Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = @json($monthly_revenue);

    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueData.map(item => `${item.month}/${item.year}`),
            datasets: [{
                label: 'Revenue',
                data: revenueData.map(item => item.revenue),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Collected',
                data: revenueData.map(item => item.collected),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Payment Methods Chart
    const paymentCtx = document.getElementById('paymentMethodsChart').getContext('2d');
    const paymentData = @json($payment_methods);

    new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: paymentData.map(item => item.payment_method || 'Other'),
            datasets: [{
                data: paymentData.map(item => item.total),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 205, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
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
