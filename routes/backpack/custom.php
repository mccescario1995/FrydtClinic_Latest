<?php

use App\Http\Controllers\AppointmentBookingController;
use App\Http\Controllers\Admin\InventoryMovementsCrudController;
use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\CRUD.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes

    // Custom Dashboard
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    // Patient Management
    Route::crud('patient', 'PatientCrudController');

    // Medical Records
    Route::crud('prenatal-record', 'PrenatalRecordCrudController');
    Route::crud('laboratory-result', 'LaboratoryResultCrudController');

    // Appointments & Scheduling
    Route::crud('appointment', 'AppointmentCrudController');
    Route::get('appointment/book', [AppointmentBookingController::class, 'showForm'])->name('appointment.booking.form');
    Route::post('appointment/book', [AppointmentBookingController::class, 'store'])->name('appointment.booking.store');
    Route::post('appointment/get-available-employees', [AppointmentBookingController::class, 'getAvailableEmployees']);

    // Search routes
    Route::get('search', [App\Http\Controllers\Admin\SearchController::class, 'index'])->name('search.index');
    Route::get('search/advanced', [App\Http\Controllers\Admin\SearchController::class, 'advanced'])->name('search.advanced');
    Route::get('search/suggestions', [App\Http\Controllers\Admin\SearchController::class, 'suggestions'])->name('search.suggestions');
    Route::get('search/{type}', [App\Http\Controllers\Admin\SearchController::class, 'quickSearch'])->name('search.quick');
    Route::get('search-export', [App\Http\Controllers\Admin\SearchController::class, 'export'])->name('search.export');

    // Billing & Payments
    Route::crud('billing', 'BillingCrudController');

    // Services & Inventory
    Route::crud('service', 'ServiceCrudController');
    Route::crud('inventory', 'InventoryCrudController');
    Route::crud('inventory-category', 'InventoryCategoryCrudController');
    Route::get('inventory/{id}/manage-stock', [InventoryMovementsCrudController::class, 'manageStock'])->name('inventory.manage-stock');
    Route::post('inventory/{id}/update-stock', [InventoryMovementsCrudController::class, 'updateStock'])->name('inventory.update-stock');
    Route::crud('inventory-movements', 'InventoryMovementsCrudController');

    // Forms & Documents
    Route::crud('form', 'FormCrudController');
    // Route::crud('document', 'DocumentCrudController'); // Disabled to avoid conflict with custom admin-portal documents

    // Reports
    Route::get('reports/patient', 'ReportController@patientReports')->name('report.patient');
    Route::get('reports/financial', 'ReportController@financialReports')->name('report.financial');
    Route::get('reports/laboratory', 'ReportController@laboratoryReports')->name('report.laboratory');
    Route::get('reports/export/patient', 'ReportController@exportPatientReport')->name('report.export.patient');
    Route::get('reports/export/financial', 'ReportController@exportFinancialReport')->name('report.export.financial');
    Route::get('reports/export/laboratory', 'ReportController@exportLaboratoryReport')->name('report.export.laboratory');
    Route::crud('activity-log', 'ActivityLogCrudController');
}); // this should be the absolute last line of this file

/**
 * DO NOT ADD ANYTHING HERE.
 */
