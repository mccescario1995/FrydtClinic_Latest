<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report - {{ $startDate->format('M j, Y') }} to {{ $endDate->format('M j, Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .summary-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }
        .summary-number {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
        .summary-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            font-size: 11px;
        }
        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Financial Report</h1>
        <p>Report Period: {{ $startDate->format('F j, Y') }} to {{ $endDate->format('F j, Y') }}</p>
        <p>Generated on: {{ date('F j, Y \a\t g:i A') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="summary-number">{{ $billings->count() }}</div>
            <div class="summary-label">Total Bills</div>
        </div>
        <div class="summary-item">
            <div class="summary-number">₱{{ number_format($billings->sum('total_amount'), 2) }}</div>
            <div class="summary-label">Total Billed</div>
        </div>
        <div class="summary-item">
            <div class="summary-number">₱{{ number_format($billings->sum('amount_paid'), 2) }}</div>
            <div class="summary-label">Total Collected</div>
        </div>
        <div class="summary-item">
            <div class="summary-number">₱{{ number_format($billings->sum('balance_due'), 2) }}</div>
            <div class="summary-label">Outstanding Balance</div>
        </div>
    </div>

    <h2>Billing Details</h2>
    <table>
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Patient</th>
                <th>Invoice Date</th>
                <th>Due Date</th>
                <th>Service Start</th>
                <th>Service End</th>
                <th>Subtotal</th>
                <th>Discount</th>
                <th>PhilHealth</th>
                <th>Tax</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Balance</th>
                <th>Status</th>
                <th>Payment Method</th>
            </tr>
        </thead>
        <tbody>
            @foreach($billings as $billing)
            <tr>
                <td>{{ $billing->invoice_number ?? 'N/A' }}</td>
                <td>{{ $billing->patient->name ?? 'N/A' }}</td>
                <td>{{ $billing->invoice_date ? $billing->invoice_date->format('M j, Y') : 'N/A' }}</td>
                <td>{{ $billing->due_date ? $billing->due_date->format('M j, Y') : 'N/A' }}</td>
                <td>{{ $billing->service_start_date ? $billing->service_start_date->format('M j, Y') : 'N/A' }}</td>
                <td>{{ $billing->service_end_date ? $billing->service_end_date->format('M j, Y') : 'N/A' }}</td>
                <td>₱{{ number_format($billing->subtotal_amount ?? 0, 2) }}</td>
                <td>₱{{ number_format($billing->discount_amount ?? 0, 2) }}</td>
                <td>₱{{ number_format($billing->philhealth_coverage ?? 0, 2) }}</td>
                <td>₱{{ number_format($billing->tax_amount ?? 0, 2) }}</td>
                <td>₱{{ number_format($billing->total_amount ?? 0, 2) }}</td>
                <td>₱{{ number_format($billing->amount_paid ?? 0, 2) }}</td>
                <td>₱{{ number_format($billing->balance_due ?? 0, 2) }}</td>
                <td>{{ $billing->payment_status ?? 'N/A' }}</td>
                <td>{{ $billing->payment_method ?? 'N/A' }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6"><strong>TOTALS</strong></td>
                <td><strong>₱{{ number_format($billings->sum('subtotal_amount'), 2) }}</strong></td>
                <td><strong>₱{{ number_format($billings->sum('discount_amount'), 2) }}</strong></td>
                <td><strong>₱{{ number_format($billings->sum('philhealth_coverage'), 2) }}</strong></td>
                <td><strong>₱{{ number_format($billings->sum('tax_amount'), 2) }}</strong></td>
                <td><strong>₱{{ number_format($billings->sum('total_amount'), 2) }}</strong></td>
                <td><strong>₱{{ number_format($billings->sum('amount_paid'), 2) }}</strong></td>
                <td><strong>₱{{ number_format($billings->sum('balance_due'), 2) }}</strong></td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>This financial report was generated automatically by the Clinic Management System</p>
        <p>Confidential - For authorized financial personnel only</p>
    </div>
</body>
</html>
