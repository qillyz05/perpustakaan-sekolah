<?php

use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PeminjamanController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\Siswa\AreaSiswaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KatalogController::class, 'home'])->name('home');
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog');
Route::get('/katalog/{buku}', [KatalogController::class, 'show'])->name('katalog.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('admin')->name('admin.')->middleware('role:pustakawan')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('buku', BukuController::class)->except('show');
        Route::resource('kategori', KategoriController::class)->except('show');
        Route::resource('siswa', SiswaController::class)->except('show');
        Route::resource('peminjaman', PeminjamanController::class)->except(['edit', 'update']);

        Route::patch('peminjaman/{peminjaman}/kembalikan', [PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');
        Route::patch('peminjaman/{peminjaman}/bayar', [PeminjamanController::class, 'bayarDenda'])->name('peminjaman.bayar');
        Route::patch('peminjaman/{peminjaman}/perpanjang', [PeminjamanController::class, 'perpanjang'])->name('peminjaman.perpanjang');
        Route::patch('peminjaman/{peminjaman}/setujui', [PeminjamanController::class, 'setujui'])->name('peminjaman.setujui');
        Route::patch('peminjaman/{peminjaman}/tolak', [PeminjamanController::class, 'tolak'])->name('peminjaman.tolak');

        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');
        Route::get('laporan/export', [LaporanController::class, 'export'])->name('laporan.export');

        Route::get('pengaturan', [PengaturanController::class, 'edit'])->name('pengaturan.edit');
        Route::put('pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
    });

    Route::prefix('siswa')->name('siswa.')->middleware('role:siswa')->group(function () {
        Route::get('/', [AreaSiswaController::class, 'dashboard'])->name('dashboard');
        Route::get('riwayat', [AreaSiswaController::class, 'riwayat'])->name('riwayat');
        Route::post('buku/{buku}/ajukan', [AreaSiswaController::class, 'ajukan'])->name('peminjaman.ajukan');
        Route::delete('peminjaman/{peminjaman}/batalkan', [AreaSiswaController::class, 'batalkan'])->name('peminjaman.batalkan');
    });
});
