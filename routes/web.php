<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Reports\Financial\BalanceSheetReportController;
use App\Http\Controllers\Reports\Financial\CashFlowReportController;
use App\Http\Controllers\Reports\Financial\GeneralLedgerReportController;
use App\Http\Controllers\Reports\Financial\ProfitLossReportController;
use App\Http\Controllers\Reports\Financial\TrialBalanceReportController;
use App\Http\Controllers\Reports\Purchase\ApInvoiceReportController;
use App\Http\Controllers\Reports\Purchase\OutgoingPaymentReportController;
use App\Http\Controllers\Reports\Purchase\PurchaseOrderReportController;
use App\Http\Controllers\Reports\Sales\ArInvoiceReportController;
use App\Http\Controllers\Reports\Sales\DepositReportController;
use App\Http\Controllers\Reports\Sales\IncomingPaymentReportController;
use App\Http\Controllers\Reports\Sales\SalesPurchaseOrderReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ThemeDemonstrationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public Guest Routes
Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:login');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Locale Switch Route
Route::get('/locale/{lang}', function (string $lang) {
    if (in_array($lang, ['en', 'km'], true)) {
        session(['locale' => $lang]);
        cookie()->queue(cookie('twa_locale', $lang, 60 * 24 * 365, null, null, false, false));
    }

    return redirect()->back();
})->name('locale.switch');

// Lightweight Session Status Check (for automatic timeout redirect)
Route::get('/auth/session-status', function () {
    return response()->json([
        'authenticated' => auth()->check() && (auth()->user()?->isActive() ?? false),
    ]);
})->name('auth.session-status');

// Protected Routes (Session Authentication + Active Account Verification)
Route::middleware(['auth', 'active'])->group(function (): void {
    // Dashboard
    Route::view('/dashboard', 'dashboard', [
        'navigation' => config('navigation'),
    ])->name('dashboard');

    // Theme & Component Showcase
    Route::view('/theme/components', 'theme.components', [
        'navigation' => config('navigation'),
    ])->name('theme.components');

    Route::get('/theme/listing', [ThemeDemonstrationController::class, 'listing'])->name('theme.listing');
    Route::get('/theme/form', [ThemeDemonstrationController::class, 'form'])->name('theme.form');
    Route::post('/theme/form', [ThemeDemonstrationController::class, 'store'])->name('theme.form.store');

    // User Profile & Settings
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // RBAC: User Management
    Route::patch('/users/{user}/status', [UserController::class, 'toggleStatus'])
        ->middleware('permission:users.status')
        ->name('users.status');

    Route::resource('users', UserController::class);

    // RBAC: Role Management
    Route::resource('roles', RoleController::class);

    // RBAC: Permission Management
    Route::resource('permissions', PermissionController::class);

    // Reports: Purchase
    Route::prefix('reports/purchase')->name('reports.purchase.')->group(function (): void {
        Route::resource('purchase-orders', PurchaseOrderReportController::class)->only(['index', 'show']);
        Route::resource('ap-invoices', ApInvoiceReportController::class)->only(['index', 'show']);
        Route::resource('outgoing-payments', OutgoingPaymentReportController::class)->only(['index', 'show']);
    });

    // Reports: Sales
    Route::prefix('reports/sales')->name('reports.sales.')->group(function (): void {
        Route::resource('purchase-orders', SalesPurchaseOrderReportController::class)->only(['index', 'show']);
        Route::resource('ar-invoices', ArInvoiceReportController::class)->only(['index', 'show']);
        Route::resource('incoming-payments', IncomingPaymentReportController::class)->only(['index', 'show']);
        Route::resource('deposits', DepositReportController::class)->only(['index', 'show']);
    });

    // Reports: Financial
    Route::prefix('reports/financial')->name('reports.financial.')->group(function (): void {
        Route::resource('balance-sheet', BalanceSheetReportController::class)->only(['index', 'show']);
        Route::resource('profit-loss', ProfitLossReportController::class)->only(['index', 'show']);
        Route::resource('cash-flow', CashFlowReportController::class)->only(['index', 'show']);
        Route::resource('trial-balance', TrialBalanceReportController::class)->only(['index', 'show']);
        Route::resource('general-ledger', GeneralLedgerReportController::class)->only(['index', 'show']);
    });
});
