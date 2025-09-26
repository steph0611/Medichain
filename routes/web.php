<?php

use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\AdminSettingsController;

// ---------------------------
// Landing Page
// ---------------------------
Route::get('/', fn () => view('landing'))->name('landing');

// ---------------------------
// Login & Auth
// ---------------------------
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.check');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Email Verification
Route::get('/verify-email', [AuthController::class, 'verifyEmail'])->name('verify.email');
Route::get('/verify-notice', fn () => view('verifyNotice'))->name('verify.notice');

// ---------------------------
// Dashboard
// ---------------------------
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// ---------------------------
// Pharmacy
// ---------------------------
Route::get('/pharmacy/upload/{id}', [PrescriptionController::class, 'showUploadForm'])
    ->name('pharmacy.upload.form');
Route::post('/pharmacy/upload/{id}', [PrescriptionController::class, 'uploadPrescription'])
    ->name('pharmacy.upload.submit');

Route::get('/pharmacy/{shop_id}/dashboard', [PharmacyDashboardController::class, 'index'])
    ->name('pharmacy.dashboard');
Route::patch('/pharmacy/prescriptions/{id}/status', [PharmacyDashboardController::class, 'updateStatus'])
    ->name('pharmacy.updateStatus');

// ---------------------------
// User Selection & Registration
// ---------------------------
Route::get('/userselect', fn () => view('userselect'))->name('userselect');

Route::get('/registerC', [RegCController::class, 'showRegisterCForm'])->name('registerC');
Route::post('/registerC', [RegCController::class, 'register'])->name('register.customer');

Route::get('/registerS', [RegSController::class, 'showRegisterSForm'])->name('registerS');
Route::post('/registerS', [RegSController::class, 'register'])->name('register.shop');

// ---------------------------
// Prescriptions (Customer)
// ---------------------------
Route::post('/prescription/upload', [PrescriptionController::class, 'upload'])
    ->name('prescription.upload.general');
Route::get('/prescription/upload/{shop_id}', [PrescriptionController::class, 'showUploadForm'])
    ->name('prescription.upload.form');
Route::post('/prescription/upload/{shop_id}', [PrescriptionController::class, 'uploadPrescription'])
    ->name('prescription.upload.shop');

// ---------------------------
// Orders
// ---------------------------
Route::group([], function () {
    Route::get('/orders', fn () => app(OrderStatusController::class)->index())
        ->name('orders.index');
    Route::post('/orders', fn () => app(OrderStatusController::class)->store())
        ->name('orders.store');
    Route::post('/orders/{id}/status', fn ($id) => app(OrderStatusController::class)->updateStatus($id))
        ->name('orders.updateStatus');
});
Route::patch('/orders/cancel/{id}', [OrderStatusController::class, 'cancelOrder'])->name('orders.cancel');

// ---------------------------
// Pharmacies
// ---------------------------
Route::get('/pharmacies', [PharmacyController::class, 'index'])->name('pharmacies.index');
Route::get('/pharmacies/{id}', [PharmacyController::class, 'show'])->name('pharmacy.show');
Route::get('/pharmacies/{id}/orders', [PharmacyController::class, 'orders'])->name('pharmacy.orders');

// ---------------------------
// History
// ---------------------------
Route::get('/history', [HistoryController::class, 'index'])->name('history.index');

// ---------------------------
// Forgot / Reset Password
// ---------------------------
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// ---------------------------
// Admin Dashboard & Settings
// ---------------------------
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/stats', [AdminDashboardController::class, 'stats'])->name('admin.stats');

Route::get('/admin/pharmacies', [AdminPharmacyController::class, 'index'])->name('admin.pharmacies.index');
Route::post('/admin/pharmacies', [AdminPharmacyController::class, 'store'])->name('admin.pharmacies.store');
Route::delete('/admin/pharmacies/{id}', [AdminPharmacyController::class, 'destroy'])->name('admin.pharmacies.destroy');

Route::get('/admin/prescriptions', [AdminPrescriptionController::class, 'index'])->name('admin.prescriptions.index');
Route::get('/admin/prescriptions/{shop_id}', [AdminPrescriptionController::class, 'show'])->name('admin.prescriptions.show');

Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
Route::get('/admin/users/{type}', [AdminUserController::class, 'show'])->name('admin.users.show');
Route::post('/admin/users/{type}', [AdminUserController::class, 'store'])->name('admin.users.store');
Route::delete('/admin/users/{type}/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

Route::prefix('admin')->group(function () {
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('admin.settings.index');
    Route::patch('/settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');
    Route::post('/settings/logout', [AdminSettingsController::class, 'logout'])->name('admin.settings.logout');
});

// ---------------------------
// Customer Profile & Settings
// ---------------------------
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

// ---------------------------
// Payments
// ---------------------------
Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.show');
Route::post('/payment', [PaymentController::class, 'processPayment'])->name('payment.process');
Route::get('/payment-success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
