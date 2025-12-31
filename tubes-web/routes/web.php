<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\VoucherCodeController;
use App\Http\Controllers\User\UserTransactionController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransCallbackController;
use App\Http\Controllers\Admin\UserController;

Route::post('/midtrans/callback', [MidtransCallbackController::class, 'handle'])
    ->name('midtrans.callback');

// Guest routes (tidak perlu login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Product detail page (public)
Route::get('/products/{product:slug}', [DashboardController::class, 'show'])->name('products.show');

// Midtrans callback (public - no auth needed)
Route::post('/midtrans/callback', [MidtransController::class, 'callback'])->name('midtrans.callback');
Route::get('/midtrans/finish', [MidtransController::class, 'finish'])->name('midtrans.finish');
Route::get('/midtrans/unfinish', [MidtransController::class, 'unfinish'])->name('midtrans.unfinish');
Route::get('/midtrans/error', [MidtransController::class, 'error'])->name('midtrans.error');

// Authenticated routes (harus login)
Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/category/{category}', [DashboardController::class, 'filterByCategory'])->name('category.filter');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    
    // User dashboard
    Route::get('/dashboard', function() {
        return redirect('/');
    })->name('dashboard');
    
    // User transactions
    Route::prefix('my')->name('user.')->group(function () {
        Route::get('/transactions', [UserTransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{transaction}', [UserTransactionController::class, 'show'])->name('transactions.show');
        Route::post('/checkout', [UserTransactionController::class, 'checkout'])->name('checkout');
    });
});

// Admin only routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Categories routes
    Route::resource('categories', CategoryController::class);
    Route::post('categories/{category}/toggle', [CategoryController::class, 'toggleActive'])->name('categories.toggle');
    
    // Products routes
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/toggle', [ProductController::class, 'toggleActive'])->name('products.toggle');
    
    // Transactions routes
    Route::get('transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/{transaction}', [AdminTransactionController::class, 'show'])->name('transactions.show');
    Route::post('transactions/{transaction}/update-status', [AdminTransactionController::class, 'updateStatus'])->name('transactions.update-status');
    Route::get('transactions/export/pdf', [AdminTransactionController::class, 'exportPDF'])->name('transactions.export-pdf');
    
    // Voucher Codes routes
    Route::get('voucher-codes', [VoucherCodeController::class, 'index'])->name('voucher-codes.index');
    Route::get('voucher-codes/create', [VoucherCodeController::class, 'create'])->name('voucher-codes.create');
    Route::post('voucher-codes', [VoucherCodeController::class, 'store'])->name('voucher-codes.store');
    Route::delete('voucher-codes/{voucherCode}', [VoucherCodeController::class, 'destroy'])->name('voucher-codes.destroy');

    // Users routes (NEW!)
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('users/{user}/toggle-role', [UserController::class, 'toggleRole'])->name('users.toggle-role');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::get('/pay', [PaymentController::class, 'pay']);