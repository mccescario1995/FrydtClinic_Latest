<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Slip - {{ $payroll->employee->name }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #000;
            background: white;
        }
        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 15mm;
            border: 1px solid #000;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 14px;
            font-weight: normal;
        }
        .company-info {
            text-align: center;
            margin-bottom: 15px;
            font-size: 11px;
        }
        .employee-info {
            margin-bottom: 15px;
        }
        .employee-info table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .employee-info th, .employee-info td {
            padding: 4px 6px;
            text-align: left;
            border: 1px solid #000;
            font-size: 11px;
        }
        .employee-info th {
            background-color: #f0f0f0;
            font-weight: bold;
            width: 25%;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin: 15px 0 8px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }
        .earnings-deductions {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .earnings, .deductions {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }
        .earnings {
            padding-right: 4%;
        }
        .earnings table, .deductions table {
            width: 100%;
            border-collapse: collapse;
        }
        .earnings th, .earnings td,
        .deductions th, .deductions td {
            padding: 4px 6px;
            text-align: left;
            border: 1px solid #000;
            font-size: 11px;
        }
        .earnings th, .deductions th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .totals {
            text-align: center;
            margin: 15px 0;
            font-weight: bold;
            font-size: 14px;
            border: 2px solid #000;
            padding: 8px;
            background-color: #f9f9f9;
        }
        .payroll-info {
            margin-bottom: 15px;
        }
        .payroll-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .payroll-info th, .payroll-info td {
            padding: 4px 6px;
            text-align: left;
            border: 1px solid #000;
            font-size: 11px;
        }
        .payroll-info th {
            background-color: #f0f0f0;
            font-weight: bold;
            width: 30%;
        }
        .attendance-history {
            margin-bottom: 15px;
        }
        .attendance-history table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        .attendance-history th, .attendance-history td {
            padding: 3px 4px;
            text-align: left;
            border: 1px solid #000;
        }
        .attendance-history th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 10px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        @media print {
            body { margin: 0; background: white; }
            .container { border: none; padding: 10mm; }
        }
        @page {
            size: A4;
            margin: 15mm;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PAY SLIP</h1>
            <h2>FRYDT Lyingin Clinic Management System</h2>
        </div>

        <div class="company-info">
            <p><strong>Company:</strong> FRYDT Lyingin Clinic </p>
            <p><strong>Address:</strong> South Centro, Sipocot, Camarines Sur </p>
            <p><strong>Phone:</strong> 450-65-84 </p>
        </div>

        <div class="employee-info">
            <div class="section-title">Employee Information</div>
            <table>
                <tr>
                    <th>Name:</th>
                    <td>{{ $payroll->employee->name }}</td>
                    <th>Employee ID:</th>
                    <td>{{ $payroll->employee->id }}</td>
                </tr>
                <tr>
                    <th>Pay Period:</th>
                    <td>{{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('M d, Y') }}</td>
                    <th>Pay Date:</th>
                    <td>{{ $payroll->payment_date ? \Carbon\Carbon::parse($payroll->payment_date)->format('M d, Y') : 'Not Paid' }}</td>
                </tr>
            </table>
        </div>

        <div class="earnings-deductions">
            <div class="earnings">
                <div class="section-title">Earnings</div>
                <table>
                    <tr>
                        <th>Description</th>
                        <th>Hours</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </tr>
                    <tr>
                        <td>Regular Hours</td>
                        <td>{{ number_format($payroll->regular_hours, 2) }}</td>
                        <td>₱{{ number_format($payroll->hourly_rate, 2) }}</td>
                        <td>₱{{ number_format($payroll->regular_hours * $payroll->hourly_rate, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Overtime Hours</td>
                        <td>{{ number_format($payroll->overtime_hours, 2) }}</td>
                        <td>₱{{ number_format($payroll->overtime_rate, 2) }}</td>
                        <td>₱{{ number_format($payroll->overtime_hours * $payroll->overtime_rate, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3"><strong>Gross Pay</strong></td>
                        <td><strong>₱{{ number_format($payroll->gross_pay, 2) }}</strong></td>
                    </tr>
                </table>
            </div>

            <div class="deductions">
                <div class="section-title">Deductions</div>
                <table>
                    <tr>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                    @if($payroll->sss_deduction > 0)
                    <tr>
                        <td>SSS</td>
                        <td>₱{{ number_format($payroll->sss_deduction, 2) }}</td>
                    </tr>
                    @endif
                    @if($payroll->philhealth_deduction > 0)
                    <tr>
                        <td>PhilHealth</td>
                        <td>₱{{ number_format($payroll->philhealth_deduction, 2) }}</td>
                    </tr>
                    @endif
                    @if($payroll->pagibig_deduction > 0)
                    <tr>
                        <td>Pag-IBIG</td>
                        <td>₱{{ number_format($payroll->pagibig_deduction, 2) }}</td>
                    </tr>
                    @endif
                    @if($payroll->tax_deduction > 0)
                    <tr>
                        <td>Withholding Tax</td>
                        <td>₱{{ number_format($payroll->tax_deduction, 2) }}</td>
                    </tr>
                    @endif
                    @if($payroll->other_deductions > 0)
                    <tr>
                        <td>Other Deductions</td>
                        <td>₱{{ number_format($payroll->other_deductions, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Total Deductions</strong></td>
                        <td><strong>₱{{ number_format($payroll->deductions, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="totals">
            <strong>NET PAY: ₱{{ number_format($payroll->net_pay, 2) }}</strong>
        </div>

        <div class="payroll-info">
            <div class="section-title">Payroll Summary</div>
            <table>
                <tr>
                    <th>Total Hours Worked:</th>
                    <td>{{ number_format($payroll->total_hours_worked, 2) }}</td>
                    <th>Status:</th>
                    <td>{{ ucfirst($payroll->status) }}</td>
                </tr>
            </table>
        </div>

        <div class="attendance-history">
            <div class="section-title">Attendance History</div>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Check-in Time</th>
                    <th>Check-out Time</th>
                    <th>Hours Worked</th>
                    <th>Status</th>
                </tr>
                @forelse($attendanceRecords as $attendance)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}</td>
                    <td>{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}</td>
                    <td>{{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '-' }}</td>
                    <td>
                        @if($attendance->check_in_time && $attendance->check_out_time)
                            {{ number_format(\Carbon\Carbon::parse($attendance->check_in_time)->diffInHours(\Carbon\Carbon::parse($attendance->check_out_time)), 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ ucfirst($attendance->status) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; font-style: italic;">No attendance records found for this pay period.</td>
                </tr>
                @endforelse
            </table>
        </div>

        <div class="footer">
            <p>This is a computer-generated pay slip. Please keep it for your records.</p>
            <p>Generated on: {{ now()->format('M d, Y H:i:s') }}</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
