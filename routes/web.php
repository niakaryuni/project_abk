<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbkController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\NilaiAkademikController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Monitoring ABK
|--------------------------------------------------------------------------
*/

// Redirect halaman utama ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// ─── AUTH ────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
     ->name('logout')
     ->middleware('auth');

// ─── ROUTES YANG BUTUH LOGIN ─────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data Siswa ABK
    Route::resource('abk', AbkController::class);
    Route::patch('abk/{abk}/nonaktifkan', [AbkController::class, 'nonaktifkan'])->name('abk.nonaktifkan');
    Route::patch('abk/{abk}/aktifkan',    [AbkController::class, 'aktifkan'])->name('abk.aktifkan');

    // Nilai Perkembangan (mingguan)
    Route::resource('nilai', NilaiController::class);

    // Nilai Akademik (per semester)
    Route::resource('nilai-akademik', NilaiAkademikController::class);
    Route::get('nilai-akademik/siswa/{abk}', [NilaiAkademikController::class, 'showSiswa'])
         ->name('nilai-akademik.show-siswa');

    // Laporan
    Route::resource('laporan', LaporanController::class);
    Route::get('/laporan/{laporan}/download', [LaporanController::class, 'download'])
         ->name('laporan.download');

    // Kelola User (khusus Admin, pembatasan di controller)
Route::resource('users', UserController::class);
Route::patch('users/{user}/nonaktifkan', [UserController::class, 'nonaktifkan'])->name('users.nonaktifkan');
Route::patch('users/{user}/aktifkan',    [UserController::class, 'aktifkan'])->name('users.aktifkan');

});