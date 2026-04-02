@extends('patient.layouts.app')

@section('title', 'Prescription Details - ' . $prescription->prescription_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-prescription-bottle me-2"></i>
                        Prescription #{{ $prescription->prescription_number }}
                    </h5>
                    <div class="card-tools">
                        <button onclick="printPrescription()" class="btn btn-primary btn-sm no-print">
                            <i class="fas fa-print"></i> Print Prescription
                        </button>
                        <a href="{{ route('patient.medical-records') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Records
                        </a>
                    </div>
                </div>

                <div class="card-body prescription-content">
                    <!-- Prescription Header -->
                    <div class="text-center mb-4">
                        <h3 class="text-primary mb-1">FRYDT CLINIC</h3>
                        <p class="mb-1">Medical Prescription</p>
                        <p class="text-muted small">Valid for 30 days from date issued</p>
                    </div>

                    <!-- Patient Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Patient Information</h6>
                            <p class="mb-1"><strong>Name:</strong> {{ $prescription->treatment->patient->name }}</p>
                            <p class="mb-1"><strong>Date of Birth:</strong>
                                @if($prescription->treatment->patient->patientProfile && $prescription->treatment->patient->patientProfile->birth_date)
                                    {{ $prescription->treatment->patient->patientProfile->birth_date->format('M d, Y') }}
                                @else
                                    N/A
                                @endif
                            </p>
                            <p class="mb-1"><strong>Address:</strong> {{ $prescription->treatment->patient->patientProfile->address ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>Phone:</strong> {{ $prescription->treatment->patient->patientProfile->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Prescription Details</h6>
                            <p class="mb-1"><strong>Prescription #:</strong> {{ $prescription->prescription_number }}</p>
                            <p class="mb-1"><strong>Prescribed By:</strong> {{ $prescription->prescriber->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Purchase Location:</strong>
                                <span class="badge bg-{{ $prescription->purchase_location === 'clinic' ? 'success' : 'info' }}">
                                    {{ ucfirst($prescription->purchase_location ?? 'N/A') }}
                                </span>
                            </p>
                            @if($prescription->purchase_location === 'clinic' && $prescription->payment && $prescription->payment->isCompleted())
                                <p class="mb-0"><strong>Payment Status:</strong> <span class="text-success">Paid</span></p>
                            @endif
                        </div>
                    </div>

                    <!-- Medication Details -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Medication</h6>
                        <div class="border rounded p-3">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="mb-2">{{ $prescription->inventory->name ?? 'N/A' }}</h5>
                                    @if($prescription->inventory->description)
                                        <p class="text-muted mb-2">{{ $prescription->inventory->description }}</p>
                                    @endif
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <p class="mb-1"><strong>Quantity:</strong> {{ $prescription->quantity_prescribed }} {{ $prescription->inventory->unit_of_measure ?? 'units' }}</p>
                                    @if($prescription->purchase_location === 'clinic')
                                        <p class="mb-1"><strong>Unit Price:</strong> ₱{{ number_format($prescription->unit_price ?? 0, 2) }}</p>
                                        <p class="mb-0"><strong>Total:</strong> ₱{{ number_format($prescription->total_price ?? 0, 2) }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dosage Instructions -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Dosage Instructions</h6>
                        <div class="border rounded p-3">
                            @if($prescription->dosage_instructions)
                                <p class="mb-0">{{ $prescription->dosage_instructions }}</p>
                            @else
                                <p class="text-muted mb-0">No specific dosage instructions provided.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Special Instructions -->
                    @if($prescription->special_instructions)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Special Instructions</h6>
                        <div class="border rounded p-3">
                            <p class="mb-0">{{ $prescription->special_instructions }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Duration -->
                    @if($prescription->duration_days)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Duration</h6>
                        <div class="border rounded p-3">
                            <p class="mb-0">{{ $prescription->duration_days }} days</p>
                        </div>
                    </div>
                    @endif

                    <!-- Indication -->
                    @if($prescription->treatment->indication)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Indication</h6>
                        <div class="border rounded p-3">
                            <p class="mb-0">{{ $prescription->treatment->indication }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Footer -->
                    <div class="text-center mt-5 pt-3 border-top">
                        <p class="text-muted small mb-1">This prescription is issued electronically and is valid for 30 days from the date prescribed.</p>
                        <p class="text-muted small mb-1">Please keep this prescription for your records.</p>
                        <p class="text-muted small mb-0">Generated on: {{ now()->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
            </div>
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
            <title>Prescription - {{ $prescription->treatment->patient->name }}</title>
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
                    <span>{{ $prescription->treatment->patient->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date of Birth:</span>
                    <span>{{ $prescription->treatment->patient->patientProfile->birth_date ? $prescription->treatment->patient->patientProfile->birth_date->format('M d, Y') : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Address:</span>
                    <span>{{ $prescription->treatment->patient->patientProfile->address ?: 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span>{{ $prescription->treatment->patient->patientProfile->phone ?: 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    <span>{{ $prescription->prescribed_date ? $prescription->prescribed_date->format('M d, Y') : now()->format('M d, Y') }}</span>
                </div>
            </div>

            <div class="medication-section">
                <h4>Medications Prescribed:</h4>

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
            </div>

            @if($prescription->treatment->indication)
            <div style="margin: 20px 0;">
                <strong>Indication:</strong> {{ $prescription->treatment->indication }}
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
</script>
@endsection

@push('styles')
<style>
.prescription-content {
    font-family: 'Times New Roman', serif;
    line-height: 1.6;
}

@media print {
    .no-print {
        display: none !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    .card-header {
        border-bottom: 2px solid #000 !important;
        background-color: #f8f9fa !important;
    }

    .prescription-content {
        font-size: 12pt;
        max-width: none;
    }

    body {
        background-color: white !important;
    }

    .container-fluid {
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .row {
        margin: 0 !important;
    }

    .col-12 {
        padding: 0 !important;
    }

    .card-body {
        padding: 20px !important;
    }

    /* Ensure badges print properly */
    .badge {
        border: 1px solid #000 !important;
        color: #000 !important;
        background-color: transparent !important;
    }
}
</style>
@endpush
