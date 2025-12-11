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

// Verification Pending (accessible to authenticated but unverified users)
Route::get('/verification/pending', function () {
    return view('auth.verification-pending');
})->middleware('auth')->name('verification.pending');

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
        
        // User Management
        Route::resource('users', \App\Http\Controllers\Admin\UserManagementController::class);
        Route::post('/users/{user}/change-role', [\App\Http\Controllers\Admin\UserManagementController::class, 'changeRole'])->name('users.change-role');
        Route::post('/users/{user}/deactivate', [\App\Http\Controllers\Admin\UserManagementController::class, 'deactivate'])->name('users.deactivate');
        Route::post('/users/{user}/reactivate', [\App\Http\Controllers\Admin\UserManagementController::class, 'reactivate'])->name('users.reactivate');
        Route::post('/users/{user}/reset-password', [\App\Http\Controllers\Admin\UserManagementController::class, 'resetPassword'])->name('users.reset-password');
        
        // Verification
        Route::get('/verifications', [\App\Http\Controllers\Admin\VerificationController::class, 'index'])->name('verifications.index');
        Route::get('/verifications/{user}', [\App\Http\Controllers\Admin\VerificationController::class, 'show'])->name('verifications.show');
        Route::post('/verifications/{user}/approve', [\App\Http\Controllers\Admin\VerificationController::class, 'approve'])->name('verifications.approve');
        Route::post('/verifications/{user}/reject', [\App\Http\Controllers\Admin\VerificationController::class, 'reject'])->name('verifications.reject');
        
        // Analyst Assignments
        Route::get('/assignments', [\App\Http\Controllers\Admin\AssignmentController::class, 'index'])->name('assignments.index');
        Route::post('/assignments/assign', [\App\Http\Controllers\Admin\AssignmentController::class, 'assign'])->name('assignments.assign');
        Route::put('/assignments/{assignment}', [\App\Http\Controllers\Admin\AssignmentController::class, 'reassign'])->name('assignments.reassign');
        Route::delete('/assignments/{assignment}', [\App\Http\Controllers\Admin\AssignmentController::class, 'remove'])->name('assignments.remove');
        
        // Analytics Oversight
        Route::get('/analytics', [\App\Http\Controllers\Admin\AdminAnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/trades', [\App\Http\Controllers\Admin\AdminAnalyticsController::class, 'allTrades'])->name('analytics.trades');
        Route::get('/analytics/traders/{trader}', [\App\Http\Controllers\Admin\AdminAnalyticsController::class, 'traderAnalytics'])->name('analytics.trader');
        
        // Activity Logs
        Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/export', [\App\Http\Controllers\Admin\ActivityLogController::class, 'export'])->name('activity-logs.export');
        
        // Backup Management
        Route::get('/backups', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups/create', [\App\Http\Controllers\Admin\BackupController::class, 'create'])->name('backups.create');
        Route::get('/backups/{filename}/download', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('backups.download');
        Route::post('/backups/{filename}/restore', [\App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('backups.restore');
        Route::delete('/backups/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('backups.destroy');
        
        // System Settings
        Route::get('/settings', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'update'])->name('settings.update');
    });

    // Analyst Routes (requires verification)
    Route::middleware(['role:analyst', \App\Http\Middleware\EnsureVerified::class])->prefix('analyst')->name('analyst.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Analyst\AnalystDashboardController::class, 'index'])->name('dashboard');
        Route::get('/traders/{trader}', [\App\Http\Controllers\Analyst\AnalystDashboardController::class, 'traderProfile'])->name('trader.profile');
        
        // Feedback Management
        Route::get('/feedback/create/{trader}/{trade?}', [\App\Http\Controllers\Analyst\FeedbackController::class, 'create'])->name('feedback.create');
        Route::post('/feedback', [\App\Http\Controllers\Analyst\FeedbackController::class, 'store'])->name('feedback.store');
        Route::get('/feedback/{feedback}/edit', [\App\Http\Controllers\Analyst\FeedbackController::class, 'edit'])->name('feedback.edit');
        Route::put('/feedback/{feedback}', [\App\Http\Controllers\Analyst\FeedbackController::class, 'update'])->name('feedback.update');
        Route::delete('/feedback/{feedback}', [\App\Http\Controllers\Analyst\FeedbackController::class, 'destroy'])->name('feedback.destroy');
    });

    // Notification Routes (for all authenticated users)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Trader Routes (requires verification)
    Route::middleware(['role:trader', \App\Http\Middleware\EnsureVerified::class])->prefix('trader')->name('trader.')->group(function () {
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
