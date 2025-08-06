<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegSController;
use App\Http\Controllers\RegCController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\OrderController;

// ---------------------------
// Initial screen & Login
// ---------------------------
Route::get('/', [AuthController::class, 'showLoginForm']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.check');

// ---------------------------
// Email Verification
// ---------------------------
Route::get('/verify-email', [AuthController::class, 'verifyEmail'])->name('verify.email');
Route::get('/verify-notice', function () {
    return view('verifyNotice');
})->name('verify.notice');

// ---------------------------
// Dashboard
// ---------------------------
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('web')
    ->name('dashboard');

// ---------------------------
// Pharmacy Upload Routes
// ---------------------------
Route::get('/pharmacy/upload/{id}', [PrescriptionController::class, 'showUploadForm'])
    ->name('pharmacy.upload.form');
Route::post('/pharmacy/upload/{id}', [PrescriptionController::class, 'uploadPrescription'])
    ->name('pharmacy.upload.submit');

// ---------------------------
// Pharmacy Dashboard & Prescriptions
// ---------------------------
Route::get('/pharmacy/{shop_id}/dashboard', [PrescriptionController::class, 'viewDashboard'])
    ->name('pharmacy.dashboard');

// Status update (Pending → Accepted → Ready → Delivered)
Route::put('/prescriptions/{id}/status', [PrescriptionController::class, 'updateStatus'])
    ->name('prescriptions.updateStatus');

// Mark processed (legacy support, optional)
Route::post('/prescription/{id}/process', [PrescriptionController::class, 'markProcessed'])
    ->name('prescription.process');

// Delete prescription
Route::delete('/prescription/{id}/delete', [PrescriptionController::class, 'delete'])
    ->name('prescription.delete');

// ---------------------------
// Logout
// ---------------------------
Route::post('/logout', function () {
    Session::forget('user');
    return redirect('/login')->with('success', 'You have been logged out.');
})->name('logout');

// ---------------------------
// User Type Selection
// ---------------------------
Route::get('/userselect', function () {
    return view('userselect');
})->name('userselect');

// ---------------------------
// Customer Registration
// ---------------------------
Route::get('/registerC', [RegCController::class, 'showRegisterCForm'])->name('registerC');
Route::post('/registerC', [RegCController::class, 'register'])->name('register.post');

// ---------------------------
// Shop Registration
// ---------------------------
Route::get('/registerS', [RegSController::class, 'showRegisterSForm'])->name('registerS');
Route::post('/registerS', [RegSController::class, 'register'])->name('register.post');

// ---------------------------
// Orders
// ---------------------------
Route::get('/orders', function () {
    return view('order');
})->name('orders');


Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
