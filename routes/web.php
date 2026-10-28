<?php

use App\Http\Controllers\Administrator\ModuleController;
use App\Http\Controllers\Administrator\NavigationGroupController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\MasterData\BarcodeController;
use App\Http\Controllers\MasterData\BinController;
use App\Http\Controllers\MasterData\BomController;
use App\Http\Controllers\MasterData\BranchController;
use App\Http\Controllers\MasterData\BusinessPartnerController;
use App\Http\Controllers\MasterData\DocumentController;
use App\Http\Controllers\MasterData\DocumentSeriesController;
use App\Http\Controllers\MasterData\ItemClassController;
use App\Http\Controllers\MasterData\ItemController;
use App\Http\Controllers\MasterData\ItemGroupController;
use App\Http\Controllers\MasterData\UomController;
use App\Http\Controllers\MasterData\UomGroupController;
use App\Http\Controllers\MasterData\WarehouseController;
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
use App\Http\Controllers\Reports\Purchase\PurchaseOrderReportController as PurchaseOrderController;
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

Route::get('/diagnostics/php', function () {
    return response()->json([
        'php_version' => PHP_VERSION,
        'php_ini' => php_ini_loaded_file(),
        'pdo_drivers' => PDO::getAvailableDrivers(),
        'odbc_extension' => extension_loaded('odbc'),
        'pdo_odbc_extension' => extension_loaded('pdo_odbc'),
    ]);
});

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
    Route::group(['prefix' => 'users'], function (): void {
        Route::patch('/{user}/status', [UserController::class, 'toggleStatus'])
            ->middleware('permission:users.status')
            ->name('users.status');

        Route::controller(UserController::class)->group(function (): void {
            Route::get('/', 'index')->name('users.index');
            Route::get('/create', 'create')->name('users.create');
            Route::post('/', 'store')->name('users.store');
            Route::get('/{user}', 'show')->name('users.show');
            Route::get('/{user}/edit', 'edit')->name('users.edit');
            Route::match(['put', 'patch'], '/{user}', 'update')->name('users.update');
            Route::delete('/{user}', 'destroy')->name('users.destroy');
        });
    });

    // RBAC: Role Management
    Route::group(['prefix' => 'roles'], function (): void {
        Route::controller(RoleController::class)->group(function (): void {
            Route::get('/', 'index')->name('roles.index');
            Route::get('/create', 'create')->name('roles.create');
            Route::post('/', 'store')->name('roles.store');
            Route::get('/{role}', 'show')->name('roles.show');
            Route::get('/{role}/edit', 'edit')->name('roles.edit');
            Route::match(['put', 'patch'], '/{role}', 'update')->name('roles.update');
            Route::delete('/{role}', 'destroy')->name('roles.destroy');
        });
    });

    // RBAC: Permission Management
    Route::group(['prefix' => 'permissions'], function (): void {
        Route::controller(PermissionController::class)->group(function (): void {
            Route::get('/', 'index')->name('permissions.index');
            Route::get('/create', 'create')->name('permissions.create');
            Route::post('/', 'store')->name('permissions.store');
            Route::get('/{permission}', 'show')->name('permissions.show');
            Route::get('/{permission}/edit', 'edit')->name('permissions.edit');
            Route::match(['put', 'patch'], '/{permission}', 'update')->name('permissions.update');
            Route::delete('/{permission}', 'destroy')->name('permissions.destroy');
        });
    });

    // Master Data (SAP Integration)
    Route::group(['prefix' => 'master-data'], function (): void {
        Route::group(['prefix' => 'branches'], function (): void {
            Route::controller(BranchController::class)->group(function (): void {
                Route::get('/', 'index')->name('master-data.branches.index');
                Route::get('/{branch}', 'show')->name('master-data.branches.show');
            });
        });

        Route::group(['prefix' => 'warehouses'], function (): void {
            Route::controller(WarehouseController::class)->group(function (): void {
                Route::get('/', 'index')->name('master-data.warehouses.index');
                Route::get('/{warehouse}', 'show')->name('master-data.warehouses.show');
            });
        });

        Route::group(['prefix' => 'bins'], function (): void {
            Route::controller(BinController::class)->group(function (): void {
                Route::get('/', 'index')->name('master-data.bins.index');
                Route::get('/{bin}', 'show')->name('master-data.bins.show');
            });
        });

        Route::group(['prefix' => 'business-partners'], function (): void {
            Route::controller(BusinessPartnerController::class)->group(function (): void {
                Route::get('/', 'index')->name('master-data.business-partners.index');
                Route::get('/{business_partner}', 'show')->name('master-data.business-partners.show');
            });
        });

        Route::group(['prefix' => 'items'], function (): void {
            Route::controller(ItemController::class)->group(function (): void {
                Route::get('/', 'index')->name('master-data.items.index');
                Route::get('/{item}', 'show')->name('master-data.items.show');
            });
        });

        Route::group(['prefix' => 'uoms'], function (): void {
            Route::controller(UomController::class)->group(function (): void {
                Route::get('/', 'index')->name('master-data.uoms.index');
                Route::get('/{uom}', 'show')->name('master-data.uoms.show');
            });
        });

        Route::group(['prefix' => 'uom-groups'], function (): void {
            Route::controller(UomGroupController::class)->group(function (): void {
                Route::get('/', 'index')->name('master-data.uom-groups.index');
                Route::get('/{uom_group}', 'show')->name('master-data.uom-groups.show');
            });
        });

        Route::group(['prefix' => 'item-groups'], function (): void {
            Route::controller(ItemGroupController::class)->group(function (): void {
                Route::get('/', 'index')->name('master-data.item-groups.index');
                Route::get('/{item_group}', 'show')->name('master-data.item-groups.show');
            });
        });

        Route::group(['prefix' => 'item-classes'], function (): void {
            Route::controller(ItemClassController::class)->group(function (): void {
                Route::get('/', 'index')->name('master-data.item-classes.index');
                Route::get('/{item_class}', 'show')->name('master-data.item-classes.show');
            });
        });

        Route::group(['prefix' => 'barcodes'], function (): void {
            Route::controller(BarcodeController::class)->group(function (): void {
                Route::get('/', 'index')->name('master-data.barcodes.index');
                Route::get('/{barcode}', 'show')->name('master-data.barcodes.show');
            });
        });

        Route::group(['prefix' => 'boms'], function (): void {
            Route::controller(BomController::class)->group(function (): void {
                Route::get('/', 'index')->name('master-data.boms.index');
                Route::get('/{bom}', 'show')->name('master-data.boms.show');
            });
        });

        Route::group(['prefix' => 'documents'], function (): void {
            Route::controller(DocumentController::class)->group(function (): void {
                Route::get('/', 'index')->name('master-data.documents.index');
                Route::get('/{document}', 'show')->name('master-data.documents.show');
            });
        });

        Route::group(['prefix' => 'document-series'], function (): void {
            Route::controller(DocumentSeriesController::class)->group(function (): void {
                Route::get('/', 'index')->name('master-data.document-series.index');
                Route::get('/{document_series}', 'show')->name('master-data.document-series.show');
            });
        });
    });

    // Purchasing (Nested User Requested Pattern & Route)
    Route::group(['prefix' => 'purchasing'], function (): void {
        Route::group(['prefix' => 'purchase-order'], function (): void {
            Route::controller(PurchaseOrderController::class)->group(function (): void {
                Route::get('/', 'index')->name('purchase.order.index');
                Route::get('/{purchase_order}', 'show')->name('purchase.order.show');
            });
        });

        Route::group(['prefix' => 'ap-invoice'], function (): void {
            Route::controller(ApInvoiceReportController::class)->group(function (): void {
                Route::get('/', 'index')->name('purchase.ap-invoice.index');
                Route::get('/{ap_invoice}', 'show')->name('purchase.ap-invoice.show');
            });
        });

        Route::group(['prefix' => 'outgoing-payment'], function (): void {
            Route::controller(OutgoingPaymentReportController::class)->group(function (): void {
                Route::get('/', 'index')->name('purchase.outgoing-payment.index');
                Route::get('/{outgoing_payment}', 'show')->name('purchase.outgoing-payment.show');
            });
        });
    });

    // Reports: Purchase, Sales, Financial
    Route::group(['prefix' => 'reports'], function (): void {
        // Reports: Purchase
        Route::group(['prefix' => 'purchase'], function (): void {
            Route::group(['prefix' => 'purchase-orders'], function (): void {
                Route::controller(PurchaseOrderReportController::class)->group(function (): void {
                    Route::get('/', 'index')->name('reports.purchase.purchase-orders.index');
                    Route::get('/{purchase_order}', 'show')->name('reports.purchase.purchase-orders.show');
                });
            });

            Route::group(['prefix' => 'ap-invoices'], function (): void {
                Route::controller(ApInvoiceReportController::class)->group(function (): void {
                    Route::get('/', 'index')->name('reports.purchase.ap-invoices.index');
                    Route::get('/{ap_invoice}', 'show')->name('reports.purchase.ap-invoices.show');
                });
            });

            Route::group(['prefix' => 'outgoing-payments'], function (): void {
                Route::controller(OutgoingPaymentReportController::class)->group(function (): void {
                    Route::get('/', 'index')->name('reports.purchase.outgoing-payments.index');
                    Route::get('/{outgoing_payment}', 'show')->name('reports.purchase.outgoing-payments.show');
                });
            });
        });

        // Reports: Sales
        Route::group(['prefix' => 'sales'], function (): void {
            Route::group(['prefix' => 'purchase-orders'], function (): void {
                Route::controller(SalesPurchaseOrderReportController::class)->group(function (): void {
                    Route::get('/', 'index')->name('reports.sales.purchase-orders.index');
                    Route::get('/{purchase_order}', 'show')->name('reports.sales.purchase-orders.show');
                });
            });

            Route::group(['prefix' => 'ar-invoices'], function (): void {
                Route::controller(ArInvoiceReportController::class)->group(function (): void {
                    Route::get('/', 'index')->name('reports.sales.ar-invoices.index');
                    Route::get('/{ar_invoice}', 'show')->name('reports.sales.ar-invoices.show');
                });
            });

            Route::group(['prefix' => 'incoming-payments'], function (): void {
                Route::controller(IncomingPaymentReportController::class)->group(function (): void {
                    Route::get('/', 'index')->name('reports.sales.incoming-payments.index');
                    Route::get('/{incoming_payment}', 'show')->name('reports.sales.incoming-payments.show');
                });
            });

            Route::group(['prefix' => 'deposits'], function (): void {
                Route::controller(DepositReportController::class)->group(function (): void {
                    Route::get('/', 'index')->name('reports.sales.deposits.index');
                    Route::get('/{deposit}', 'show')->name('reports.sales.deposits.show');
                });
            });
        });

        // Reports: Financial
        Route::group(['prefix' => 'financial'], function (): void {
            Route::group(['prefix' => 'balance-sheet'], function (): void {
                Route::controller(BalanceSheetReportController::class)->group(function (): void {
                    Route::get('/', 'index')->name('reports.financial.balance-sheet.index');
                    Route::get('/{balance_sheet}', 'show')->name('reports.financial.balance-sheet.show');
                });
            });

            Route::group(['prefix' => 'profit-loss'], function (): void {
                Route::controller(ProfitLossReportController::class)->group(function (): void {
                    Route::get('/', 'index')->name('reports.financial.profit-loss.index');
                    Route::get('/{profit_loss}', 'show')->name('reports.financial.profit-loss.show');
                });
            });

            Route::group(['prefix' => 'cash-flow'], function (): void {
                Route::controller(CashFlowReportController::class)->group(function (): void {
                    Route::get('/', 'index')->name('reports.financial.cash-flow.index');
                    Route::get('/{cash_flow}', 'show')->name('reports.financial.cash-flow.show');
                });
            });

            Route::group(['prefix' => 'trial-balance'], function (): void {
                Route::controller(TrialBalanceReportController::class)->group(function (): void {
                    Route::get('/', 'index')->name('reports.financial.trial-balance.index');
                    Route::get('/{trial_balance}', 'show')->name('reports.financial.trial-balance.show');
                });
            });

            Route::group(['prefix' => 'general-ledger'], function (): void {
                Route::controller(GeneralLedgerReportController::class)->group(function (): void {
                    Route::get('/', 'index')->name('reports.financial.general-ledger.index');
                    Route::get('/{general_ledger}', 'show')->name('reports.financial.general-ledger.show');
                });
            });
        });
    });

    // Administration: Modules & Navigation Groups
    Route::group(['prefix' => 'admin'], function (): void {
        Route::group(['prefix' => 'navigation-groups'], function (): void {
            Route::controller(NavigationGroupController::class)->group(function (): void {
                Route::get('/', 'index')->name('admin.navigation-groups.index');
                Route::post('/', 'store')->name('admin.navigation-groups.store');
                Route::post('/reorder', 'reorder')->name('admin.navigation-groups.reorder');
                Route::get('/{navigation_group}', 'show')->name('admin.navigation-groups.show');
                Route::match(['put', 'patch'], '/{navigation_group}', 'update')->name('admin.navigation-groups.update');
                Route::delete('/{navigation_group}', 'destroy')->name('admin.navigation-groups.destroy');
            });
        });

        Route::group(['prefix' => 'modules'], function (): void {
            Route::controller(ModuleController::class)->group(function (): void {
                Route::get('/', 'index')->name('admin.modules.index');
                Route::get('/create', 'create')->name('admin.modules.create');
                Route::post('/', 'store')->name('admin.modules.store');
                Route::post('/reorder', 'reorder')->name('admin.modules.reorder');
                Route::patch('/{module}/status', 'toggleStatus')->name('admin.modules.status');
                Route::get('/{module}', 'show')->name('admin.modules.show');
                Route::get('/{module}/edit', 'edit')->name('admin.modules.edit');
                Route::match(['put', 'patch'], '/{module}', 'update')->name('admin.modules.update');
                Route::delete('/{module}', 'destroy')->name('admin.modules.destroy');
            });
        });
    });
});
