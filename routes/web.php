<?php

use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServicesController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;

// PayPal callback routes (must be at the top - no middleware)
Route::get('/patient/payments/success', [PaymentController::class, 'success'])->name('patient.payment.success');
Route::get('/patient/payments/cancel', [PaymentController::class, 'cancel'])->name('patient.payment.cancel');

// OTP Verification routes (before auth middleware)
Route::middleware(['auth:backpack'])->group(function () {
    Route::get('/verify-otp', [App\Http\Controllers\PinVerificationController::class, 'show'])->name('verify-otp');
    Route::post('/verify-otp', [App\Http\Controllers\PinVerificationController::class, 'verify'])->name('verify-otp.post');
    Route::post('/resend-otp', [App\Http\Controllers\PinVerificationController::class, 'resendOtp'])->name('resend-otp');
    Route::get('/set-phone', [App\Http\Controllers\PinVerificationController::class, 'showSetPhone'])->name('set-phone');
    Route::post('/set-phone', [App\Http\Controllers\PinVerificationController::class, 'setPhone'])->name('set-phone.post');
});

// Public routes
Route::get('/homepage', function () {
    return view('pages.landing');
})->name('homepage');

Route::get('/terms', function () {
    return view('pages.terms');
})->name('pages.terms');

Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('pages.privacy');

// Services routes (public)
Route::prefix('services')->group(function () {
    Route::get('/', [ServicesController::class, 'index'])->name('services.index');
    Route::get('/{id}', [ServicesController::class, 'show'])->name('services.show')->where('id', '[0-9]+');
    Route::get('/type/{type}', [ServicesController::class, 'getByType'])->name('services.by-type')->where('type', 'single|package');
    Route::get('/api/data', [ServicesController::class, 'getServices'])->name('services.api.data');
});

// Terms and Privacy routes (public)
Route::get('/terms-and-conditions', function () {
    return view('pages.terms');
})->name('terms');

Route::get('/privacy-policy', function () {
    return view('pages.privacy');
})->name('privacy');

// Redirect /login to admin login
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

// Override Backpack registration to use custom controller
Route::get('/admin/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('backpack.auth.register');
Route::post('/admin/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Custom logout route
Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Root route - redirect based on authentication and user type
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin-portal.dashboard');
        }
        if ($user->isEmployee()) {
            return redirect()->route('employee.dashboard');
        }
        if ($user->isPatient() || $user->hasRole('Patient')) {
            return redirect()->route('patient.dashboard');
        }
        // Default fallback to Backpack admin for any other authenticated users
        return redirect('/admin/dashboard');
    }
    return redirect('/homepage');
})->name('home');

// Patient routes (authenticated patients only)
Route::middleware(['auth:backpack'])->group(function () {
    Route::middleware([RoleMiddleware::class . ':Patient'])
        ->prefix('patient')
        ->name('patient.')
        ->controller(PatientDashboardController::class)
        ->group(function () {

            // Account created confirmation
            Route::get('/account-created', function () {
                return view('auth.account-created');
            })->name('account.created');

            // Dashboard
            Route::get('/dashboard', 'index')->name('dashboard');

            // Appointments
            Route::get('/appointments', 'appointments')->name('appointments');
            Route::get('/book-appointment', 'bookAppointment')->name('book-appointment');
            Route::post('/book-appointment', 'storeAppointment')->name('store-appointment');
            Route::get('/appointments/{appointmentId}/edit', 'editAppointment')->name('appointments.edit');
            Route::put('/appointments/{appointmentId}', 'updateAppointment')->name('appointments.update');
            Route::delete('/appointments/{appointmentId}/cancel', 'cancelAppointment')->name('appointments.cancel');

            // AJAX routes for appointment booking
            Route::get('/api/available-employees', 'getAvailableEmployees')->name('api.available-employees');
            Route::get('/api/available-time-slots', action: 'getAvailableTimeSlots')->name('api.available-time-slots');

            // Route for main booking form availability checking
            Route::post('/api/appointment-get-available-times', [App\Http\Controllers\AppointmentBookingController::class, 'getAvailableTimes'])->name('api.appointment-get-available-times');

            // Profile
            Route::get('/profile', 'profile')->name('profile');
            Route::put('/profile', 'updateProfile')->name('update-profile');

            // Payments
            Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
            Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
            Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
            Route::get('/payments/{paymentId}', [PaymentController::class, 'show'])->name('payments.show');
            Route::post('/payments/{paymentId}/upload-proof', [PaymentController::class, 'uploadProofOfPayment'])->name('payments.upload-proof');

            // Prescription Payments
            Route::get('/prescriptions/{prescriptionId}/pay', [PaymentController::class, 'create'])->name('prescriptions.pay');
            Route::post('/prescriptions/{prescriptionId}/choose-location', 'choosePurchaseLocation')->name('prescriptions.choose-location');
            Route::get('/prescriptions/{prescriptionId}/view', 'viewPrescription')->name('prescriptions.view');

            // Medical Records
            Route::get('/medical-records', 'medicalRecords')->name('medical-records');
            Route::get('/laboratory-results', 'laboratoryResults')->name('laboratory-results');
            Route::get('/laboratory-results/{resultId}', 'viewLabResult')->name('lab-result-detail');

            // Billing
            Route::get('/billing', 'billing')->name('billing');
            Route::get('/billing/{billingId}', 'viewBill')->name('billing-detail');
            Route::get('/billing/{billingId}/print', 'printReceipt')->name('billing-print');

            // Documents
            Route::get('/documents', 'documents')->name('documents');
            Route::get('/documents/download/{documentId}', 'downloadDocument')->name('download-document');

            // PayPal callback routes (moved outside auth middleware)
        });
});

