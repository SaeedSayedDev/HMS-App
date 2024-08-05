<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetEmailController;
use App\Http\Controllers\Auth\VerificationEmailController;
use App\Http\Controllers\DoctorProfileController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\MedicinePaymentController;
use App\Http\Controllers\PatientProfileController;
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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::post('email/verification/create', [VerificationEmailController::class, 'create']);
Route::post('email/verification/store', [VerificationEmailController::class, 'store']);

Route::post('email/password-reset/create', [PasswordResetEmailController::class, 'create']);
Route::post('email/password-reset/store', [PasswordResetEmailController::class, 'store']);

Route::group(['middleware' => ['auth']], function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('password/confirm', [PasswordController::class, 'confirm']);
    Route::post('password/update', [PasswordController::class, 'update']);

    Route::get('images/{image}', [ImageController::class, 'show']);

    # Appointments
    Route::get('available-slots/{doctor}', [AppointmentController::class, 'availableSlots']);

    # Medicines
    Route::get('medicines', [MedicineController::class, 'index']);
    Route::get('medicines/{medicine}', [MedicineController::class, 'show']);

    Route::group(['middleware' => 'auth.patient'], function () {
        Route::get('patient/profile', [PatientProfileController::class, 'show']);
        Route::put('patient/profile', [PatientProfileController::class, 'update']);
        Route::delete('patient/profile', [PatientProfileController::class, 'destroy']);

        # Appointments
        Route::get('appointments', [AppointmentController::class, 'index']);
        Route::get('appointments/{appointment}', [AppointmentController::class, 'show']);
        Route::post('appointments', [AppointmentController::class, 'store']);
        Route::put('appointments/{appointment}', [AppointmentController::class, 'update']);
        Route::delete('appointments/{appointment}', [AppointmentController::class, 'destroy']);
        Route::post('appointments/{appointment}/pay', [AppointmentController::class, 'pay']);

        # Medicines
        Route::post('medicines/{medicine}/pay', [MedicineController::class, 'pay']);

        # Medicine Payments
        Route::get('medicine-payments/patient', [MedicinePaymentController::class, 'indexPatient']);
        Route::get('medicine-payments/{medicinePayment}/patient', [MedicinePaymentController::class, 'showPatient']);
    });

    Route::group(['middleware' => 'auth.doctor'], function () {
        Route::get('doctor/profile', [DoctorProfileController::class, 'show']);
        Route::put('doctor/profile', [DoctorProfileController::class, 'update']);
        // Route::delete('doctor/profile', [DoctorProfileController::class, 'destroy']);

        # Appointments
        Route::get('doctor/appointments', [AppointmentController::class, 'indexDoctor']);
        Route::get('doctor/appointments/{appointment}', [AppointmentController::class, 'showDoctor']);
        Route::put('doctor/appointments/{appointment}/accept', [AppointmentController::class, 'accept']);
        Route::put('doctor/appointments/{appointment}/reject', [AppointmentController::class, 'reject']);
    });

    Route::group(['middleware' => 'AdminAuth'], function () {
        Route::group(['middleware' => 'check.permission:admin_management'], function () {
            Route::get('admins', [AdminController::class, 'getAdmins']);
            Route::get('admin/show/{admin_id}', [AdminController::class, 'showAdmin']);
            Route::post('admin/store', [AdminController::class, 'storeAdmin']);
            Route::put('admin/update/{admin_id}', [AdminController::class, 'updateAdmin']);
            Route::delete('admin/delete/{admin_id}', [AdminController::class, 'deleteAdmin']);
        });
        Route::group(['middleware' => 'check.permission:doctor_management'], function () {
            Route::get('doctor/show/{doctor_id}', [DoctorProfileController::class, 'showDoctor']);
            Route::post('doctor/store', [DoctorProfileController::class, 'storeDoctor']);
            Route::put('doctor/update/{doctor_id}', [DoctorProfileController::class, 'updateDoctor']);
            Route::post('doctor/destroy/{doctor_id}', [DoctorProfileController::class, 'destroyDoctor']);
            Route::post('doctor/working/hours/{doctor_id}', [DoctorProfileController::class, 'updateWorkingHours']);
        });

        Route::group(['middleware' => 'check.permission:medicine_management'], function () {
            # Medicines
            Route::post('medicines', [MedicineController::class, 'store']);
            Route::put('medicines/{medicine}', [MedicineController::class, 'update']);
            Route::delete('medicines/{medicine}', [MedicineController::class, 'destroy']);

            # Medicine Payments
            Route::get('medicine-payments', [MedicinePaymentController::class, 'index']);
            Route::get('medicine-payments/{medicinePayment}', [MedicinePaymentController::class, 'show']);
        });
    });

    
    Route::get('doctors', [DoctorProfileController::class, 'doctors']);
});

Route::post('/artisanOrder', [AdminController::class, 'artisanOrder'])->name('artisanOrder');
