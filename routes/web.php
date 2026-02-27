<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TabulasiController;

Route::get('/', function () {
    if (auth()->check()) return redirect('/dashboard');
    return inertia('Welcome', ['error' => session('error')]);
})->name('login');

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [TabulasiController::class, 'index'])->name('dashboard');
    
    Route::get('/tabulasi/create', [TabulasiController::class, 'create'])->name('tabulasi.create');
    Route::post('/tabulasi', [TabulasiController::class, 'store'])->name('tabulasi.store');
    
    Route::get('/tabulasi/{link}', [TabulasiController::class, 'show'])->name('tabulasi.show');
    Route::delete('/tabulasi/{link}', [TabulasiController::class, 'destroy'])->name('tabulasi.destroy');
    
    // RUTE BARU: EDIT STRUKTUR TABULASI
    Route::get('/tabulasi/{link}/edit-setup', [TabulasiController::class, 'editSetup'])->name('tabulasi.setup.edit');
    Route::put('/tabulasi/{link}/setup', [TabulasiController::class, 'updateSetup'])->name('tabulasi.setup.update');

    Route::post('/tabulasi/{link}/akses', [TabulasiController::class, 'storeAkses'])->name('tabulasi.akses.store');
    Route::delete('/tabulasi/{link}/akses/{akses_id}', [TabulasiController::class, 'destroyAkses'])->name('tabulasi.akses.destroy');
    
    Route::post('/tabulasi/{link}/minta-akses', [TabulasiController::class, 'requestAccess'])->name('tabulasi.akses.request');
    Route::post('/tabulasi/{link}/akses/{akses_id}/terima', [TabulasiController::class, 'approveAccess'])->name('tabulasi.akses.approve');

    Route::post('/tabulasi/{link}/item', [TabulasiController::class, 'storeItem'])->name('tabulasi.item.store');
    Route::delete('/tabulasi/{link}/item/{item_id}', [TabulasiController::class, 'destroyItem'])->name('tabulasi.item.destroy');
    Route::post('/tabulasi/{link}/item/{item_id}/toggle', [TabulasiController::class, 'toggleItem'])->name('tabulasi.item.toggle');

    Route::get('/tabulasi/{link}/item/{item_id}/edit', [TabulasiController::class, 'editItem'])->name('tabulasi.item.edit');
    Route::put('/tabulasi/{link}/item/{item_id}', [TabulasiController::class, 'updateItem'])->name('tabulasi.item.update');

    Route::get('/tabulasi/{link}/export', [TabulasiController::class, 'exportCsv'])->name('tabulasi.export');
});

use Illuminate\Support\Facades\Artisan;

Route::get('/setup-database', function() {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return 'MANTAP! Migrasi Berhasil! Semua tabel sudah dibuat di Aiven. Silakan kembali ke halaman login.';
    } catch (\Exception $e) {
        return 'Gagal Migrasi: ' . $e->getMessage();
    }
});