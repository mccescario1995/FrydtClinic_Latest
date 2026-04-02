<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Patient Report - {{ date('Y-m-d') }}</title>
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
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
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
        <h1>Patient Report</h1>
        <p>Generated on: {{ date('F j, Y \a\t g:i A') }}</p>
        <p>Report Period: All Time</p>
    </div>

    <div class="stats">
        <div class="stat-item">
            <div class="stat-number">{{ $patients->count() }}</div>
            <div class="stat-label">Total Patients</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $patients->where('gender', 'Female')->count() }}</div>
            <div class="stat-label">Female Patients</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $patients->where('gender', 'Male')->count() }}</div>
            <div class="stat-label">Male Patients</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $patients->whereNotNull('philhealth_membership')->where('philhealth_membership', 'member')->count() }}</div>
            <div class="stat-label">PhilHealth Members</div>
        </div>
    </div>

    <h2>Patient Details</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Gender</th>
                <th>Age</th>
                <th>PhilHealth</th>
                <th>Registration Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $patient)
            <tr>
                <td>{{ $patient->id }}</td>
                <td>{{ $patient->name ?? 'N/A' }}</td>
                <td>{{ $patient->email ?? 'N/A' }}</td>
                <td>{{ $patient->phone ?? 'N/A' }}</td>
                <td>{{ $patient->gender ?? 'N/A' }}</td>
                <td>
                    @if($patient->birth_date)
                        {{ $patient->birth_date->age }}
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ $patient->philhealth_membership === 'member' ? 'Yes' : 'No' }}</td>
                <td>{{ $patient->created_at ? $patient->created_at->format('M j, Y') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated automatically by the Clinic Management System</p>
        <p>Confidential - For authorized personnel only</p>
    </div>
</body>
</html>
