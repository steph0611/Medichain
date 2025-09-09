<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegSController;
use App\Http\Controllers\RegCController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PharmacyDashboardController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\Auth\PasswordResetController;

// ---------------------------
// Landing Page (App Info)
// ---------------------------
Route::get('/', function () {
    return view('landing'); // You'll create resources/views/landing.blade.php
})->name('landing');

// ---------------------------
// Initial screen & Login
// ---------------------------
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
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// ---------------------------
// Pharmacy Upload Routes
// ---------------------------
Route::get('/pharmacy/upload/{id}', [PrescriptionController::class, 'showUploadForm'])
    ->name('pharmacy.upload.form');

Route::post('/pharmacy/upload/{id}', [PrescriptionController::class, 'uploadPrescription'])
    ->name('pharmacy.upload.submit');

// Pharmacy Dashboard
Route::get('/pharmacy/{shop_id}/dashboard', [PharmacyDashboardController::class, 'index'])->name('pharmacy.dashboard');

// Update status
Route::patch('/pharmacy/prescriptions/{id}/status', [PharmacyDashboardController::class, 'updateStatus'])->name('pharmacy.updateStatus');

// ---------------------------
// Logout
// ---------------------------
Route::get('/logout', function () {
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
// Prescription Upload (Customer)
Route::post('/prescription/upload', [PrescriptionController::class, 'upload'])
    ->name('prescription.upload');

// ---------------------------
// Update Location
Route::post('/update-location', [LocationController::class, 'update']);

// ---------------------------
// Orders Routes (session-based protection)
Route::group([], function () {
    Route::get('/orders', function() {
        $customer = session('user');
        if (!$customer || !isset($customer['customer_id'])) {
            return redirect('/login')->with('error', 'Please login first.');
        }
        return app(OrderStatusController::class)->index();
    })->name('orders.index');

    Route::post('/orders', function() {
        $customer = session('user');
        if (!$customer || !isset($customer['customer_id'])) {
            return redirect('/login')->with('error', 'Please login first.');
        }
        return app(OrderStatusController::class)->store();
    })->name('orders.store');

    Route::post('/orders/{id}/status', function($id) {
        $customer = session('user');
        if (!$customer || !isset($customer['customer_id'])) {
            return redirect('/login')->with('error', 'Please login first.');
        }
        return app(OrderStatusController::class)->updateStatus($id);
    })->name('orders.updateStatus');
});

Route::get('/pharmacies', [PharmacyController::class, 'index'])->name('pharmacies.index');
Route::get('/pharmacies/{id}', [PharmacyController::class, 'show'])->name('pharmacy.show');
Route::get('/pharmacies/{id}/orders', [PharmacyController::class, 'orders'])->name('pharmacy.orders');

Route::get('/history', [HistoryController::class, 'index'])->name('history.index');





// Forgot Password
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');

// Reset Password
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
