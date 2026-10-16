<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GoldRateController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TrayController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes - e-Emas POS & Jewelry Management
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
| Admin & Backoffice Routes (e-Emas Management)
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
