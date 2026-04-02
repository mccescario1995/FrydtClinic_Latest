<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt - Invoice #{{ $billing->invoice_number }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            background: white;
            max-width: 8.5in;
            margin: 0 auto;
            padding: 0.5in;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #4ade80;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .clinic-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }

        .clinic-tagline {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 15px;
        }

        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            gap: 30px;
        }

        .info-section {
            flex: 1;
        }

        .info-section h4 {
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            padding: 2px 0;
        }

        .info-label {
            font-weight: 600;
            color: #555;
        }

        .info-value {
            color: #333;
        }

        .amount-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border: 1px solid #ddd;
        }

        .amount-table th,
        .amount-table td {
            padding: 10px 12px;
            text-align: right;
            border-bottom: 1px solid #eee;
        }

        .amount-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            font-size: 12px;
            text-transform: uppercase;
        }

        .amount-table td:first-child,
        .amount-table th:first-child {
            text-align: left;
        }

        .amount-table .label {
            font-weight: 500;
        }

        .amount-table .amount {
            font-weight: 600;
            color: #333;
        }

        .amount-table .total-row {
            background-color: #4ade80;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }

        .amount-table .total-row td {
            border: none;
            padding: 12px;
        }

        .amount-table .balance-row {
            background-color: #dc3545;
            color: white;
            font-weight: bold;
        }

        .amount-table .balance-row td {
            border: none;
            padding: 12px;
        }

        .services-section {
            margin: 25px 0;
        }

        .services-section h4 {
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 12px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e0e0e0;
        }

        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        .services-table th,
        .services-table td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }

        .services-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            font-size: 12px;
            text-transform: uppercase;
        }

        .insurance-section {
            display: flex;
            gap: 30px;
            margin: 25px 0;
        }

        .insurance-box {
            flex: 1;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }

        .insurance-box h4 {
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }

        .insurance-info {
            font-size: 13px;
        }

        .insurance-info .info-row {
            margin-bottom: 5px;
        }

        .payment-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }

        .status-partial {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-unpaid {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-overdue {
            background-color: #dc3545;
            color: white;
        }

        .notes-section {
            margin: 25px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #4ade80;
            border-radius: 3px;
        }

        .notes-section h4 {
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 8px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #666;
            font-size: 12px;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .print-button:hover {
            background: #0056b3;
        }

        @media print {
            .print-button {
                display: none !important;
            }

            body {
                padding: 0.5in;
                font-size: 12px;
            }

            .receipt-header {
                margin-bottom: 20px;
            }

            .clinic-name {
                font-size: 20px;
            }

            .receipt-title {
                font-size: 16px;
            }

            .amount-table,
            .services-table,
            .insurance-box {
                font-size: 11px;
            }

            .amount-table th,
            .amount-table td,
            .services-table th,
            .services-table td {
                padding: 6px 8px;
            }
        }

        @page {
            margin: 0.5in;
            size: A4;
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">
        <i class="fas fa-print"></i> Print Receipt
    </button>

    <div class="receipt-header">
        <div class="clinic-name">FRYDT CLINIC</div>
        {{-- <div class="clinic-tagline">Healthcare • Compassion • Excellence</div> --}}
        <div class="receipt-title">OFFICIAL RECEIPT</div>
    </div>

    <div class="receipt-info">
        <div class="info-section">
            <h4>Invoice Information</h4>
            <div class="info-row">
                <span class="info-label">Invoice Number:</span>
                <span class="info-value">{{ $billing->invoice_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Invoice Date:</span>
                <span class="info-value">{{ $billing->invoice_date->format('F j, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Due Date:</span>
                <span class="info-value">{{ $billing->due_date->format('F j, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Service Period:</span>
                <span class="info-value">
                    {{ $billing->service_start_date?->format('M j, Y') ?? 'N/A' }} -
                    {{ $billing->service_end_date?->format('M j, Y') ?? 'N/A' }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    @if($billing->payment_status === 'paid')
                        <span class="payment-status status-paid">Paid</span>
                    @elseif($billing->payment_status === 'overdue')
                        <span class="payment-status status-overdue">Overdue</span>
                    @elseif($billing->payment_status === 'partial')
                        <span class="payment-status status-partial">Partial</span>
                    @else
                        <span class="payment-status status-unpaid">{{ ucfirst($billing->payment_status) }}</span>
                    @endif
                </span>
            </div>
        </div>

        <div class="info-section">
            <h4>Patient Information</h4>
            <div class="info-row">
                <span class="info-label">Patient Name:</span>
                <span class="info-value">{{ $billing->patient->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Patient ID:</span>
                <span class="info-value">#{{ $billing->patient->id }}</span>
            </div>
            @if($billing->responsible_party_name)
            <div class="info-row">
                <span class="info-label">Responsible Party:</span>
                <span class="info-value">{{ $billing->responsible_party_name }}</span>
            </div>
            @endif
            @if($billing->philhealth_member)
            <div class="info-row">
                <span class="info-label">PhilHealth:</span>
                <span class="info-value">
                    <span class="payment-status status-paid">Member</span>
                    @if($billing->philhealth_number)
                        <br><small>No: {{ $billing->philhealth_number }}</small>
                    @endif
                </span>
            </div>
            @endif
        </div>
    </div>

    @if($billing->services_rendered && count($billing->services_rendered) > 0)
    <div class="services-section">
        <h4>Services Rendered</h4>
        <table class="services-table">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Provider</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($billing->services_rendered as $service)
                <tr>
                    <td>{{ $service['name'] ?? 'N/A' }}</td>
                    <td>{{ isset($service['date']) ? \Carbon\Carbon::parse($service['date'])->format('M j, Y') : 'N/A' }}</td>
                    <td>{{ $service['provider'] ?? 'N/A' }}</td>
                    <td>₱{{ number_format($service['amount'] ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="amount-table">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td class="label">Subtotal Amount</td>
                <td class="amount">₱{{ number_format($billing->subtotal_amount, 2) }}</td>
            </tr>
            @if($billing->discount_amount > 0)
            <tr>
                <td class="label">Discount</td>
                <td class="amount">-₱{{ number_format($billing->discount_amount, 2) }}</td>
            </tr>
            @endif
            @if($billing->philhealth_coverage > 0)
            <tr>
                <td class="label">PhilHealth Coverage</td>
                <td class="amount">-₱{{ number_format($billing->philhealth_coverage, 2) }}</td>
            </tr>
            @endif
            @if($billing->tax_amount > 0)
            <tr>
                <td class="label">Tax</td>
                <td class="amount">₱{{ number_format($billing->tax_amount, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>TOTAL AMOUNT</td>
                <td>₱{{ number_format($billing->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Amount Paid</td>
                <td class="amount">₱{{ number_format($billing->amount_paid, 2) }}</td>
            </tr>
            @if($billing->balance_due > 0)
            <tr class="balance-row">
                <td>BALANCE DUE</td>
                <td>₱{{ number_format($billing->balance_due, 2) }}</td>
            </tr>
            @endif
        </table>
    </div>

    @if($billing->has_insurance || $billing->philhealth_member)
    <div class="insurance-section">
        @if($billing->has_insurance)
        <div class="insurance-box">
            <h4>Insurance Information</h4>
            <div class="insurance-info">
                <div class="info-row">
                    <span class="info-label">Provider:</span>
                    <span class="info-value">{{ $billing->insurance_provider ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Policy Number:</span>
                    <span class="info-value">{{ $billing->insurance_policy_number ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Coverage Amount:</span>
                    <span class="info-value">₱{{ number_format($billing->insurance_coverage_amount ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
        @endif

        @if($billing->philhealth_member)
        <div class="insurance-box">
            <h4>PhilHealth Details</h4>
            <div class="insurance-info">
                <div class="info-row">
                    <span class="info-label">Member Number:</span>
                    <span class="info-value">{{ $billing->philhealth_number ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Benefit Amount:</span>
                    <span class="info-value">₱{{ number_format($billing->philhealth_benefit_amount ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    @if($billing->amount_paid > 0)
    <div class="info-section" style="margin: 25px 0;">
        <h4>Payment Information</h4>
        <div class="info-row">
            <span class="info-label">Payment Method:</span>
            <span class="info-value">{{ $billing->payment_method ?? 'N/A' }}</span>
        </div>
        @if($billing->payment_reference)
        <div class="info-row">
            <span class="info-label">Reference:</span>
            <span class="info-value">{{ $billing->payment_reference }}</span>
        </div>
        @endif
    </div>
    @endif

    @if($billing->billing_notes || $billing->collection_notes)
    <div class="notes-section">
        @if($billing->billing_notes)
        <h4>Billing Notes</h4>
        <p>{{ $billing->billing_notes }}</p>
        @endif
        @if($billing->collection_notes)
        <h4>Collection Notes</h4>
        <p>{{ $billing->collection_notes }}</p>
        @endif
    </div>
    @endif

    <div class="footer">
        <p><strong>Generated on:</strong> {{ date('F j, Y \a\t g:i A') }}</p>
        <p>Thank you for choosing FRYDT Clinic for your healthcare needs.</p>
        <p style="margin-top: 10px; font-size: 10px;">
            This is a computer-generated receipt. No signature required.
        </p>
    </div>
</body>
</html>
