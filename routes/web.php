<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegSController;
use App\Http\Controllers\RegCController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PrescriptionController;

// Initial screen
Route::get('/', [AuthController::class, 'showLoginForm']);

// Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.check');

// Email verification
Route::get('/verify-email', [AuthController::class, 'verifyEmail'])->name('verify.email');
Route::get('/verify-notice', function () {
    return view('verifyNotice');
})->name('verify.notice');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('web')
    ->name('dashboard');

// ✅ FIXED: Pharmacy prescription upload routes
Route::get('/pharmacy/upload/{id}', [PrescriptionController::class, 'showUploadForm'])
    ->name('pharmacy.upload.form');
Route::post('/pharmacy/upload/{id}', [PrescriptionController::class, 'uploadPrescription'])
    ->name('pharmacy.upload.submit');

// Logout
Route::post('/logout', function () {
    Session::forget('user');
    return redirect('/login')->with('success', 'You have been logged out.');
})->name('logout');

// User type selection
Route::get('/userselect', function () {
    return view('userselect');
})->name('userselect');

// CUSTOMER Registration
Route::get('/registerC', [RegCController::class, 'showRegisterCForm'])->name('registerC');
Route::post('/registerC', [RegCController::class, 'register'])->name('register.post');

// SHOP Registration
Route::get('/registerS', [RegSController::class, 'showRegisterSForm'])->name('registerS');
Route::post('/registerS', [RegSController::class, 'register'])->name('register.post');

// Orders
Route::get('/orders', function () {
    return view('order');
})->name('orders');

Route::get('/pharmacy/{shop_id}/dashboard', [PrescriptionController::class, 'viewDashboard'])->name('pharmacy.dashboard');

