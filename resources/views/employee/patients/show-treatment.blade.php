@extends('employee.layouts.app')

@section('title', 'Treatment Details - ' . $patient->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-prescription-bottle mr-2"></i>
                        Treatment Details for {{ $patient->name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('employee.patients.medical-records', $patient->id) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Medical Records
                        </a>
                        <a href="{{ route('employee.patients.show', $patient->id) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-user"></i> Patient Profile
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Treatment Information -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle mr-2"></i>Treatment Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <dl class="row">
                                                <dt class="col-sm-4">Treatment Type:</dt>
                                                <dd class="col-sm-8">{{ ucfirst($treatment->treatment_type) }}</dd>

                                                <dt class="col-sm-4">Treatment Name:</dt>
                                                <dd class="col-sm-8">{{ $treatment->treatment_name }}</dd>

                                                <dt class="col-sm-4">Generic Name:</dt>
                                                <dd class="col-sm-8">{{ $treatment->generic_name ?: 'N/A' }}</dd>

                                                <dt class="col-sm-4">Brand Name:</dt>
                                                <dd class="col-sm-8">{{ $treatment->brand_name ?: 'N/A' }}</dd>

                                                <dt class="col-sm-4">Dosage:</dt>
                                                <dd class="col-sm-8">{{ $treatment->dosage ?: 'N/A' }}</dd>

                                                <dt class="col-sm-4">Frequency:</dt>
                                                <dd class="col-sm-8">{{ $treatment->frequency ?: 'N/A' }}</dd>
                                            </dl>
                                        </div>
                                        <div class="col-md-6">
                                            <dl class="row">
                                                <dt class="col-sm-4">Route:</dt>
                                                <dd class="col-sm-8">{{ $treatment->route ? ucfirst($treatment->route) : 'N/A' }}</dd>

                                                <dt class="col-sm-4">Duration:</dt>
                                                <dd class="col-sm-8">{{ $treatment->duration_days ? $treatment->duration_days . ' days' : 'N/A' }}</dd>

                                                <dt class="col-sm-4">Quantity:</dt>
                                                <dd class="col-sm-8">{{ $treatment->quantity_prescribed ?: 'N/A' }}</dd>

                                                <dt class="col-sm-4">Priority:</dt>
                                                <dd class="col-sm-8">
                                                    @if($treatment->priority === 'stat')
                                                        <span class="badge text-bg-danger">STAT</span>
                                                    @elseif($treatment->priority === 'urgent')
                                                        <span class="badge text-bg-warning">Urgent</span>
                                                    @else
                                                        <span class="badge text-bg-info">{{ ucfirst($treatment->priority) }}</span>
                                                    @endif
                                                </dd>

                                                <dt class="col-sm-4">Status:</dt>
                                                <dd class="col-sm-8">
                                                    @if($treatment->status === 'active')
                                                        <span class="badge text-bg-success">Active</span>
                                                    @elseif($treatment->status === 'completed')
                                                        <span class="badge text-bg-primary">Completed</span>
                                                    @elseif($treatment->status === 'discontinued')
                                                        <span class="badge text-bg-secondary">Discontinued</span>
                                                    @else
                                                        <span class="badge text-bg-info">{{ ucfirst($treatment->status) }}</span>
                                                    @endif
                                                </dd>

                                                <dt class="col-sm-4">Prescribed Date:</dt>
                                                <dd class="col-sm-8">{{ $treatment->prescribed_date ? $treatment->prescribed_date->format('M d, Y') : 'N/A' }}</dd>
                                            </dl>
                                        </div>
                                    </div>

                                    @if($treatment->indication)
                                    <div class="mt-3">
                                        <h6>Indication:</h6>
                                        <p class="text-muted">{{ $treatment->indication }}</p>
                                    </div>
                                    @endif

                                    @if($treatment->special_instructions)
                                    <div class="mt-3">
                                        <h6>Special Instructions:</h6>
                                        <p class="text-muted">{{ $treatment->special_instructions }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user-md mr-2"></i>Prescriber Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($treatment->prescriber)
                                    <div class="text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-user-md fa-3x text-primary"></i>
                                        </div>
                                        <h6>{{ $treatment->prescriber->name }}</h6>
                                        <p class="text-muted">{{ $treatment->prescriber->email }}</p>
                                        @if($treatment->prescriber->employeeProfile && $treatment->prescriber->employeeProfile->phone)
                                        <p class="text-muted">{{ $treatment->prescriber->employeeProfile->phone }}</p>
                                        @endif
                                    </div>
                                    @else
                                    <p class="text-muted text-center">Prescriber information not available</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Prescription Details -->
                    @if($treatment->prescriptions->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-prescription-bottle mr-2"></i>Prescription Details
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Medicine</th>
                                                    <th>Quantity Prescribed</th>
                                                    <th>Quantity Dispensed</th>
                                                    <th>Dosage Instructions</th>
                                                    <th>Dispensed By</th>
                                                    <th>Dispensed Date</th>
                                                    <th>Status</th>
                                                    <th>Payment Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($treatment->prescriptions as $prescription)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $prescription->inventory->name }}</strong><br>
                                                        <small class="text-muted">{{ $prescription->inventory->description }}</small>
                                                    </td>
                                                    <td>{{ $prescription->quantity_prescribed }}</td>
                                                    <td>{{ $prescription->quantity_dispensed }}</td>
                                                    <td>{{ $prescription->dosage_instructions ?: 'N/A' }}</td>
                                                    <td>
                                                        @if($prescription->dispenser)
                                                            {{ $prescription->dispenser->name }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>{{ $prescription->dispensed_date ? $prescription->dispensed_date->format('M d, Y') : 'N/A' }}</td>
                                                    <td>
                                                        @if($prescription->status === 'fully_dispensed')
                                                            <span class="badge text-bg-success">Fully Dispensed</span>
                                                        @elseif($prescription->status === 'partially_dispensed')
                                                            <span class="badge text-bg-warning">Partially Dispensed</span>
                                                        @elseif($prescription->status === 'pending_payment')
                                                            <span class="badge text-bg-primary">Pending Payment</span>
                                                        @elseif($prescription->status === 'external_purchase')
                                                            <span class="badge text-bg-info">External Purchase</span>
                                                        @else
                                                            <span class="badge text-bg-secondary">{{ ucfirst(str_replace('_', ' ', $prescription->status)) }}</span>
                                                        @endif
                                                        @if($prescription->purchase_location)
                                                            <br><small class="text-muted">Purchase: {{ ucfirst($prescription->purchase_location) }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($prescription->payment)
                                                            @if($prescription->payment->isCompleted())
                                                                <span class="badge text-bg-success">Paid</span>
                                                            @elseif($prescription->payment->isPending())
                                                                <span class="badge text-bg-warning">Pending</span>
                                                            @elseif($prescription->payment->isAwaitingApproval())
                                                                <span class="badge text-bg-info">Awaiting Approval</span>
                                                            @else
                                                                <span class="badge text-bg-secondary">{{ ucfirst($prescription->payment->status) }}</span>
                                                            @endif
                                                        @else
                                                            <span class="badge text-bg-light">Not Paid</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('employee.patients.edit-treatment', [$patient->id, $treatment->id]) }}" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Edit Treatment
                                        </a>
                                        <button type="button" class="btn btn-success" onclick="printPrescription()">
                                            <i class="fas fa-print"></i> Print Prescription
                                        </button>
                                        {{-- <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#dispenseModal">
                                            <i class="fas fa-pills"></i> Dispense Medication
                                        </button> --}}
                                        <button type="button" class="btn btn-danger" onclick="deleteTreatment()">
                                            <i class="fas fa-trash"></i> Delete Treatment
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dispense Medication Modal -->
<div class="modal fade" id="dispenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dispense Medication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('employee.patients.dispense-medication', [$patient->id, $treatment->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Medicine *</label>
                        <select name="inventory_id" class="form-select" required>
                            <option value="">Select Medicine</option>
                            @foreach(\App\Models\Inventory::active()->get() as $medicine)
                                <option value="{{ $medicine->id }}" data-stock="{{ $medicine->current_quantity }}" data-price="{{ $medicine->selling_price }}">
                                    {{ $medicine->name }} (Stock: {{ $medicine->current_quantity }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity to Dispense *</label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes about the dispensing"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Dispense Medication</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function printPrescription() {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Prescription - {{ $patient->name }}</title>
            <style>
                body {
                    font-family: 'Courier New', monospace;
                    font-size: 12px;
                    line-height: 1.4;
                    margin: 20px;
                    max-width: 800px;
                }
                .header {
                    text-align: center;
                    border-bottom: 2px solid #000;
                    padding-bottom: 10px;
                    margin-bottom: 20px;
                }
                .clinic-name {
                    font-size: 18px;
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                .clinic-info {
                    font-size: 10px;
                    color: #666;
                }
                .prescription-title {
                    text-align: center;
                    font-size: 16px;
                    font-weight: bold;
                    margin: 20px 0;
                    text-decoration: underline;
                }
                .patient-info {
                    margin-bottom: 20px;
                }
                .info-row {
                    display: flex;
                    margin-bottom: 5px;
                }
                .info-label {
                    width: 120px;
                    font-weight: bold;
                }
                .medication-section {
                    margin: 20px 0;
                }
                .medication-item {
                    border: 1px solid #ccc;
                    padding: 10px;
                    margin-bottom: 10px;
                    background-color: #f9f9f9;
                }
                .medication-name {
                    font-weight: bold;
                    font-size: 14px;
                    margin-bottom: 5px;
                }
                .instructions {
                    margin: 10px 0;
                    font-style: italic;
                }
                .signature-section {
                    margin-top: 40px;
                    border-top: 1px solid #000;
                    padding-top: 20px;
                }
                .signature-line {
                    width: 200px;
                    border-bottom: 1px solid #000;
                    margin-top: 40px;
                }
                .footer {
                    margin-top: 30px;
                    font-size: 10px;
                    text-align: center;
                    color: #666;
                }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="clinic-name">FRYDT CLINIC</div>
                <div class="clinic-info">
                    Address: [Clinic Address]<br>
                    Phone: [Clinic Phone] | Email: [Clinic Email]<br>
                    License No: [License Number]
                </div>
            </div>

            <div class="prescription-title">PRESCRIPTION</div>

            <div class="patient-info">
                <div class="info-row">
                    <span class="info-label">Patient Name:</span>
                    <span>{{ $patient->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date of Birth:</span>
                    <span>{{ $patient->patientProfile->birth_date ? $patient->patientProfile->birth_date->format('M d, Y') : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Address:</span>
                    <span>{{ $patient->patientProfile->address ?: 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span>{{ $patient->patientProfile->phone ?: 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    <span>{{ $treatment->prescribed_date ? $treatment->prescribed_date->format('M d, Y') : now()->format('M d, Y') }}</span>
                </div>
            </div>

            <div class="medication-section">
                <h4>Medications Prescribed:</h4>

                @foreach($treatment->prescriptions as $prescription)
                <div class="medication-item">
                    <div class="medication-name">
                        {{ $prescription->inventory->name }}
                        @if($prescription->inventory->description)
                        ({{ $prescription->inventory->description }})
                        @endif
                    </div>
                    <div><strong>Quantity:</strong> {{ $prescription->quantity_prescribed }} {{ $prescription->inventory->unit_of_measure ?: 'units' }}</div>
                    @if($prescription->dosage_instructions)
                    <div><strong>Dosage Instructions:</strong> {{ $prescription->dosage_instructions }}</div>
                    @endif
                    @if($prescription->duration_days)
                    <div><strong>Duration:</strong> {{ $prescription->duration_days }} days</div>
                    @endif
                    @if($prescription->special_instructions)
                    <div class="instructions">{{ $prescription->special_instructions }}</div>
                    @endif
                </div>
                @endforeach
            </div>

            @if($treatment->indication)
            <div style="margin: 20px 0;">
                <strong>Indication:</strong> {{ $treatment->indication }}
            </div>
            @endif

            <div class="signature-section">
                <div style="display: flex; justify-content: space-between;">
                    <div>
                        <div class="signature-line"></div>
                        <div style="text-align: center; font-size: 10px;">
                            Physician's Signature
                        </div>
                    </div>
                    <div>
                        <div class="signature-line"></div>
                        <div style="text-align: center; font-size: 10px;">
                            Pharmacist's Signature
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer">
                <p>This prescription is valid for 30 days from the date issued.</p>
                <p>Please keep this prescription for your records.</p>
                <p>Printed on: {{ now()->format('M d, Y \a\t h:i A') }}</p>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function deleteTreatment() {
    if (confirm('Are you sure you want to delete this treatment? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("employee.patients.delete-treatment", [$patient->id, $treatment->id]) }}';
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
