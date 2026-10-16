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

    // 5. Pengaturan Identitas Toko & Surat Emas (Settings)
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
