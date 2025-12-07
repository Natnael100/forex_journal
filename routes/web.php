<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Analyst\AnalystDashboardController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Trader\TraderDashboardController;
use Illuminate\Support\Facades\Route;

// Guest Routes (Public)
Route::middleware('guest')->group(function () {
    // Registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Password Reset
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Email Verification
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        // Additional admin routes will be added in future phases
    });

    // Trader Routes
    Route::middleware(['role:trader'])->prefix('trader')->name('trader.')->group(function () {
        Route::get('/dashboard', [TraderDashboardController::class, 'index'])->name('dashboard');
        
        // Trade Management
        Route::resource('trades', \App\Http\Controllers\Trader\TradeController::class);
        
        // Analytics
        Route::get('/analytics', [\App\Http\Controllers\Trader\AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/review', [\App\Http\Controllers\Trader\AnalyticsController::class, 'review'])->name('analytics.review');
    });

    // Analyst Routes
    Route::middleware(['role:analyst'])->prefix('analyst')->name('analyst.')->group(function () {
        Route::get('/dashboard', [AnalystDashboardController::class, 'index'])->name('dashboard');
        // Additional analyst routes will be added in future phases
    });
});

// Welcome page (redirect to login)
Route::get('/', function () {
    return redirect()->route('login');
});
