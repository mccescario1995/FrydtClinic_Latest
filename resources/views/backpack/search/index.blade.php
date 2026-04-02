@extends(backpack_view('blank'))

@section('header')
    <section class="container-fluid d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2><i class="la la-search"></i> Global Search</h2>
            <p class="text-muted mb-0">Search across all clinic data</p>
        </div>
        <div>
            <a href="{{ backpack_url('search/advanced') }}" class="btn btn-outline-primary">
                <i class="la la-filter"></i> Advanced Search
            </a>
        </div>
    </section>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <!-- Search Form -->
                <form method="GET" action="{{ route('search.index') }}" class="mb-4">
                    <div class="input-group input-group-lg">
                        <input type="text"
                               name="q"
                               class="form-control"
                               placeholder="Search patients, appointments, lab results..."
                               value="{{ $query }}"
                               autocomplete="off"
                               id="search-input">
                        <button class="btn btn-primary" type="submit">
                            <i class="la la-search"></i> Search
                        </button>
                    </div>
                </form>

                @if(!empty($query))
                    <div class="mb-3">
                        <h5>Search Results for: <strong>"{{ $query }}"</strong></h5>
                        <hr>
                    </div>

                    <!-- Search Results -->
                    @if(count($results) > 0)
                        @foreach($results as $type => $items)
                            @if(count($items) > 0)
                                <div class="search-section mb-4">
                                    <h6 class="text-primary mb-3">
                                        <i class="la la-{{ $type === 'users' ? 'users' : ($type === 'appointments' ? 'calendar' : ($type === 'patients' ? 'user-md' : 'flask')) }}"></i>
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                        <span class="badge bg-secondary">{{ count($items) }}</span>
                                    </h6>

                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <tbody>
                                                @foreach($items as $item)
                                                    <tr class="search-result-row" style="cursor: pointer;" onclick="viewResult('{{ $type }}', {{ $item->id }})">
                                                        <td>
                                                            @if($type === 'users')
                                                                <strong>{{ $item->name }}</strong><br>
                                                                <small class="text-muted">{{ $item->email }}</small>
                                                            @elseif($type === 'appointments')
                                                                <strong>{{ $item->patient->name ?? 'N/A' }}</strong> - {{ $item->service->name ?? 'N/A' }}<br>
                                                                <small class="text-muted">{{ $item->appointment_datetime->format('M j, Y g:i A') }}</small>
                                                            @elseif($type === 'patients')
                                                                <strong>{{ $item->name ?? 'N/A' }}</strong><br>
                                                                <small class="text-muted">{{ $item->phone ?? 'No phone' }}</small>
                                                            @elseif($type === 'lab_results')
                                                                <strong>{{ $item->patient->name ?? 'N/A' }}</strong> - {{ $item->test_name }}<br>
                                                                <small class="text-muted">{{ $item->result_value ?? 'N/A' }}</small>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">
                                                            <small class="text-muted">
                                                                @if($type === 'users' || $type === 'patients')
                                                                    {{ $item->created_at->format('M j, Y') }}
                                                                @elseif($type === 'appointments')
                                                                    {{ $item->created_at->format('M j, Y') }}
                                                                @elseif($type === 'lab_results')
                                                                    {{ $item->result_available_date_time ? $item->result_available_date_time->format('M j, Y') : 'N/A' }}
                                                                @endif
                                                            </small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    @if(count($items) >= 10)
                                        <div class="text-center mt-2">
                                            <a href="{{ route('search.quick', $type) }}?q={{ urlencode($query) }}" class="btn btn-sm btn-outline-primary">
                                                View All {{ ucfirst(str_replace('_', ' ', $type)) }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="la la-search la-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No results found</h5>
                            <p class="text-muted">Try adjusting your search terms or check the spelling</p>
                        </div>
                    @endif
                @else
                    <!-- Search Statistics -->
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-primary">{{ number_format($stats['total_users']) }}</h3>
                                    <p class="mb-0">Total Users</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-success">{{ number_format($stats['total_patients']) }}</h3>
                                    <p class="mb-0">Patients</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-info">{{ number_format($stats['total_appointments']) }}</h3>
                                    <p class="mb-0">Appointments</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-warning">{{ number_format($stats['total_lab_results']) }}</h3>
                                    <p class="mb-0">Lab Results</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted">Enter a search term above to find specific records</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function viewResult(type, id) {
    let url = '';

    switch(type) {
        case 'users':
            url = '{{ backpack_url("user") }}/' + id + '/show';
            break;
        case 'appointments':
            url = '{{ backpack_url("appointment") }}/' + id + '/show';
            break;
        case 'patients':
            url = '{{ backpack_url("patient") }}/' + id + '/show';
            break;
        case 'lab_results':
            // You might need to create a lab results CRUD for this
            url = '#'; // Placeholder
            break;
    }

    if (url !== '#') {
        window.location.href = url;
    }
}

// Auto-suggest functionality
document.getElementById('search-input').addEventListener('input', function() {
    const query = this.value;
    if (query.length >= 2) {
        fetch('{{ route("search.suggestions") }}?q=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                // You can implement a dropdown with suggestions here
                console.log('Suggestions:', data.suggestions);
            })
            .catch(error => console.error('Error:', error));
    }
});
</script>

<style>
.search-result-row:hover {
    background-color: #f8f9fa;
}

.search-section {
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 1rem;
}
</style>
@endsection
