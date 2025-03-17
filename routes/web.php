<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
    
    // User profile routes
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/kyc', [UserController::class, 'showKycForm'])->name('profile.kyc');
    Route::post('/profile/kyc', [UserController::class, 'submitKyc'])->name('profile.kyc.submit');
    
    // Asset routes
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
    Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
    Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
    Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
    Route::get('/my-assets', [AssetController::class, 'myAssets'])->name('assets.my');
    
    // Exchange routes
    Route::get('/exchanges', [ExchangeController::class, 'index'])->name('exchanges.index');
    Route::get('/exchanges/create', [ExchangeController::class, 'create'])->name('exchanges.create');
    Route::post('/exchanges', [ExchangeController::class, 'store'])->name('exchanges.store');
    Route::get('/exchanges/{exchange}', [ExchangeController::class, 'show'])->name('exchanges.show');
    Route::post('/exchanges/{exchange}/accept', [ExchangeController::class, 'accept'])->name('exchanges.accept');
    Route::post('/exchanges/{exchange}/reject', [ExchangeController::class, 'reject'])->name('exchanges.reject');
    Route::post('/exchanges/{exchange}/cancel', [ExchangeController::class, 'cancel'])->name('exchanges.cancel');
    Route::post('/exchanges/{exchange}/deposit', [ExchangeController::class, 'deposit'])->name('exchanges.deposit');
    Route::post('/exchanges/{exchange}/complete', [ExchangeController::class, 'complete'])->name('exchanges.complete');
    
    // Transaction routes
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::post('/transactions/{transaction}/payment', [TransactionController::class, 'processPayment'])->name('transactions.payment');
    
    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Admin user management
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
        Route::put('/users/{user}/status', [AdminController::class, 'updateUserStatus'])->name('users.status');
        Route::get('/users/{user}/kyc', [AdminController::class, 'showUserKyc'])->name('users.kyc');
        Route::post('/users/{user}/kyc/approve', [AdminController::class, 'approveKyc'])->name('users.kyc.approve');
        Route::post('/users/{user}/kyc/reject', [AdminController::class, 'rejectKyc'])->name('users.kyc.reject');
        
        // Admin asset management
        Route::get('/assets', [AdminController::class, 'assets'])->name('assets.index');
        Route::get('/assets/pending', [AdminController::class, 'pendingAssets'])->name('assets.pending');
        Route::post('/assets/{asset}/approve', [AssetController::class, 'approve'])->name('assets.approve');
        Route::post('/assets/{asset}/reject', [AssetController::class, 'reject'])->name('assets.reject');
        
        // Admin exchange management
        Route::get('/exchanges', [AdminController::class, 'exchanges'])->name('exchanges.index');
        Route::post('/exchanges/{exchange}/approve', [ExchangeController::class, 'adminApprove'])->name('exchanges.approve');
        Route::post('/exchanges/{exchange}/reject', [ExchangeController::class, 'adminReject'])->name('exchanges.reject');
        
        // Admin transaction management
        Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions.index');
        Route::post('/transactions/{transaction}/approve', [TransactionController::class, 'adminApprove'])->name('transactions.approve');
        Route::post('/transactions/{transaction}/reject', [TransactionController::class, 'adminReject'])->name('transactions.reject');
        
        // Admin settings
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    });
});