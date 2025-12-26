<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

// Guest routes (tidak perlu login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Public homepage (bisa diakses semua orang)
Route::get('/', [DashboardController::class, 'index'])->name('home');

// Authenticated routes (harus login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    
    // User dashboard & transactions
    Route::get('/dashboard', function() {
        return redirect('/');
    })->name('dashboard');
});

// Admin only routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Categories routes
    Route::resource('categories', CategoryController::class);
    Route::post('categories/{category}/toggle', [CategoryController::class, 'toggleActive'])->name('categories.toggle');
});

Route::get('/pay', [PaymentController::class, 'pay']);