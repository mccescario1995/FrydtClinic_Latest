@extends('admin-portal.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Postnatal Records</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin-portal.medical-records') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Medical Records
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters and search form -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="patient_id" class="form-control">
                                    <option value="">All Patients</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="provider_id" class="form-control">
                                    <option value="">All Providers</option>
                                    @foreach($providers as $provider)
                                        <option value="{{ $provider->id }}" {{ request('provider_id') == $provider->id ? 'selected' : '' }}>
                                            {{ $provider->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>

                    <!-- Records table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Provider</th>
                                    <th>Visit Date</th>
                                    <th>Visit Number</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($postnatalRecords as $record)
                                    <tr>
                                        <td>{{ $record->patient->name ?? 'N/A' }}</td>
                                        <td>{{ $record->provider->name ?? 'N/A' }}</td>
                                        <td>{{ $record->visit_date ? \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') : 'N/A' }}</td>
                                        <td>{{ $record->visit_number }}</td>
                                        <td>
                                            <a href="{{ route('admin-portal.patients.show', $record->patient_id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View Patient
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No postnatal records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    {{ $postnatalRecords->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
