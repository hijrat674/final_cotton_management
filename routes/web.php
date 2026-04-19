<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Accounting\AccountController;
use App\Http\Controllers\Accounting\JournalEntryController;
use App\Http\Controllers\Accounting\ReportController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Cotton\CottonEntryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\Inventory\InventoryItemController;
use App\Http\Controllers\Inventory\InventoryTransactionController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Production\ProductionStageController;
use App\Http\Controllers\Payroll\EmployeeAdvanceController;
use App\Http\Controllers\Payroll\SalaryPaymentController;
use App\Http\Controllers\Payroll\SalaryRecordController;
use App\Http\Controllers\Sales\CustomerController;
use App\Http\Controllers\Sales\SaleController;
use App\Http\Controllers\Sales\SalePaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::get('/language/{locale}', [LocaleController::class, 'update'])->name('language.switch');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::middleware('role:admin,manager,production,sales')->group(function () {
        Route::resource('inventory-items', InventoryItemController::class)
            ->only(['index', 'show']);

        Route::resource('inventory-transactions', InventoryTransactionController::class)
            ->only(['index', 'show']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('inventory-items', InventoryItemController::class)
            ->only(['create', 'store', 'edit', 'update', 'destroy']);
    });

    Route::middleware('role:admin,production')->group(function () {
        Route::resource('inventory-transactions', InventoryTransactionController::class)
            ->only(['create', 'store']);
    });

    Route::prefix('cotton-entries')->group(function () {
        Route::middleware('role:admin,production')->group(function () {
            Route::get('/create', [CottonEntryController::class, 'create'])->name('cotton-entries.create');
            Route::post('/', [CottonEntryController::class, 'store'])->name('cotton-entries.store');
        });

        Route::middleware('role:admin,manager,production')->group(function () {
            Route::get('/', [CottonEntryController::class, 'index'])->name('cotton-entries.index');
            Route::get('/{cotton_entry}', [CottonEntryController::class, 'show'])->name('cotton-entries.show');
        });

        Route::middleware('role:admin')->group(function () {
            Route::get('/{cotton_entry}/edit', [CottonEntryController::class, 'edit'])->name('cotton-entries.edit');
            Route::put('/{cotton_entry}', [CottonEntryController::class, 'update'])->name('cotton-entries.update');
            Route::delete('/{cotton_entry}', [CottonEntryController::class, 'destroy'])->name('cotton-entries.destroy');
        });
    });

    Route::prefix('production-stages')->group(function () {
        Route::middleware('role:admin,production')->group(function () {
            Route::get('/create', [ProductionStageController::class, 'create'])->name('production-stages.create');
            Route::post('/', [ProductionStageController::class, 'store'])->name('production-stages.store');
        });

        Route::middleware('role:admin,manager,production')->group(function () {
            Route::get('/', [ProductionStageController::class, 'index'])->name('production-stages.index');
            Route::get('/{productionStage}', [ProductionStageController::class, 'show'])->name('production-stages.show');
        });

        Route::middleware('role:admin')->group(function () {
            Route::get('/{productionStage}/edit', [ProductionStageController::class, 'edit'])->name('production-stages.edit');
            Route::put('/{productionStage}', [ProductionStageController::class, 'update'])->name('production-stages.update');
            Route::delete('/{productionStage}', [ProductionStageController::class, 'destroy'])->name('production-stages.destroy');
        });
    });

    Route::prefix('expenses')->group(function () {
        Route::middleware('role:admin,production')->group(function () {
            Route::get('/create', [ExpenseController::class, 'create'])->name('expenses.create');
            Route::post('/', [ExpenseController::class, 'store'])->name('expenses.store');
        });

        Route::middleware('role:admin,manager,production')->group(function () {
            Route::get('/', [ExpenseController::class, 'index'])->name('expenses.index');
            Route::get('/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');
        });

        Route::middleware('role:admin')->group(function () {
            Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
            Route::put('/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
            Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
        });
    });

    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::patch('/users/{user}/status', [UserController::class, 'toggleStatus'])->name('users.status');
        Route::patch('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.password.update');
        Route::resource('users', UserController::class);
    });

    Route::get('/customers', [CustomerController::class, 'index'])
        ->middleware('role:admin,manager,sales')
        ->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])
        ->middleware('role:admin,sales')
        ->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])
        ->middleware('role:admin,sales')
        ->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])
        ->middleware('role:admin,manager,sales')
        ->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])
        ->middleware('role:admin,sales')
        ->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])
        ->middleware('role:admin,sales')
        ->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('customers.destroy');

    Route::get('/sales', [SaleController::class, 'index'])
        ->middleware('role:admin,manager,sales')
        ->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])
        ->middleware('role:admin,sales')
        ->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])
        ->middleware('role:admin,sales')
        ->name('sales.store');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])
        ->middleware('role:admin,manager,sales')
        ->name('sales.show');
    Route::get('/sales/{sale}/edit', [SaleController::class, 'edit'])
        ->middleware('role:admin,sales')
        ->name('sales.edit');
    Route::put('/sales/{sale}', [SaleController::class, 'update'])
        ->middleware('role:admin,sales')
        ->name('sales.update');
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('sales.destroy');

    Route::get('/sales/{sale}/payments/create', [SalePaymentController::class, 'create'])
        ->middleware('role:admin,sales')
        ->name('sale-payments.create');
    Route::post('/sale-payments', [SalePaymentController::class, 'store'])
        ->middleware('role:admin,sales')
        ->name('sale-payments.store');

    Route::get('/employees', [EmployeeController::class, 'index'])
        ->middleware('role:admin,manager,production')
        ->name('employees.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])
        ->middleware('role:admin')
        ->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])
        ->middleware('role:admin')
        ->name('employees.store');
    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])
        ->middleware('role:admin,manager,production')
        ->name('employees.show');
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])
        ->middleware('role:admin')
        ->name('employees.edit');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])
        ->middleware('role:admin')
        ->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('employees.destroy');

    Route::get('/salary-records', [SalaryRecordController::class, 'index'])
        ->middleware('role:admin,manager')
        ->name('salary-records.index');
    Route::get('/salary-records/create', [SalaryRecordController::class, 'create'])
        ->middleware('role:admin')
        ->name('salary-records.create');
    Route::post('/salary-records', [SalaryRecordController::class, 'store'])
        ->middleware('role:admin')
        ->name('salary-records.store');
    Route::get('/salary-records/{salaryRecord}', [SalaryRecordController::class, 'show'])
        ->middleware('role:admin,manager')
        ->name('salary-records.show');

    Route::post('/salary-payments', [SalaryPaymentController::class, 'store'])
        ->middleware('role:admin')
        ->name('salary-payments.store');

    Route::get('/employee-advances', [EmployeeAdvanceController::class, 'index'])
        ->middleware('role:admin,manager')
        ->name('employee-advances.index');
    Route::get('/employee-advances/create', [EmployeeAdvanceController::class, 'create'])
        ->middleware('role:admin')
        ->name('employee-advances.create');
    Route::post('/employee-advances', [EmployeeAdvanceController::class, 'store'])
        ->middleware('role:admin')
        ->name('employee-advances.store');
    Route::get('/employee-advances/{employeeAdvance}', [EmployeeAdvanceController::class, 'show'])
        ->middleware('role:admin,manager')
        ->name('employee-advances.show');

    Route::prefix('accounting')->middleware('role:admin,manager')->group(function () {
        Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('accounting.dashboard');
        Route::get('/reports/profit-loss', [ReportController::class, 'profitAndLoss'])->name('accounting.reports.profit-loss');
        Route::get('/reports/cash-summary', [ReportController::class, 'cashSummary'])->name('accounting.reports.cash-summary');
        Route::get('/reports/receivables', [ReportController::class, 'receivables'])->name('accounting.reports.receivables');
        Route::get('/reports/general-ledger', [ReportController::class, 'generalLedger'])->name('accounting.reports.general-ledger');
        Route::get('/reports/trial-balance', [ReportController::class, 'trialBalance'])->name('accounting.reports.trial-balance');
        Route::get('/accounts', [AccountController::class, 'index'])->name('accounting.accounts.index');
        Route::get('/journal-entries', [JournalEntryController::class, 'index'])->name('accounting.journal-entries.index');
        Route::get('/journal-entries/{journalEntry}', [JournalEntryController::class, 'show'])->name('accounting.journal-entries.show');
    });
});
