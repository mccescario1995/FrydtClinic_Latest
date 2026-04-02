<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laboratory Report - {{ $startDate->format('M j, Y') }} to {{ $endDate->format('M j, Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
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
        .stats {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .stat-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .stat-number {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 9px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 8px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 9px;
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
        <h1>Laboratory Report</h1>
        <p>Report Period: {{ $startDate->format('F j, Y') }} to {{ $endDate->format('F j, Y') }}</p>
        <p>Generated on: {{ date('F j, Y \a\t g:i A') }}</p>
    </div>

    <div class="stats">
        <div class="stat-item">
            <div class="stat-number">{{ $labResults->count() }}</div>
            <div class="stat-label">Total Tests</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $labResults->where('result_status', 'normal')->count() }}</div>
            <div class="stat-label">Normal Results</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $labResults->where('result_status', 'abnormal')->count() }}</div>
            <div class="stat-label">Abnormal Results</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $labResults->where('result_status', 'critical')->count() }}</div>
            <div class="stat-label">Critical Results</div>
        </div>
    </div>

    <h2>Laboratory Test Results</h2>
    <table>
        <thead>
            <tr>
                <th>Patient</th>
                <th>Test Name</th>
                <th>Test Code</th>
                <th>Category</th>
                <th>Sample Type</th>
                <th>Urgent</th>
                <th>Ordered Date</th>
                <th>Performed Date</th>
                <th>Result Date</th>
                <th>Result Value</th>
                <th>Unit</th>
                <th>Reference Range</th>
                <th>Status</th>
                <th>Ordering Provider</th>
                <th>Performing Tech</th>
            </tr>
        </thead>
        <tbody>
            @foreach($labResults as $result)
            <tr>
                <td>{{ $result->patient->name ?? 'N/A' }}</td>
                <td>{{ $result->test_name ?? 'N/A' }}</td>
                <td>{{ $result->test_code ?? 'N/A' }}</td>
                <td>{{ $result->test_category ?? 'N/A' }}</td>
                <td>{{ $result->sample_type ?? 'N/A' }}</td>
                <td>{{ $result->urgent ? 'Yes' : 'No' }}</td>
                <td>{{ $result->test_ordered_date_time ? $result->test_ordered_date_time->format('M j, Y H:i') : 'N/A' }}</td>
                <td>{{ $result->test_performed_date_time ? $result->test_performed_date_time->format('M j, Y H:i') : 'N/A' }}</td>
                <td>{{ $result->result_available_date_time ? $result->result_available_date_time->format('M j, Y H:i') : 'N/A' }}</td>
                <td>{{ $result->result_value ?? 'N/A' }}</td>
                <td>{{ $result->result_unit ?? 'N/A' }}</td>
                <td>{{ $result->reference_range ?? 'N/A' }}</td>
                <td>{{ $result->result_status ?? 'N/A' }}</td>
                <td>{{ $result->orderingProvider->name ?? 'N/A' }}</td>
                <td>{{ $result->performingTechnician->name ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This laboratory report was generated automatically by the Clinic Management System</p>
        <p>Confidential - For authorized medical and laboratory personnel only</p>
    </div>
</body>
</html>
