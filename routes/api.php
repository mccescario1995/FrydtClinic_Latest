<?php

use App\Http\Controllers\Api\MobileApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Mobile API Routes
Route::prefix('mobile')->group(function () {

    // Authentication
    Route::post('login', [MobileApiController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {

        // Patient Dashboard
        Route::get('dashboard', [MobileApiController::class, 'getPatientDashboard']);

        // Appointments
        Route::get('appointments', [MobileApiController::class, 'getPatientAppointments']);
        Route::post('appointments/book', [MobileApiController::class, 'bookAppointment']);

        // Lab Results
        Route::get('lab-results', [MobileApiController::class, 'getPatientLabResults']);

        // Billing
        Route::get('billing', [MobileApiController::class, 'getPatientBilling']);

        // Profile
        Route::put('profile', [MobileApiController::class, 'updateProfile']);

        // Services & Doctors (for booking)
        Route::get('services', [MobileApiController::class, 'getServices']);
        Route::get('doctors', [MobileApiController::class, 'getDoctors']);
    });
});
