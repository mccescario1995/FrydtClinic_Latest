<?php

namespace App\Http\Controllers\Admin;

use App\Models\Appointment;
use App\Models\Billing;
use App\Models\LaboratoryResult;
use App\Models\PatientProfile;
use App\Models\PrenatalRecord;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
// use PDF; // Temporarily disabled - requires PDF package
// use Excel; // Temporarily disabled - requires Laravel Excel package

class ReportController extends \Backpack\CRUD\app\Http\Controllers\AdminController
{
    public function patientReports()
    {
        $this->data['title'] = 'Patient Reports';

        // Patient demographics
        $this->data['total_patients'] = PatientProfile::count();
        $this->data['patients_by_gender'] = PatientProfile::selectRaw('gender, COUNT(*) as count')
            ->groupBy('gender')
            ->get();

        $this->data['patients_by_age_group'] = PatientProfile::selectRaw("
            CASE
                WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 18 THEN 'Under 18'
                WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 35 THEN '18-35'
                WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 36 AND 55 THEN '36-55'
                ELSE 'Over 55'
            END as age_group,
            COUNT(*) as count
        ")
        ->whereNotNull('birth_date')
        ->groupBy('age_group')
        ->get();

        return view('backpack.reports.patient', $this->data);
    }

    public function financialReports()
    {
        $this->data['title'] = 'Financial Reports';

        // Monthly revenue for the last 12 months
        $this->data['monthly_revenue'] = Billing::selectRaw('
            YEAR(invoice_date) as year,
            MONTH(invoice_date) as month,
            SUM(total_amount) as revenue,
            SUM(amount_paid) as collected,
            SUM(balance_due) as outstanding
        ')
        ->where('invoice_date', '>=', Carbon::now()->subMonths(12))
        ->where('payment_status', 'paid')
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

        // Payment methods breakdown
        $this->data['payment_methods'] = Billing::selectRaw('payment_method, COUNT(*) as count, SUM(amount_paid) as total')
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->get();

        // Outstanding payments
        $this->data['outstanding_payments'] = Billing::where('payment_status', '!=', 'paid')
            ->with('patient')
            ->orderBy('due_date')
            ->get();

        // Pending payments (unpaid but not overdue)
        $this->data['pending_payments'] = Billing::where('payment_status', 'unpaid')
            ->where('due_date', '>', Carbon::now())
            ->with('patient')
            ->get();

        // Overdue payments
        $this->data['overdue_payments'] = Billing::where('payment_status', 'unpaid')
            ->where('due_date', '<=', Carbon::now())
            ->with('patient')
            ->get();

        return view('backpack.reports.financial', $this->data);
    }

    public function laboratoryReports()
    {
        $this->data['title'] = 'Laboratory Reports';

        // Test volume by category
        $this->data['test_volume_by_category'] = LaboratoryResult::selectRaw('test_category, COUNT(*) as count')
            ->where('test_performed_date_time', '>=', Carbon::now()->subMonths(6))
            ->groupBy('test_category')
            ->get();

        // Turnaround time analysis
        $this->data['turnaround_times'] = LaboratoryResult::selectRaw('
            test_category,
            AVG(TIMESTAMPDIFF(HOUR, test_ordered_date_time, result_available_date_time)) as avg_turnaround_hours
        ')
        ->whereNotNull('result_available_date_time')
        ->where('test_performed_date_time', '>=', Carbon::now()->subMonths(3))
        ->groupBy('test_category')
        ->get();

        // Result status distribution
        $this->data['result_distribution'] = LaboratoryResult::selectRaw('result_status, COUNT(*) as count')
            ->where('test_performed_date_time', '>=', Carbon::now()->subMonths(6))
            ->groupBy('result_status')
            ->get();

        return view('backpack.reports.laboratory', $this->data);
    }

    public function exportPatientReport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $patients = PatientProfile::with('user')->get();

        if ($format === 'excel') {
            // Excel export temporarily disabled - requires Laravel Excel package
            return response()->json(['error' => 'Excel export not available. Please use PDF format.'], 400);
        }

        // PDF export temporarily disabled - requires PDF package
        return response()->json([
            'message' => 'PDF export temporarily unavailable. Here is the data:',
            'data' => $patients->toArray(),
            'total_patients' => $patients->count()
        ]);
    }

    public function exportFinancialReport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $billings = Billing::with('patient')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->get();

        if ($format === 'excel') {
            // Excel export temporarily disabled - requires Laravel Excel package
            return response()->json(['error' => 'Excel export not available. Please use PDF format.'], 400);
        }

        // PDF export temporarily disabled - requires PDF package
        return response()->json([
            'message' => 'PDF export temporarily unavailable. Here is the data:',
            'data' => $billings->toArray(),
            'total_records' => $billings->count(),
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate
            ]
        ]);
    }

    public function exportLaboratoryReport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $startDate = $request->get('start_date', Carbon::now()->subMonths(1));
        $endDate = $request->get('end_date', Carbon::now());

        $labResults = LaboratoryResult::with('patient')
            ->whereBetween('test_performed_date_time', [$startDate, $endDate])
            ->get();

        if ($format === 'excel') {
            // Excel export temporarily disabled - requires Laravel Excel package
            return response()->json(['error' => 'Excel export not available. Please use PDF format.'], 400);
        }

        // PDF export temporarily disabled - requires PDF package
        return response()->json([
            'message' => 'PDF export temporarily unavailable. Here is the data:',
            'data' => $labResults->toArray(),
            'total_records' => $labResults->count(),
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate
            ]
        ]);
    }
}
