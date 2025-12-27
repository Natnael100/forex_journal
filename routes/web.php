<?php

use Illuminate\Support\Facades\Route;

// Auth
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;

// General
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\VerificationController as AdminVerificationController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\AdminAnalystRequestController;
use App\Http\Controllers\Admin\AdminAnalyticsController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\SystemSettingsController;

// Analyst Controllers
use App\Http\Controllers\Analyst\AnalystDashboardController;
use App\Http\Controllers\Analyst\FeedbackController as AnalystFeedbackController;
use App\Http\Controllers\Analyst\FeedbackTemplateController;

// Trader Controllers
use App\Http\Controllers\Trader\TraderDashboardController;
use App\Http\Controllers\Trader\TraderAnalystRequestController;
use App\Http\Controllers\Trader\TradeController;
use App\Http\Controllers\Trader\StrategyController;
use App\Http\Controllers\Trader\TradeAccountController;
use App\Http\Controllers\Trader\AnalyticsController as TraderAnalyticsController;
use App\Http\Controllers\Trader\FeedbackController as TraderFeedbackController;
use App\Http\Controllers\Trader\AchievementController;
use App\Http\Controllers\Trader\LeaderboardController;

// Middleware
use App\Http\Middleware\EnsureVerified;

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

// Public Profile View (accessible to all)
Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('profile.show');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Analyst Assignment Requests
    Route::get('/analyst/request', [TraderAnalystRequestController::class, 'create'])->name('trader.analyst-request.create');
    Route::post('/analyst/request', [TraderAnalystRequestController::class, 'store'])->name('trader.analyst-request.store');
    Route::delete('/analyst/request/{analystRequest}', [TraderAnalystRequestController::class, 'cancel'])->name('trader.analyst-request.cancel');
    Route::get('/analyst/consent/{analystRequest}', [TraderAnalystRequestController::class, 'showConsent'])->name('trader.analyst-request.consent');
    Route::post('/analyst/consent/{analystRequest}', [TraderAnalystRequestController::class, 'processConsent'])->name('trader.analyst-request.process-consent');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Email Verification
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Notification Routes (Authenticated Users)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Profile Routes (Authenticated Users)
    Route::get('/settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/settings/profile/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.upload-photo');
    Route::post('/settings/profile/cover', [ProfileController::class, 'uploadCover'])->name('profile.upload-cover');
    Route::delete('/settings/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.delete-photo');
    Route::delete('/settings/profile/cover', [ProfileController::class, 'deleteCover'])->name('profile.delete-cover');

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // User Management
        Route::resource('users', UserManagementController::class);
        Route::post('/users/{user}/change-role', [UserManagementController::class, 'changeRole'])->name('users.change-role');
        Route::post('/users/{user}/deactivate', [UserManagementController::class, 'deactivate'])->name('users.deactivate');
        Route::post('/users/{user}/reactivate', [UserManagementController::class, 'reactivate'])->name('users.reactivate');
        Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('/users/{user}/reset-profile-photo', [UserManagementController::class, 'resetProfilePhoto'])->name('users.reset-profile-photo');
        Route::post('/users/{user}/reset-cover-photo', [UserManagementController::class, 'resetCoverPhoto'])->name('users.reset-cover-photo');
        Route::post('/users/{user}/update-username', [UserManagementController::class, 'updateUsername'])->name('users.update-username');
        Route::post('/users/{user}/moderate-bio', [UserManagementController::class, 'moderateBio'])->name('users.moderate-bio');
        Route::post('/users/{user}/toggle-verification', [UserManagementController::class, 'toggleVerification'])->name('users.toggle-verification');
        
        // Verification
        Route::get('/verifications', [AdminVerificationController::class, 'index'])->name('verifications.index');
        Route::get('/verifications/{user}', [AdminVerificationController::class, 'show'])->name('verifications.show');
        Route::post('/verifications/{user}/approve', [AdminVerificationController::class, 'approve'])->name('verifications.approve');
        Route::post('/verifications/{user}/reject', [AdminVerificationController::class, 'reject'])->name('verifications.reject');
        
        // Analyst Assignments
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::post('/assignments/assign', [AssignmentController::class, 'assign'])->name('assignments.assign');
        Route::put('/assignments/{assignment}', [AssignmentController::class, 'reassign'])->name('assignments.reassign');
        Route::delete('/assignments/{assignment}', [AssignmentController::class, 'remove'])->name('assignments.remove');
        
        // Analyst Assignment Requests
        Route::get('/assignments/requests', [AdminAnalystRequestController::class, 'index'])->name('assignments.requests.index');
        Route::put('/assignments/requests/{analystRequest}', [AdminAnalystRequestController::class, 'update'])->name('assignments.requests.update');
        
        // Analytics Oversight
        Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/trades', [AdminAnalyticsController::class, 'allTrades'])->name('analytics.trades');
        Route::get('/analytics/traders/{trader}', [AdminAnalyticsController::class, 'traderAnalytics'])->name('analytics.trader');
        
        // Activity Logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
        
        // Backup Management
        Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups/create', [BackupController::class, 'create'])->name('backups.create');
        Route::get('/backups/{filename}/download', [BackupController::class, 'download'])->name('backups.download');
        Route::post('/backups/{filename}/restore', [BackupController::class, 'restore'])->name('backups.restore');
        Route::delete('/backups/{filename}', [BackupController::class, 'destroy'])->name('backups.destroy');
        
        // System Settings
        Route::get('/settings', [SystemSettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SystemSettingsController::class, 'update'])->name('settings.update');
    });

    // Analyst Routes (requires verification)
    Route::middleware(['role:analyst', EnsureVerified::class])->prefix('analyst')->name('analyst.')->group(function () {
        Route::get('/dashboard', [AnalystDashboardController::class, 'index'])->name('dashboard');
        Route::get('/traders/{trader}', [AnalystDashboardController::class, 'traderProfile'])->name('trader.profile');
        Route::post('/traders/{trader}/simulate', [AnalystDashboardController::class, 'simulate'])->name('trader.simulate');
        
        // Governance (Phase 6)
        Route::post('/traders/{trader}/focus', [AnalystDashboardController::class, 'updateFocus'])->name('trader.update-focus');
        Route::post('/traders/{trader}/rules', [AnalystDashboardController::class, 'storeRule'])->name('trader.rules.store');
        Route::delete('/rules/{rule}', [AnalystDashboardController::class, 'deleteRule'])->name('rules.destroy');
        
        // Feedback Management (Using POST for creation because of complex form, but conventionally GET create)
        Route::get('/feedback/create/{trader}/{trade?}', [AnalystFeedbackController::class, 'create'])->name('feedback.create');
        Route::post('/feedback', [AnalystFeedbackController::class, 'store'])->name('feedback.store');
        
        // AI Draft Generation
        Route::post('/feedback/{trader}/generate-draft', [AnalystFeedbackController::class, 'generateDraft'])->name('feedback.generate-draft');
        
        Route::get('/feedback/{feedback}/edit', [AnalystFeedbackController::class, 'edit'])->name('feedback.edit');
        Route::put('/feedback/{feedback}', [AnalystFeedbackController::class, 'update'])->name('feedback.update');
        Route::delete('/feedback/{feedback}', [AnalystFeedbackController::class, 'destroy'])->name('feedback.destroy');
        
        // Feedback Templates
        Route::resource('templates', FeedbackTemplateController::class);
    });

    // Trader Routes (requires verification)
    Route::middleware(['role:trader', EnsureVerified::class])->prefix('trader')->name('trader.')->group(function () {
        Route::get('/dashboard', [TraderDashboardController::class, 'index'])->name('dashboard');
        
        // Trade Management
        Route::resource('trades', TradeController::class);
        
        // Strategies
        Route::resource('strategies', StrategyController::class);
        
        // Trade Accounts
        Route::resource('accounts', TradeAccountController::class);
        Route::post('/accounts/{account}/transaction', [TradeAccountController::class, 'addTransaction'])
            ->name('accounts.transaction');
        
        // Analytics
        Route::get('/analytics', [TraderAnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/review', [TraderAnalyticsController::class, 'review'])->name('analytics.review');
        Route::get('/analytics/pair/{pair}', [TraderAnalyticsController::class, 'pairAnalysis'])->name('analytics.pair');
        
        // Feedback
        Route::get('/feedback', [TraderFeedbackController::class, 'index'])->name('feedback.index');
        Route::get('/feedback/{feedback}', [TraderFeedbackController::class, 'show'])->name('feedback.show');
        
        // Trading Tools
        Route::get('/tools', function () {
            return view('trader.tools.index');
        })->name('tools.index');
        
        // Achievements
        Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
        
        // Leaderboard
        Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
    });

});

// Welcome page (redirect to login)
Route::get('/', function () {
    return redirect()->route('login');
});

