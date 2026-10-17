<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\BuybackDeductionController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GoldRateController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StockMovementController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\TrayController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\CashBookController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\DebtController;
use App\Http\Controllers\Admin\FinanceReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes - POS Emas & Jewelry Management
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Authenticated User & POS Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin & Backoffice Routes (POS Emas Management)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // 1. Dashboard Ringkasan
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Kurs Harga Emas Harian (Live Gold Rates)
    Route::get('/gold-rates', [GoldRateController::class, 'index'])->name('gold-rates.index');
    Route::post('/gold-rates/update-prices', [GoldRateController::class, 'updatePrices'])->name('gold-rates.update-prices');

    // 3. Manajemen Baki Etalase (Trays)
    Route::get('/trays', [TrayController::class, 'index'])->name('trays.index');
    Route::post('/trays', [TrayController::class, 'store'])->name('trays.store');
    Route::get('/trays/{tray}', [TrayController::class, 'show'])->name('trays.show');
    Route::put('/trays/{tray}', [TrayController::class, 'update'])->name('trays.update');
    Route::post('/trays/move-item', [TrayController::class, 'moveItem'])->name('trays.move-item');
    Route::delete('/trays/{tray}', [TrayController::class, 'destroy'])->name('trays.destroy');

    // 4. Katalog Inventaris Perhiasan (Items & Barcode)
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    Route::get('/items/{item}/print-tag', [ItemController::class, 'printTag'])->name('items.print-tag');

    // 4b. Master Persediaan Sitokmas (Brand, Unit, Warehouse)
    Route::resource('brands', BrandController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('units', UnitController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('warehouses', WarehouseController::class)->only(['index', 'store', 'update', 'destroy']);

    // 4c. Mutasi Barang & Keluar Masuk Barang
    Route::get('/inventory/mutations', [StockMovementController::class, 'mutations'])->name('inventory.mutations');
    Route::get('/inventory/movements', [StockMovementController::class, 'movements'])->name('inventory.movements');
    Route::post('/inventory/movements', [StockMovementController::class, 'storeMovement'])->name('inventory.movements.store');

    // 4d. Modul Pelanggan & Point Sitokmas (FASE 2)
    Route::get('/customers/export', [CustomerController::class, 'export'])->name('customers.export');
    Route::get('/customers/{customer}/transactions', [CustomerController::class, 'transactions'])->name('customers.transactions');
    Route::post('/customers/{customer}/redeem-reward', [CustomerController::class, 'redeemReward'])->name('customers.redeem-reward');
    Route::get('/customers/{customer}/print-card', [CustomerController::class, 'printCard'])->name('customers.print-card');
    Route::resource('customers', CustomerController::class);

    // 5. Kasir POS Penjualan & Cetak Surat Emas (FASE 3)
    Route::get('/pos', [\App\Http\Controllers\Admin\PosController::class, 'index'])->name('pos.index');
    Route::post('/pos', [\App\Http\Controllers\Admin\PosController::class, 'store'])->name('pos.store');
    Route::get('/pos/invoice/{sale}', [\App\Http\Controllers\Admin\PosController::class, 'invoice'])->name('pos.invoice');
    Route::get('/pos/certificate/{sale}', [\App\Http\Controllers\Admin\PosController::class, 'certificate'])->name('pos.certificate');

    // 6. Master Bank & Pembayaran Kasir (Ref: SITOKMAS)
    Route::post('/bank-accounts', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:100',
            'account_holder' => 'required|string|max:255',
            'notes' => 'nullable|string|max:255',
        ]);
        \App\Models\BankAccount::create($validated);
        return back()->with('success', 'Rekening bank pembayaran berhasil ditambahkan!');
    })->name('bank-accounts.store');

    Route::delete('/bank-accounts/{bankAccount}', function (\App\Models\BankAccount $bankAccount) {
        $bankAccount->delete();
        return back()->with('success', 'Rekening bank berhasil dihapus!');
    })->name('bank-accounts.destroy');

    // 7. Modul Buyback & Beli Kembali Emas (FASE 4)
    Route::get('/buybacks', [\App\Http\Controllers\Admin\BuybackController::class, 'index'])->name('buybacks.index');
    Route::get('/buybacks/create', [\App\Http\Controllers\Admin\BuybackController::class, 'create'])->name('buybacks.create');
    Route::get('/buybacks/search-sale', [\App\Http\Controllers\Admin\BuybackController::class, 'searchSale'])->name('buybacks.search-sale');
    Route::post('/buybacks', [\App\Http\Controllers\Admin\BuybackController::class, 'store'])->name('buybacks.store');
    Route::get('/buybacks/{buyback}/receipt', [\App\Http\Controllers\Admin\BuybackController::class, 'receipt'])->name('buybacks.receipt');

    // 7b. Modul Pembelian & Supplier Sitokmas (FASE 3)
    Route::resource('suppliers', SupplierController::class);
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::resource('buyback-deductions', BuybackDeductionController::class)->only(['index', 'store', 'update', 'destroy']);

    // 7c. Modul Transaksi Khusus Toko Emas Sitokmas (FASE 4)
    Route::resource('pawns', \App\Http\Controllers\Admin\PawnController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::post('/pawns/{pawn}/redeem', [\App\Http\Controllers\Admin\PawnController::class, 'redeem'])->name('pawns.redeem');
    
    Route::resource('services', \App\Http\Controllers\Admin\JewelryServiceController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::patch('/services/{service}/status', [\App\Http\Controllers\Admin\JewelryServiceController::class, 'updateStatus'])->name('services.update-status');

    Route::resource('tray-scales', \App\Http\Controllers\Admin\TrayScaleController::class)->only(['index', 'create', 'store']);

    // 7d. Modul Pegawai & Penggajian Toko Emas (FASE 5)
    Route::resource('employees', EmployeeController::class);
    Route::resource('payrolls', PayrollController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    // 7e. Modul Keuangan & Buku Kas Toko Emas (FASE 6)
    Route::resource('cash-book', CashBookController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::resource('expenses', ExpenseController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('/debts', [DebtController::class, 'index'])->name('debts.index');
    Route::post('/debts/{debt}/pay', [DebtController::class, 'pay'])->name('debts.pay');
    Route::delete('/debts/{debt}', [DebtController::class, 'destroy'])->name('debts.destroy');
    Route::get('/finance/profit-loss', [FinanceReportController::class, 'profitAndLoss'])->name('finance.profit-loss');
    Route::get('/finance/tax-report', [FinanceReportController::class, 'taxReport'])->name('finance.tax-report');

    // 8. Stock Opname & Audit Baki (FASE 5)
    Route::get('/stock-audits', [\App\Http\Controllers\Admin\StockAuditController::class, 'index'])->name('stock-audits.index');
    Route::post('/stock-audits', [\App\Http\Controllers\Admin\StockAuditController::class, 'store'])->name('stock-audits.store');
    Route::get('/stock-audits/{stockAudit}', [\App\Http\Controllers\Admin\StockAuditController::class, 'show'])->name('stock-audits.show');
    Route::post('/stock-audits/{stockAudit}/scan', [\App\Http\Controllers\Admin\StockAuditController::class, 'scan'])->name('stock-audits.scan');
    Route::post('/stock-audits/{stockAudit}/complete', [\App\Http\Controllers\Admin\StockAuditController::class, 'complete'])->name('stock-audits.complete');

    // 9. Laporan & Statistik Penjualan Realtime (Ref: SITOKMAS - FASE 5)
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');

    // 10. Pengaturan Identitas Toko & Surat Emas (Settings)
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
