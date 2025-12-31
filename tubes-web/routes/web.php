<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\VoucherCodeController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\User\UserTransactionController;
use App\Http\Controllers\MidtransController;
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

// Public product routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/api/products/search', [ProductController::class, 'search'])->name('products.search');

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

    // Transactions (alternative routes from fe branch)
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.list');
    Route::get('/transactions/create/{product}', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::post('/transactions/apply-voucher', [TransactionController::class, 'applyVoucher'])->name('transactions.apply-voucher');
    Route::delete('/transactions/{transactionCode}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
    Route::get('/transactions/{transactionCode}', [TransactionController::class, 'detail'])->name('transactions.detail');
    Route::get('/transactions/{transactionCode}/invoice', [TransactionController::class, 'downloadInvoice'])->name('transactions.invoice');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/profile/vouchers', [ProfileController::class, 'vouchers'])->name('profile.vouchers');
});

// Midtrans payment callback (no auth required)
Route::post('/payment/midtrans/callback', [TransactionController::class, 'midtransCallback'])->name('payment.midtrans.callback');

// Admin only routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Categories routes
    Route::resource('categories', CategoryController::class);
    Route::post('categories/{category}/toggle', [CategoryController::class, 'toggleActive'])->name('categories.toggle');

    // Products routes
    Route::resource('products', AdminProductController::class);
    Route::post('products/{product}/toggle', [AdminProductController::class, 'toggleActive'])->name('products.toggle');

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

    // Sliders routes
    Route::resource('sliders', SliderController::class);
    Route::post('sliders/{slider}/toggle', [SliderController::class, 'toggleActive'])->name('sliders.toggle');
});

// Payment route (from second branch)
Route::get('/pay', [PaymentController::class, 'pay']);
