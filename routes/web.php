<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OperationalController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\POController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\BBMController;
use Illuminate\Support\Facades\Route;

// ── Auth ────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ── Protected Routes ────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('/', fn() => redirect()->route('dashboard'));

    // Operasional BBM
    Route::prefix('operational')->name('operational.')->group(function () {
        Route::get('/', [OperationalController::class, 'index'])
            ->name('index');
        Route::get('/create', [OperationalController::class, 'create'])
            ->name('create');
        Route::post('/', [OperationalController::class, 'store'])
            ->name('store');
        Route::get('/{transaksiBBM}', [OperationalController::class, 'show'])
            ->name('show');
        Route::get('/monitoring/odometer', [OperationalController::class, 'monitoringOdometer'])
            ->name('odometer');
        Route::get('/kendaraan/{id}/info', [OperationalController::class, 'getKendaraanInfo'])
            ->name('kendaraan-info');
    });

    // Gudang
    Route::prefix('gudang')->name('gudang.')->group(function () {
        Route::get('/', [GudangController::class, 'index'])
            ->name('index');
        Route::get('/kartu-stok', [GudangController::class, 'kartuStok'])
            ->name('kartu-stok');
        Route::get('/stok-masuk', [GudangController::class, 'formStokMasuk'])
            ->name('stok-masuk.form');
        Route::post('/stok-masuk', [GudangController::class, 'stokMasuk'])
            ->name('stok-masuk');
        Route::patch('/stok/{stok}/batas', [GudangController::class, 'updateBatasStok'])
            ->name('update-batas');
    });

    // Purchase Order
    Route::resource('po', POController::class)->except(['edit', 'update', 'destroy']);
    Route::patch('/po/{po}/status', [POController::class, 'updateStatus'])
        ->name('po.update-status');
    Route::patch('/po/{po}/close', [POController::class, 'close'])
        ->name('po.close');

    // Approval
    Route::prefix('approval')->name('approval.')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])
            ->name('index');
        Route::post('/proses', [ApprovalController::class, 'proses'])
            ->name('proses');
        Route::get('/riwayat', [ApprovalController::class, 'riwayat'])
            ->name('riwayat');
    });

    // Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/efisiensi', [ReportController::class, 'efisiensi'])
            ->name('efisiensi');
        Route::get('/biaya', [ReportController::class, 'biaya'])
            ->name('biaya');
        Route::get('/anomali', [ReportController::class, 'anomali'])
            ->name('anomali');
        Route::get('/export/efisiensi', [ReportController::class, 'exportEfisiensi'])
            ->name('export.efisiensi');
    });

    // Master Data
    Route::resource('kendaraan', KendaraanController::class);
    Route::resource('bbm', BBMController::class)->except(['show', 'destroy']);
    Route::resource('vendor', VendorController::class);

    // User Management
    Route::resource('user', UserController::class)->except(['show']);
    Route::patch('/user/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->name('user.toggle-status');
});
