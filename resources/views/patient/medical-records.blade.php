@extends('patient.layouts.app')

@section('title', 'Medical Records - FRYDT Patient Portal')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-notes-medical me-2"></i>My Medical Records
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Prenatal Records Section -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-baby me-2"></i>Prenatal Care Records
                        </h6>
                        @if($prenatalRecords->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Provider</th>
                                            <th>Gestational Age</th>
                                            <th>Blood Pressure</th>
                                            <th>Weight</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($prenatalRecords as $record)
                                        <tr>
                                            <td>{{ $record->visit_date->format('M j, Y') }}</td>
                                            <td>{{ $record->provider->name ?? 'N/A' }}</td>
                                            <td>{{ $record->gestational_age }}</td>
                                            <td>{{ $record->blood_pressure }}</td>
                                            <td>{{ $record->weight ? $record->weight . ' kg' : 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-success">Completed</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $prenatalRecords->links() }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-notes-medical fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No prenatal records found.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Postnatal Records Section -->
                    <div class="mb-4">
                        <h6 class="text-success mb-3">
                            <i class="fas fa-child me-2"></i>Postnatal Care Records
                        </h6>
                        @if($postnatalRecords->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Provider</th>
                                            <th>Days Postpartum</th>
                                            <th>Weight</th>
                                            <th>Blood Pressure</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($postnatalRecords as $record)
                                        <tr>
                                            <td>{{ $record->visit_date->format('M j, Y') }}</td>
                                            <td>{{ $record->provider->name ?? 'N/A' }}</td>
                                            <td>{{ $record->days_postpartum ?? 'N/A' }} days</td>
                                            <td>{{ $record->weight ? $record->weight . ' kg' : 'N/A' }}</td>
                                            <td>
                                                @if($record->blood_pressure_systolic && $record->blood_pressure_diastolic)
                                                    {{ $record->blood_pressure_systolic }}/{{ $record->blood_pressure_diastolic }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Completed</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $postnatalRecords->links() }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-child fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No postnatal records found.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Postpartum Records Section -->
                    <div class="mb-4">
                        <h6 class="text-warning mb-3">
                            <i class="fas fa-female me-2"></i>Postpartum Care Records
                        </h6>
                        @if($postpartumRecords->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Provider</th>
                                            <th>Weeks Postpartum</th>
                                            <th>Weight</th>
                                            <th>Blood Pressure</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($postpartumRecords as $record)
                                        <tr>
                                            <td>{{ $record->visit_date->format('M j, Y') }}</td>
                                            <td>{{ $record->provider->name ?? 'N/A' }}</td>
                                            <td>{{ $record->weeks_postpartum ?? 'N/A' }} weeks</td>
                                            <td>{{ $record->weight ? $record->weight . ' kg' : 'N/A' }}</td>
                                            <td>
                                                @if($record->blood_pressure_systolic && $record->blood_pressure_diastolic)
                                                    {{ $record->blood_pressure_systolic }}/{{ $record->blood_pressure_diastolic }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Completed</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $postpartumRecords->links() }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-female fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No postpartum records found.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Delivery Records Section -->
                    <div class="mb-4">
                        <h6 class="text-danger mb-3">
                            <i class="fas fa-hospital me-2"></i>Delivery Records
                        </h6>
                        @if($deliveryRecords->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Delivery Date</th>
                                            <th>Delivery Type</th>
                                            <th>Provider</th>
                                            <th>Newborn Gender</th>
                                            <th>Newborn Weight</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($deliveryRecords as $record)
                                        <tr>
                                            <td>{{ $record->delivery_date_time->format('M j, Y H:i') }}</td>
                                            <td>{{ $record->delivery_type ?? 'N/A' }}</td>
                                            <td>{{ $record->attendingProvider->name ?? 'N/A' }}</td>
                                            <td>{{ $record->newborn_gender ?? 'N/A' }}</td>
                                            <td>{{ $record->newborn_weight ? $record->newborn_weight . ' kg' : 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-success">Completed</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $deliveryRecords->links() }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-hospital fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No delivery records found.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Prescriptions Section -->
                    <div class="mb-4">
                        <h6 class="text-info mb-3">
                            <i class="fas fa-pills me-2"></i>Prescriptions
                        </h6>
                        @if(isset($prescriptions) && $prescriptions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Prescription #</th>
                                            <th>Medicine</th>
                                            <th>Dispensed Date</th>
                                            <th>Quantity</th>
                                            <th>Clinic Stock</th>
                                            <th>Total Price</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($prescriptions as $prescription)
                                        <tr>
                                            <td>{{ $prescription->prescription_number }}</td>
                                            <td>{{ $prescription->inventory->name ?? 'N/A' }}</td>
                                            <td>{{ $prescription->dispensed_date ? $prescription->dispensed_date->format('M j, Y') : 'N/A' }}</td>
                                            <td>{{ $prescription->quantity_dispensed ?: $prescription->quantity_prescribed }}</td>
                                            <td>
                                                @if($prescription->status === 'prescribed')
                                                    {{ $prescription->inventory->current_quantity ?? 0 }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>₱{{ number_format($prescription->total_price ?? 0, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $prescription->status === 'fully_dispensed' ? 'success' : ($prescription->status === 'partially_dispensed' ? 'warning' : ($prescription->status === 'pending_payment' ? 'primary' : ($prescription->status === 'external_purchase' ? 'info' : 'secondary'))) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $prescription->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($prescription->status === 'prescribed')
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <form method="POST" action="{{ route('patient.prescriptions.choose-location', $prescription->id) }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="purchase_location" value="clinic">
                                                            <button type="submit" class="btn btn-sm btn-success"
                                                                    @if($prescription->inventory->current_quantity < $prescription->quantity_prescribed) disabled @endif>
                                                                <i class="fas fa-store"></i> Clinic
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('patient.prescriptions.choose-location', $prescription->id) }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="purchase_location" value="outside">
                                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                                <i class="fas fa-external-link-alt"></i> Outside
                                                            </button>
                                                        </form>
                                                    </div>
                                                @elseif($prescription->purchase_location === 'clinic' && $prescription->isFullyDispensed)
                                                    @if($prescription->payment && $prescription->payment->isCompleted())
                                                        <a href="{{ route('patient.prescriptions.view', $prescription->id) }}"
                                                           class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i> View/Print
                                                        </a>
                                                    @else
                                                        <a href="{{ route('patient.prescriptions.pay', $prescription->id) }}"
                                                           class="btn btn-sm btn-success">
                                                            <i class="fas fa-credit-card"></i> Pay Now
                                                        </a>
                                                    @endif
                                                @elseif($prescription->purchase_location === 'outside' && $prescription->status === 'external_purchase')
                                                    <a href="{{ route('patient.prescriptions.view', $prescription->id) }}"
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> View/Print
                                                    </a>
                                                @elseif($prescription->status === 'pending_payment')
                                                    <span class="text-primary small">Payment Pending</span>
                                                @else
                                                    <span class="text-muted small">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $prescriptions->links() }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-pills fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No prescriptions found.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Laboratory Results Section -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-flask me-2"></i>Recent Laboratory Results
                        </h6>
                        @if($labResults->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Test Name</th>
                                            <th>Date Ordered</th>
                                            <th>Status</th>
                                            <th>Result</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($labResults as $result)
                                        <tr>
                                            <td>{{ $result->test_name }}</td>
                                            <td>{{ $result->test_ordered_date_time->format('M j, Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $result->getTestStatusBadgeClass() }}">
                                                    {{ ucfirst(str_replace('_', ' ', $result->test_status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $result->result_display ?? 'Pending' }}</td>
                                            <td>
                                                @if($result->isCompleted())
                                                    <a href="{{ route('patient.lab-result-detail', $result->id) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $labResults->links() }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-flask fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No laboratory results found.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
