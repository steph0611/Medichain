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
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminPharmacyController;
use App\Http\Controllers\AdminPrescriptionController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PaymentController;


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
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


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




Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/stats', [AdminDashboardController::class, 'stats'])->name('admin.stats'); // JSON API for charts






Route::get('/admin/pharmacies', [AdminPharmacyController::class, 'index'])->name('admin.pharmacies.index');
Route::post('/admin/pharmacies', [AdminPharmacyController::class, 'store'])->name('admin.pharmacies.store');
Route::delete('/admin/pharmacies/{id}', [AdminPharmacyController::class, 'destroy'])->name('admin.pharmacies.destroy');



Route::get('/admin/prescriptions', [AdminPrescriptionController::class, 'index'])->name('admin.prescriptions.index');
Route::get('/admin/prescriptions/{shop_id}', [AdminPrescriptionController::class, 'show'])->name('admin.prescriptions.show');






// Overview Tiles page
Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');

// Show table for a specific type: admins, pharmacies, customers
Route::get('/admin/users/{type}', [AdminUserController::class, 'show'])->name('admin.users.show');

// Add a new user
Route::post('/admin/users/{type}', [AdminUserController::class, 'store'])->name('admin.users.store');

// Delete a user
Route::delete('/admin/users/{type}/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');



Route::prefix('admin')->group(function() {
    Route::get('/settings', [App\Http\Controllers\AdminSettingsController::class, 'index'])->name('admin.settings.index');
    Route::patch('/settings', [App\Http\Controllers\AdminSettingsController::class, 'update'])->name('admin.settings.update');
    Route::post('/settings/logout', [App\Http\Controllers\AdminSettingsController::class, 'logout'])->name('admin.settings.logout');
});



Route::get('/customer/profile', [CustomerProfileController::class, 'edit'])->name('customer.profile');
Route::post('/customer/profile/update', [CustomerProfileController::class, 'update'])->name('customer.profile.update');
Route::post('/customer/profile/password', [CustomerProfileController::class, 'updatePassword'])->name('customer.profile.password');





Route::prefix('customer/settings')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('customer.settings');

    Route::patch('/', [SettingsController::class, 'update'])->name('customer.settings.update');

    Route::post('/notifications', [SettingsController::class, 'saveNotifications'])->name('customer.settings.notifications');
    Route::post('/preferences', [SettingsController::class, 'savePreferences'])->name('customer.settings.preferences');
    Route::post('/privacy', [SettingsController::class, 'savePrivacy'])->name('customer.settings.privacy');

    Route::post('/deactivate', [SettingsController::class, 'deactivateAccount'])->name('customer.settings.deactivate');
    Route::post('/delete', [SettingsController::class, 'deleteAccount'])->name('customer.settings.delete');
});




// Show prescription upload form for a specific shop
Route::get('/prescription/upload/{shop_id}', [PrescriptionController::class, 'showUploadForm'])
    ->name('prescription.upload');

// Handle prescription upload and create order
Route::post('/prescription/upload/{shop_id}', [PrescriptionController::class, 'uploadPrescription'])
    ->name('prescription.upload.post');

// ---------------------------
// Payment Routes
// ---------------------------

// Show payment form
Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.show');

// Process payment (Stripe)
Route::post('/payment', [PaymentController::class, 'processPayment'])->name('payment.process');

// Payment success page
Route::get('/payment-success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');