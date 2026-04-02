    /**
     * Generate payroll for employees
     * Updated to use dynamic deduction system
     */
    public function generatePayroll(Request $request)
    {
        $request->validate([
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after:pay_period_start',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:users,id',
            'overtime_rate' => 'required|numeric|min:0',
        ]);

        $payPeriodStart = Carbon::parse($request->pay_period_start);
        $payPeriodEnd = Carbon::parse($request->pay_period_end);

        $generatedPayrolls = [];
        $skippedEmployees = [];

        foreach ($request->employee_ids as $employeeId) {
            // Check if payroll already exists for this period
            $existingPayroll = Payroll::where('employee_id', $employeeId)
                ->where('pay_period_start', $payPeriodStart)
                ->where('pay_period_end', $payPeriodEnd)
                ->first();

            if ($existingPayroll) {
                $skippedEmployees[] = ['id' => $employeeId, 'reason' => 'Payroll already exists for this period'];
                continue; // Skip if already exists
            }

            // Get hourly rate from employee profile
            $employeeProfile = \App\Models\EmployeeProfile::where('employee_id', $employeeId)->first();
            $hourlyRate = $employeeProfile ? $employeeProfile->hourly_rate : 0;

            if ($hourlyRate <= 0) {
                $employee = User::find($employeeId);
                $employeeName = $employee ? $employee->name : 'Unknown Employee';
                $skippedEmployees[] = ['id' => $employeeId, 'name' => $employeeName, 'reason' => 'No hourly rate set'];
                continue; // Skip if no hourly rate set
            }

            // Check if employee has deduction settings configured
            $employeeDeductions = \App\Models\EmployeeDeduction::where('employee_id', $employeeId)->enabled()->count();
            if ($employeeDeductions == 0) {
                $employee = User::find($employeeId);
                $employeeName = $employee ? $employee->name : 'Unknown Employee';
                $skippedEmployees[] = ['id' => $employeeId, 'name' => $employeeName, 'reason' => 'No deduction settings configured'];
                continue; // Skip if no deduction settings
            }

            $payroll = new Payroll([
                'employee_id' => $employeeId,
                'pay_period_start' => $payPeriodStart,
                'pay_period_end' => $payPeriodEnd,
                'hourly_rate' => $hourlyRate,
                'overtime_rate' => $request->overtime_rate,
            ]);

            $payroll->calculatePay();
            $generatedPayrolls[] = $payroll;

            ActivityLogger::log('payroll_generated', "Payroll generated for {$payroll->employee->name}", $payroll->employee);
        }

        $message = 'Payroll generated for ' . count($generatedPayrolls) . ' employees.';

        if (!empty($skippedEmployees)) {
            $message .= ' Skipped ' . count($skippedEmployees) . ' employees: ';
            $skipReasons = [];
            foreach ($skippedEmployees as $skipped) {
                $name = $skipped['name'] ?? 'Employee #' . $skipped['id'];
                $skipReasons[] = $name . ' (' . $skipped['reason'] . ')';
            }
            $message .= implode(', ', $skipReasons);
        }

        return redirect()->route('admin-portal.payroll')->with('success', $message);
    }
