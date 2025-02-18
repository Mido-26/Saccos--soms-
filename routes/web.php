<?php

use Illuminate\Http\Request;
use App\Http\Middleware\CheckConfigs;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoansController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SavingsController;
use App\Http\Middleware\CheckAccountStatus;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\LoanRepaymentsController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


Route::middleware(CheckConfigs::class)->group( function(){

    Route::get('/email/verify', action: function(){
        return view('auth.email-verification');
    })->middleware('auth')->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        // Mark the user's email address as verified
        $request->fulfill();
        return redirect('/dashboard'); 
    })->middleware(['auth', 'signed'])->name('verification.verify');


    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Verification email sent!');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');


    Route::middleware(['auth','verified', CheckAccountStatus::class])->group(function () {
        // logout route
        Route::get('logout', [LoginController::class, 'logout'])->name('logout');
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        
        // Authenticated routes
        Route::get('profile', [ProfileController::class, 'index'])->name('profile');
        Route::patch('profile/{user}', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::patch('profile/{user}/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
        
        // Dashboard routes
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::Post('dashboard', [DashboardController::class, 'switchUser'])->name('dashboard');

        // transactions routes
        Route::get('transactions/user/{user?}', [TransactionsController::class, 'index'])->name('transactions.user');
        Route::resource('transactions', TransactionsController::class);

        // User management routes
        Route::middleware([RoleMiddleware::class . ':admin,staff'])->group( function () : void {
            Route::resource('savings', SavingsController::class);
            Route::resource('users', UserController::class);
            Route::patch('users/{user}/{todo}', [UserController::class, 'update'])->name('users.updateAction'); 
            // Route::resource('loan-categories', LoansCartController::class);   
        });

        // Notifications routes
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index'); // Shows the notifications page
        Route::get('/notifications/fetch', [NotificationController::class, 'fetchNotifications'])->name('notifications.fetch'); // Fetch unread notifications
        Route::get('/notifications/mark-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::get('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
        Route::get('/notifications/clear-all', [NotificationController::class, 'destroy'])->name('notifications.clearAll');

        // Loans routes
        // Payment route
        Route::post('/loan-repayments/{repayment}/pay', [LoanRepaymentsController::class, 'markAsPaid'])
        ->name('loan-repayments.pay');

        // Export route
        Route::get('/loans/{loan}/installments/export/{type}', [LoanRepaymentsController::class, 'exportInstallments'])
        ->name('loans.installments.export');
        Route::get('/loans/{loan}/installments', [LoanRepaymentsController::class, 'show'])->name('loans.installments');
        Route::get('/loans/status/{status}', [LoansController::class, 'index'])->name('loans.status');
        Route::resource('loans', LoansController::class);
        Route::get('/loans/user/{user}', [LoansController::class, 'index'])->name('loans.user');
        Route::patch('/loans/{loan}/{action}/{referee?}/', [LoansController::class, 'updateStatus'])->name('loans.updateStatus');

        // Settings routes
        Route::get('/unauthorized', function () {
            return view('/errors/403');
        })->name('unauthorized');
        // Route for the Settings page (GET request)

    // // Route for handling form submissions or other actions (POST request)
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    //     // Monthly Reports
    // Route::get('/reports/monthly', [ReportsController::class, 'monthlyReports'])->name('reports.monthly');

    // // Annual Reports
    // Route::get('/reports/annual', [ReportsController::class, 'annualReports'])->name('reports.annual');

    // // Custom Reports
    // Route::get('/reports/custom', [ReportsController::class, 'customReports'])->name('reports.custom');
    Route::get('/development-not-available', function() {
        return view('/errors/inprogress');
    })->name('inprogress');
    });

    Route::middleware('auth')->group( function(){
        Route::get('/inactive', function () {
            return view('inactive');
        })->name('inactive');
        
        Route::get('/support', function () {
            return view('/errors/support');
        })->name('support');
        
    });

    // Login routes
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('forgot-password', [LoginController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [LoginController::class, 'sendResetLinkEmail'])->name('password.email');
    // Show reset password form
    Route::get('reset-password/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
    // Handle password reset submission
    Route::post('reset-password', [LoginController::class, 'reset'])->name('password.update');

    // reports Routes
    Route::get('/reports/index', [ReportsController::class, 'index'])->name('reports.index');
    // Savings Report
    Route::get('reports/savings', [ReportsController::class, 'index'])->name('reports.savings');
    Route::get('reports/savings', [ReportsController::class, 'generateSavings'])->name('reports.savings.generate');
    Route::get('reports/savings/download', [ReportsController::class, 'downloadSavings'])->name('reports.savings.download');

    // Loans Report
    Route::get('reports/loans', [ReportsController::class, 'generateLoans'])->name('reports.loans.generate');
    Route::get('reports/loans/download', [ReportsController::class, 'downloadLoans'])->name('reports.loans.download');

    // Transactions Report
    Route::get('reports/transactions', [ReportsController::class, 'generateTransactions'])->name('reports.transactions.generate');
    Route::get('reports/transactions/download', [ReportsController::class, 'downloadTransactions'])->name('reports.transactions.download');

    // Members Report
    Route::get('reports/members', [ReportsController::class, 'generateMembers'])->name('reports.members.generate');
    Route::get('reports/members/download', [ReportsController::class, 'downloadMembers'])->name('reports.members.download');

    // Penalties Report
    Route::get('reports/penalties', [ReportsController::class, 'generatePenalties'])->name('reports.penalties.generate');
    Route::get('reports/penalties/download', [ReportsController::class, 'downloadPenalties'])->name('reports.penalties.download');

    // Dividends Report
    Route::get('reports/dividends', [ReportsController::class, 'generateDividends'])->name('reports.dividends.generate');
    Route::get('reports/dividends/download', [ReportsController::class, 'downloadDividends'])->name('reports.dividends.download');


    // settings route
    Route::post('/settings/{settings}', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/settings/', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/{settings}', [SettingsController::class, 'edit'])->name('settings.edit');
});

Route::post('/settings/store', [SettingsController::class, 'store'])->name('settings.store');
Route::get('config' ,function(){
    return view('config.index');
})->name('config');
Route::get('config/create' ,function(){
    return view('config.create');
});