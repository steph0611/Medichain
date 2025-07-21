<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegController;

Route::get('/', [AuthController::class, 'showLoginForm']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/dashboard', function () {
    $customer = session('user');
    return view('dashboard', compact('customer'));
})->middleware('web');

Route::post('/logout', function () {
    Session::forget('user');
    return redirect('/login')->with('success', 'You have been logged out.');
})->name('logout');


Route::get('/register', [RegController::class, 'showRegisterForm']);
Route::post('/register', [RegController::class, 'register']);