// Attendance routes (for employees)
Route::middleware(['auth:backpack', 'role:Employee|Doctor|Admin'])->group(function () {
    Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/process', [App\Http\Controllers\AttendanceController::class, 'process'])->name('attendance.process');
});

// Admin routes (custom admin portal)
Route::middleware(['auth:backpack', 'admin-portal'])->prefix('admin-portal')->name('admin-portal.')->group(function () {
    Route::get('/', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');

    // Account created confirmation
    Route::get('/account-created/{user}', function ($userId) {
        $user = \App\Models\User::findOrFail($userId);
        return view('admin-portal.account-created', compact('user'));
    })->name('account.created');

    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [App\Http\Controllers\AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [App\Http\Controllers\AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}', [App\Http\Controllers\AdminController::class, 'showUser'])->name('users.show');
    Route::get('/users/{user}/edit', [App\Http\Controllers\AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [App\Http\Controllers\AdminController::class, 'updateUser'])->name('users.update');
    Route::get('/patients', [App\Http\Controllers\AdminController::class, 'patients'])->name('patients');
    Route::get('/patients/create', [App\Http\Controllers\AdminController::class, 'createPatient'])->name('patients.create');
    Route::post('/patients', [App\Http\Controllers\AdminController::class, 'storePatient'])->name('patients.store');
    Route::get('/patients/{patientId}', [App\Http\Controllers\AdminController::class, 'showPatient'])->name('patients.show');
    Route::get('/patients/{patientId}/edit', [App\Http\Controllers\AdminController::class, 'editPatient'])->name('patients.edit');
    Route::put('/patients/{patientId}', [App\Http\Controllers\AdminController::class, 'updatePatient'])->name('patients.update');
    Route::delete('/patients/{patientId}', [App\Http\Controllers\AdminController::class, 'deletePatient'])->name('patients.delete');
    Route::get('/patients/{patientId}/philhealth/edit', [App\Http\Controllers\AdminController::class, 'editPatientPhilHealth'])->name('patients.philhealth.edit');
    Route::put('/patients/{patientId}/philhealth', [App\Http\Controllers\AdminController::class, 'updatePatientPhilHealth'])->name('patients.philhealth.update');

    // Medical Records Management for Admins
    Route::get('/medical-records/prenatal', [App\Http\Controllers\AdminController::class, 'adminPrenatalRecords'])->name('medical-records.prenatal');
    Route::get('/patients/{patientId}/prenatal-records', [App\Http\Controllers\AdminController::class, 'adminPatientPrenatalRecords'])->name('patients.prenatal-records');

    Route::get('/medical-records/postnatal', [App\Http\Controllers\AdminController::class, 'adminPostnatalRecords'])->name('medical-records.postnatal');
    Route::get('/patients/{patientId}/postnatal-records', [App\Http\Controllers\AdminController::class, 'adminPatientPostnatalRecords'])->name('patients.postnatal-records');

    Route::get('/medical-records/postpartum', [App\Http\Controllers\AdminController::class, 'adminPostpartumRecords'])->name('medical-records.postpartum');
    Route::get('/patients/{patientId}/postpartum-records', [App\Http\Controllers\AdminController::class, 'adminPatientPostpartumRecords'])->name('patients.postpartum-records');

    Route::get('/medical-records/delivery', [App\Http\Controllers\AdminController::class, 'adminDeliveryRecords'])->name('medical-records.delivery');
    Route::get('/patients/{patientId}/delivery-records', [App\Http\Controllers\AdminController::class, 'adminPatientDeliveryRecords'])->name('patients.delivery-records');

    Route::get('/medical-records/lab-results', [App\Http\Controllers\AdminController::class, 'adminLabResults'])->name('medical-records.lab-results');
    Route::get('/patients/{patientId}/lab-results', [App\Http\Controllers\AdminController::class, 'adminPatientLabResults'])->name('patients.lab-results');
    Route::get('/patients/{patientId}/philhealth', [App\Http\Controllers\AdminController::class, 'editPatientPhilHealth'])->name('patients.philhealth.show');
    Route::put('/patients/{patientId}/philhealth', [App\Http\Controllers\AdminController::class, 'updatePatientPhilHealth'])->name('patients.philhealth.update');
    Route::get('/appointments', [App\Http\Controllers\AdminController::class, 'appointments'])->name('appointments');
    Route::get('/appointments/{appointmentId}', [App\Http\Controllers\AdminController::class, 'showAppointment'])->name('appointments.show');
    Route::post('/appointments/{appointmentId}/status', [App\Http\Controllers\AdminController::class, 'updateAppointmentStatus'])->name('appointments.update-status');
    Route::get('/medical-records', [App\Http\Controllers\AdminController::class, 'medicalRecords'])->name('medical-records');
    Route::get('/prenatal-records', [App\Http\Controllers\AdminController::class, 'prenatalRecords'])->name('prenatal-records');
    Route::get('/attendance', [App\Http\Controllers\AdminController::class, 'attendance'])->name('attendance');
    Route::get('/attendance/{attendance}/details', [App\Http\Controllers\AdminController::class, 'getAttendanceDetails'])->name('attendance.details');
    Route::delete('/attendance/{attendance}', [App\Http\Controllers\AdminController::class, 'deleteAttendance'])->name('attendance.delete');
    Route::get('/attendance/{attendance}/edit', [App\Http\Controllers\AdminController::class, 'editAttendance'])->name('attendance.edit');
    Route::put('/attendance/{attendance}', [App\Http\Controllers\AdminController::class, 'updateAttendance'])->name('attendance.update');
    Route::get('/clock-in-out', [App\Http\Controllers\AdminController::class, 'clockInOut'])->name('clock-in-out');
    Route::post('/clock-in-out', [App\Http\Controllers\AdminController::class, 'processClockInOut'])->name('process-clock-in-out');
    Route::post('/attendance/clock-in/{employee}', [App\Http\Controllers\AdminController::class, 'adminClockIn'])->name('attendance.clock-in');
    Route::post('/attendance/clock-out/{employee}', [App\Http\Controllers\AdminController::class, 'adminClockOut'])->name('attendance.clock-out');
    Route::get('/payroll', [App\Http\Controllers\AdminController::class, 'payroll'])->name('payroll');
    Route::post('/payroll/generate', [App\Http\Controllers\AdminController::class, 'generatePayroll'])->name('payroll.generate');
    Route::post('/payroll/{payroll}/process', [App\Http\Controllers\AdminController::class, 'processPayroll'])->name('payroll.process');
    Route::post('/payroll/{payroll}/mark-paid', [App\Http\Controllers\AdminController::class, 'markPayrollAsPaid'])->name('payroll.mark-paid');
    Route::get('/payroll/{payroll}/pay-slip', [App\Http\Controllers\AdminController::class, 'generatePaySlip'])->name('payroll.pay-slip');
    Route::get('/payroll-reports', [App\Http\Controllers\AdminController::class, 'payrollReports'])->name('payroll-reports');
    // Document Management
    Route::get('/documents', [App\Http\Controllers\AdminController::class, 'documents'])->name('documents');
    Route::get('/documents/create', [App\Http\Controllers\AdminController::class, 'createDocument'])->name('documents.create');
    Route::post('/documents', [App\Http\Controllers\AdminController::class, 'storeDocument'])->name('documents.store');
    Route::get('/documents/{document}', [App\Http\Controllers\AdminController::class, 'showDocument'])->name('documents.show');
    Route::get('/documents/{document}/edit', [App\Http\Controllers\AdminController::class, 'editDocument'])->name('documents.edit');
    Route::put('/documents/{document}', [App\Http\Controllers\AdminController::class, 'updateDocument'])->name('documents.update');
    Route::delete('/documents/{document}', [App\Http\Controllers\AdminController::class, 'deleteDocument'])->name('documents.delete');
    Route::get('/documents/{document}/download', [App\Http\Controllers\AdminController::class, 'downloadDocument'])->name('documents.download');

    Route::get('/services', [App\Http\Controllers\AdminController::class, 'services'])->name('services');
    Route::get('/services/create', [App\Http\Controllers\AdminController::class, 'createService'])->name('services.create');
    Route::post('/services', [App\Http\Controllers\AdminController::class, 'storeService'])->name('services.store');
    Route::get('/services/{service}', [App\Http\Controllers\AdminController::class, 'showService'])->name('services.show');
    Route::get('/services/{service}/edit', [App\Http\Controllers\AdminController::class, 'editService'])->name('services.edit');
    Route::put('/services/{service}', [App\Http\Controllers\AdminController::class, 'updateService'])->name('services.update');
    Route::delete('/services/{service}', [App\Http\Controllers\AdminController::class, 'deleteService'])->name('services.delete');

    Route::get('/reports', [App\Http\Controllers\AdminController::class, 'reports'])->name('reports');

    // Inventory Management
    Route::get('/inventory', [App\Http\Controllers\AdminController::class, 'inventory'])->name('inventory');
    Route::get('/inventory/create', [App\Http\Controllers\AdminController::class, 'createInventory'])->name('inventory.create');
    Route::post('/inventory', [App\Http\Controllers\AdminController::class, 'storeInventory'])->name('inventory.store');
    Route::get('/inventory/{inventory}', [App\Http\Controllers\AdminController::class, 'showInventory'])->name('inventory.show');
    Route::get('/inventory/{inventory}/edit', [App\Http\Controllers\AdminController::class, 'editInventory'])->name('inventory.edit');
    Route::put('/inventory/{inventory}', [App\Http\Controllers\AdminController::class, 'updateInventory'])->name('inventory.update');
    Route::delete('/inventory/{inventory}', [App\Http\Controllers\AdminController::class, 'deleteInventory'])->name('inventory.delete');
    Route::post('/inventory/{inventory}/update-stock', [App\Http\Controllers\AdminController::class, 'updateInventoryStock'])->name('inventory.update-stock');
    Route::get('/inventory/{inventory}/movements', [App\Http\Controllers\AdminController::class, 'inventoryMovements'])->name('inventory.movements');

    // Employee Schedule Management
    Route::get('/schedules', [App\Http\Controllers\AdminController::class, 'schedules'])->name('schedules');
    Route::get('/schedules/create', [App\Http\Controllers\AdminController::class, 'createSchedule'])->name('schedules.create');
    Route::post('/schedules', [App\Http\Controllers\AdminController::class, 'storeSchedule'])->name('schedules.store');
    Route::get('/schedules/{employee}', [App\Http\Controllers\AdminController::class, 'manageEmployeeSchedules'])->name('schedules.manage');
    Route::put('/schedules/{employee}/update', [App\Http\Controllers\AdminController::class, 'updateEmployeeSchedules'])->name('schedules.update-employee');
    Route::get('/schedules/{schedule}/show', [App\Http\Controllers\AdminController::class, 'showSchedule'])->name('schedules.show');
    Route::get('/schedules/{schedule}/edit', [App\Http\Controllers\AdminController::class, 'editSchedule'])->name('schedules.edit');
    Route::put('/schedules/{schedule}', [App\Http\Controllers\AdminController::class, 'updateSchedule'])->name('schedules.update');
    Route::delete('/schedules/{schedule}', [App\Http\Controllers\AdminController::class, 'deleteSchedule'])->name('schedules.delete');

    // Mandatory Deductions Management
    Route::get('/mandatory-deductions', [App\Http\Controllers\AdminController::class, 'mandatoryDeductions'])->name('mandatory-deductions');
    Route::get('/mandatory-deductions/create', [App\Http\Controllers\AdminController::class, 'createMandatoryDeduction'])->name('mandatory-deductions.create');
    Route::post('/mandatory-deductions', [App\Http\Controllers\AdminController::class, 'storeMandatoryDeduction'])->name('mandatory-deductions.store');
    Route::get('/mandatory-deductions/{deduction}', [App\Http\Controllers\AdminController::class, 'showMandatoryDeduction'])->name('mandatory-deductions.show');
    Route::get('/mandatory-deductions/{deduction}/edit', [App\Http\Controllers\AdminController::class, 'editMandatoryDeduction'])->name('mandatory-deductions.edit');
    Route::put('/mandatory-deductions/{deduction}', [App\Http\Controllers\AdminController::class, 'updateMandatoryDeduction'])->name('mandatory-deductions.update');
    Route::delete('/mandatory-deductions/{deduction}', [App\Http\Controllers\AdminController::class, 'deleteMandatoryDeduction'])->name('mandatory-deductions.delete');

    Route::get('/employee-deductions', [App\Http\Controllers\AdminController::class, 'employeeDeductions'])->name('employee-deductions');
    Route::get('/employee-deductions/{employee}', [App\Http\Controllers\AdminController::class, 'manageEmployeeDeductions'])->name('employee-deductions.manage');
    Route::put('/employee-deductions/{employee}/update', [App\Http\Controllers\AdminController::class, 'updateEmployeeDeductions'])->name('employee-deductions.update-employee');
    Route::post('/employee-deductions/bulk-update', [App\Http\Controllers\AdminController::class, 'bulkUpdateEmployeeDeductions'])->name('employee-deductions.bulk-update');

    Route::get('/settings', [App\Http\Controllers\AdminController::class, 'settings'])->name('settings');
    Route::post('/settings/sms', [App\Http\Controllers\AdminController::class, 'updateSmsSettings'])->name('update-sms-settings');
    Route::post('/settings/test-sms', [App\Http\Controllers\AdminController::class, 'testSms'])->name('test-sms');
    Route::post('/settings/check-sms-credits', [App\Http\Controllers\AdminController::class, 'checkSmsCredits'])->name('check-sms-credits');
    Route::get('/sms-logs', [App\Http\Controllers\AdminController::class, 'smsLogs'])->name('sms-logs');
    Route::get('/sms-logs/{smsLog}', [App\Http\Controllers\AdminController::class, 'getSmsLogDetails'])->name('sms-logs.details');
    Route::delete('/sms-logs/{smsLog}', [App\Http\Controllers\AdminController::class, 'deleteSmsLog'])->name('sms-logs.delete');

    // Payment Management
    Route::get('/payments', [App\Http\Controllers\AdminController::class, 'payments'])->name('payments');
    Route::get('/payments/{paymentId}', [App\Http\Controllers\AdminController::class, 'showPayment'])->name('payments.show');
    Route::post('/payments/{paymentId}/approve', [App\Http\Controllers\AdminController::class, 'approvePayment'])->name('payments.approve');
    Route::post('/payments/{paymentId}/reject', [App\Http\Controllers\AdminController::class, 'rejectPayment'])->name('payments.reject');
    Route::post('/payments/{paymentId}/partial', [App\Http\Controllers\AdminController::class, 'processPartialPayment'])->name('payments.partial');
});

// Employee routes (pure Laravel MVC)
Route::middleware(['auth:backpack', 'employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\EmployeeController::class, 'dashboard'])->name('dashboard');
    Route::get('/appointments', [App\Http\Controllers\EmployeeController::class, 'appointments'])->name('appointments');
    Route::get('/profile', [App\Http\Controllers\EmployeeController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\EmployeeController::class, 'updateProfile'])->name('profile.update');
    Route::get('/schedule', [App\Http\Controllers\EmployeeController::class, 'schedule'])->name('schedule');
    Route::get('/attendance', [App\Http\Controllers\EmployeeController::class, 'attendance'])->name('attendance');
    Route::post('/clock', [App\Http\Controllers\EmployeeController::class, 'clock'])->name('clock');
    Route::get('/payroll', [App\Http\Controllers\EmployeeController::class, 'payroll'])->name('payroll');
    Route::get('/payroll/{payroll}/pay-slip', [App\Http\Controllers\EmployeeController::class, 'generatePaySlip'])->name('payroll.pay-slip');

    // Patient Management Routes
    Route::get('/patients', [App\Http\Controllers\EmployeeController::class, 'patients'])->name('patients');
    Route::get('/patients/create', [App\Http\Controllers\EmployeeController::class, 'createPatient'])->name('patients.create');
    Route::post('/patients', [App\Http\Controllers\EmployeeController::class, 'storePatient'])->name('patients.store');
    Route::get('/patients/{patientId}', [App\Http\Controllers\EmployeeController::class, 'showPatient'])->name('patients.show');
    Route::get('/patients/{patientId}/edit', [App\Http\Controllers\EmployeeController::class, 'editPatient'])->name('patients.edit');
    Route::put('/patients/{patientId}', [App\Http\Controllers\EmployeeController::class, 'updatePatient'])->name('patients.update');
    Route::delete('/patients/{patientId}', [App\Http\Controllers\EmployeeController::class, 'deletePatient'])->name('patients.delete');
    Route::get('/patients/{patientId}/schedule-appointment', [App\Http\Controllers\EmployeeController::class, 'scheduleAppointment'])->name('patients.schedule-appointment');
    Route::post('/patients/{patientId}/schedule-appointment', [App\Http\Controllers\EmployeeController::class, 'storeScheduledAppointment'])->name('patients.store-scheduled-appointment');
    Route::get('/patients/{patientId}/appointments', [App\Http\Controllers\EmployeeController::class, 'patientAppointments'])->name('patients.appointments');
    Route::get('/patients/{patientId}/appointments/{appointmentId}/details', [App\Http\Controllers\EmployeeController::class, 'getAppointmentDetails'])->name('patients.appointments.details');
    Route::post('/patients/{patientId}/appointments/{appointmentId}/status', [App\Http\Controllers\EmployeeController::class, 'updateAppointmentStatus'])->name('patients.appointments.update-status');
    Route::get('/patients/{patientId}/medical-records', [App\Http\Controllers\EmployeeController::class, 'patientMedicalRecords'])->name('patients.medical-records');

    // Employee Appointment Management
    Route::post('/appointments/{appointmentId}/status', [App\Http\Controllers\EmployeeController::class, 'updateEmployeeAppointmentStatus'])->name('appointments.update-status');

    // Medical Records Management
    Route::get('/patients/{patientId}/prenatal-records', [App\Http\Controllers\EmployeeController::class, 'prenatalRecords'])->name('patients.prenatal-records');
    Route::get('/patients/{patientId}/prenatal-records/create', [App\Http\Controllers\EmployeeController::class, 'createPrenatalRecord'])->name('patients.create-prenatal-record');
    Route::get('/patients/{patientId}/prenatal-records/{record}', [App\Http\Controllers\EmployeeController::class, 'showPrenatalRecord'])->name('patients.show-prenatal-record');
    Route::get('/patients/{patientId}/prenatal-records/{record}/edit', [App\Http\Controllers\EmployeeController::class, 'editPrenatalRecord'])->name('patients.edit-prenatal-record');
    Route::post('/patients/{patientId}/prenatal-records', [App\Http\Controllers\EmployeeController::class, 'storePrenatalRecord'])->name('patients.store-prenatal-record');
    Route::put('/patients/{patientId}/prenatal-records/{record}', [App\Http\Controllers\EmployeeController::class, 'updatePrenatalRecord'])->name('patients.update-prenatal-record');
    Route::delete('/patients/{patientId}/prenatal-records/{record}', [App\Http\Controllers\EmployeeController::class, 'deletePrenatalRecord'])->name('patients.delete-prenatal-record');

    Route::get('/patients/{patientId}/postnatal-records/{record}', [App\Http\Controllers\EmployeeController::class, 'showPostnatalRecord'])->name('patients.show-postnatal-record');
    Route::get('/patients/{patientId}/postnatal-records/{record}/edit', [App\Http\Controllers\EmployeeController::class, 'editPostnatalRecord'])->name('patients.edit-postnatal-record');
    Route::put('/patients/{patientId}/postnatal-records/{record}', [App\Http\Controllers\EmployeeController::class, 'updatePostnatalRecord'])->name('patients.update-postnatal-record');

    Route::get('/patients/{patientId}/postnatal-records', [App\Http\Controllers\EmployeeController::class, 'postnatalRecords'])->name('patients.postnatal-records');
    Route::get('/patients/{patientId}/postnatal-record/add', [App\Http\Controllers\EmployeeController::class, 'createPostnatalRecord'])->name('patients.create-postnatal-record');
    Route::post('/patients/{patientId}/postnatal-records', [App\Http\Controllers\EmployeeController::class, 'storePostnatalRecord'])->name('patients.store-postnatal-record');
    Route::get('/patients/{patientId}/postnatal-records/{record}', [App\Http\Controllers\EmployeeController::class, 'showPostnatalRecord'])->name('patients.show-postnatal-record');
    Route::get('/patients/{patientId}/postnatal-records/{record}/edit', [App\Http\Controllers\EmployeeController::class, 'editPostnatalRecord'])->name('patients.edit-postnatal-record');
    Route::put('/patients/{patientId}/postnatal-records/{record}', [App\Http\Controllers\EmployeeController::class, 'updatePostnatalRecord'])->name('patients.update-postnatal-record');
    Route::delete('/patients/{patientId}/postnatal-records/{record}', [App\Http\Controllers\EmployeeController::class, 'deletePostnatalRecord'])->name('patients.delete-postnatal-record');

    Route::get('/patients/{patientId}/postpartum-records', [App\Http\Controllers\EmployeeController::class, 'postpartumRecords'])->name('patients.postpartum-records');
    Route::get('/patients/{patientId}/postpartum-records/create', [App\Http\Controllers\EmployeeController::class, 'createPostpartumRecord'])->name('patients.create-postpartum-record');
    Route::post('/patients/{patientId}/postpartum-records', [App\Http\Controllers\EmployeeController::class, 'storePostpartumRecord'])->name('patients.store-postpartum-record');
    Route::get('/patients/{patientId}/postpartum-records/{record}', [App\Http\Controllers\EmployeeController::class, 'showPostpartumRecord'])->name('patients.show-postpartum-record');
    Route::get('/patients/{patientId}/postpartum-records/{record}/edit', [App\Http\Controllers\EmployeeController::class, 'editPostpartumRecord'])->name('patients.edit-postpartum-record');
    Route::put('/patients/{patientId}/postpartum-records/{record}', [App\Http\Controllers\EmployeeController::class, 'updatePostpartumRecord'])->name('patients.update-postpartum-record');
    Route::delete('/patients/{patientId}/postpartum-records/{record}', [App\Http\Controllers\EmployeeController::class, 'deletePostpartumRecord'])->name('patients.delete-postpartum-record');

    Route::get('/patients/{patientId}/delivery-records', [App\Http\Controllers\EmployeeController::class, 'deliveryRecords'])->name('patients.delivery-records');
    Route::get('/patients/{patientId}/delivery-records/create', [App\Http\Controllers\EmployeeController::class, 'createDeliveryRecord'])->name('patients.create-delivery-record');
    Route::post('/patients/{patientId}/delivery-records', [App\Http\Controllers\EmployeeController::class, 'storeDeliveryRecord'])->name('patients.store-delivery-record');
    Route::get('/patients/{patientId}/delivery-records/{record}', [App\Http\Controllers\EmployeeController::class, 'showDeliveryRecord'])->name('patients.show-delivery-record');
    Route::get('/patients/{patientId}/delivery-records/{record}/edit', [App\Http\Controllers\EmployeeController::class, 'editDeliveryRecord'])->name('patients.edit-delivery-record');
    Route::put('/patients/{patientId}/delivery-records/{record}', [App\Http\Controllers\EmployeeController::class, 'updateDeliveryRecord'])->name('patients.update-delivery-record');
    Route::delete('/patients/{patientId}/delivery-records/{record}', [App\Http\Controllers\EmployeeController::class, 'deleteDeliveryRecord'])->name('patients.delete-delivery-record');

    Route::get('/patients/{patientId}/lab-results', [App\Http\Controllers\EmployeeController::class, 'labResults'])->name('patients.lab-results');
    Route::get('/patients/{patientId}/lab-results/create', [App\Http\Controllers\EmployeeController::class, 'createLabResult'])->name('patients.create-lab-result');
    Route::post('/patients/{patientId}/lab-results', [App\Http\Controllers\EmployeeController::class, 'storeLabResult'])->name('patients.store-lab-result');
    Route::get('/patients/{patientId}/lab-results/{result}', [App\Http\Controllers\EmployeeController::class, 'showLabResult'])->name('patients.show-lab-result');
    Route::get('/patients/{patientId}/lab-results/{result}/edit', [App\Http\Controllers\EmployeeController::class, 'editLabResult'])->name('patients.edit-lab-result');
    Route::put('/patients/{patientId}/lab-results/{result}', [App\Http\Controllers\EmployeeController::class, 'updateLabResult'])->name('patients.update-lab-result');
    Route::delete('/patients/{patientId}/lab-results/{result}', [App\Http\Controllers\EmployeeController::class, 'deleteLabResult'])->name('patients.delete-lab-result');

    // Treatment and Prescription Management
    Route::get('/patients/{patientId}/treatments', [App\Http\Controllers\EmployeeController::class, 'treatments'])->name('patients.treatments');
    Route::get('/patients/{patientId}/treatments/create', [App\Http\Controllers\EmployeeController::class, 'createTreatment'])->name('patients.create-treatment');
    Route::post('/patients/{patientId}/treatments', [App\Http\Controllers\EmployeeController::class, 'storeTreatment'])->name('patients.store-treatment');
    Route::get('/patients/{patientId}/treatments/{treatmentId}', [App\Http\Controllers\EmployeeController::class, 'showTreatment'])->name('patients.show-treatment');
    Route::get('/patients/{patientId}/treatments/{treatmentId}/edit', [App\Http\Controllers\EmployeeController::class, 'editTreatment'])->name('patients.edit-treatment');
    Route::put('/patients/{patientId}/treatments/{treatmentId}', [App\Http\Controllers\EmployeeController::class, 'updateTreatment'])->name('patients.update-treatment');
    Route::delete('/patients/{patientId}/treatments/{treatmentId}', [App\Http\Controllers\EmployeeController::class, 'deleteTreatment'])->name('patients.delete-treatment');
    Route::post('/patients/{patientId}/treatments/{treatmentId}/dispense', [App\Http\Controllers\EmployeeController::class, 'dispenseMedication'])->name('patients.dispense-medication');

    // Payment Management
    Route::get('/payments', [App\Http\Controllers\EmployeeController::class, 'payments'])->name('payments');
    Route::get('/payments/{paymentId}', [App\Http\Controllers\EmployeeController::class, 'showPayment'])->name('payments.show');
    Route::get('/patients/{patientId}/payments/create', [App\Http\Controllers\EmployeeController::class, 'createPayment'])->name('patients.create-payment');
    Route::post('/patients/{patientId}/payments', [App\Http\Controllers\EmployeeController::class, 'storePayment'])->name('patients.store-payment');
    Route::get('/patients/{patientId}/payments', [App\Http\Controllers\EmployeeController::class, 'patientPayments'])->name('patients.payments');
});

// Page Manager routes (if you're using Backpack's Page Manager) - Keep this at the END
Route::get('{page}/{subs?}', [App\Http\Controllers\PageController::class, 'index'])
    ->where(['page' => '^(((?=(?!admin))(?=(?!employee))(?=(?!\/)).))*$', 'subs' => '.*'])
    ->name('pages');

