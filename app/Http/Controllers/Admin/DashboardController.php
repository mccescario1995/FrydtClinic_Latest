<?php
namespace App\Http\Controllers\Admin;

use App\Models\Appointment;
use App\Models\Billing;
use App\Models\LaboratoryResult;
use App\Models\PatientProfile;
use App\Models\PrenatalRecord;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends \Backpack\CRUD\app\Http\Controllers\AdminController
{
    public function index()
    {
        $this->data['title'] = 'Clinic Dashboard';

        // Today's Statistics
        $this->data['today_appointments']   = Appointment::whereDate('appointment_datetime', Carbon::today())->count();
        $this->data['pending_appointments'] = Appointment::where('appointment_datetime', '>=', Carbon::now())
            ->where('status', 'scheduled')
            ->count();

        // Patient Statistics
        $this->data['total_patients']  = PatientProfile::count();
        $this->data['active_patients'] = PatientProfile::whereHas('user', function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subMonths(6));
        })->count();

        // Prenatal Statistics
        $this->data['active_prenatal'] = PrenatalRecord::where('next_visit_date', '>=', Carbon::now())
            ->distinct('patient_id')
            ->count('patient_id');

        // Laboratory Statistics
        $this->data['pending_lab_results']   = LaboratoryResult::where('result_status', 'pending')->count();
        $this->data['completed_lab_results'] = LaboratoryResult::where('result_status', '!=', 'pending')
            ->where('test_performed_date_time', '>=', Carbon::now()->subDays(7))
            ->count();

        // Financial Statistics
        $this->data['total_revenue'] = Billing::where('payment_status', 'paid')
            ->where('invoice_date', '>=', Carbon::now()->startOfMonth())
            ->sum('total_amount');

        $this->data['pending_payments'] = Billing::where('payment_status', '!=', 'paid')
            ->where('due_date', '>=', Carbon::now())
            ->sum('balance_due');

        $this->data['overdue_payments'] = Billing::where('payment_status', '!=', 'paid')
            ->where('due_date', '<', Carbon::now())
            ->sum('balance_due');

        // Recent Activities
        $this->data['recent_appointments'] = Appointment::with(['patient', 'employee'])
            ->orderBy('appointment_datetime', 'desc')
            ->limit(5)
            ->get();

        $this->data['recent_lab_results'] = LaboratoryResult::with(['patient'])
            ->where('result_available_date_time', '>=', Carbon::now()->subDays(7))
            ->orderBy('result_available_date_time', 'desc')
            ->limit(5)
            ->get();

        // Monthly Trends (Last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date          = Carbon::now()->subMonths($i);
            $monthlyData[] = [
                'month'        => $date->format('M Y'),
                'patients'     => PatientProfile::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'appointments' => Appointment::whereMonth('appointment_datetime', $date->month)
                    ->whereYear('appointment_datetime', $date->year)
                    ->count(),
                'revenue'      => Billing::where('payment_status', 'paid')
                    ->whereMonth('invoice_date', $date->month)
                    ->whereYear('invoice_date', $date->year)
                    ->sum('total_amount'),
            ];
        }
        $this->data['monthly_trends'] = $monthlyData;

        // Staff Performance
        $this->data['staff_performance'] = User::role(['Doctor', 'Employee'])
            ->withCount(['employeeAppointments' => function ($query) {
                $query->where('appointment_datetime', '>=', Carbon::now()->startOfMonth());
            }])
            ->orderBy('employee_appointments_count', 'desc')
            ->limit(5)
            ->get();

        return view('backpack.dashboard', $this->data);
    }
}
