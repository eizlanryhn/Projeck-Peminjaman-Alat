<?php

use App\Http\Controllers\AlatController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KoreksiPeminjamanController;
use App\Http\Controllers\KoreksiPengembalianController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LogAktivitasController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PersetujuanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dasbor', function () {
        return view('dasbor.admin');
    })->middleware('role:admin')->name('admin.dasbor');

    Route::get('/petugas/dasbor', function () {
        return view('dasbor.petugas');
    })->middleware('role:petugas')->name('petugas.dasbor');

    Route::get('/peminjam/dasbor', function () {
        return view('dasbor.peminjam');
    })->middleware('role:peminjam')->name('peminjam.dasbor');

    // 3.1 - CRUD Kategori (admin only)
    Route::resource('kategori', KategoriController::class)
        ->except(['show'])
        ->middleware('permission:kategori.kelola');

    // 3.2 - CRUD Alat (admin only)
    Route::resource('alat', AlatController::class)
        ->except(['show'])
        ->middleware('permission:alat.kelola');

    // 3.3 - CRUD Pengguna (admin only)
    Route::resource('pengguna', PenggunaController::class)
        ->except(['show'])
        ->middleware('permission:user.kelola');

    // 4.1 - Katalog Alat & Keranjang (peminjam / petugas)
    Route::middleware('permission:alat.lihat')
        ->prefix('katalog')
        ->name('katalog.')
        ->group(function () {
            Route::get('/', [KatalogController::class, 'katalog'])->name('daftar');
            Route::get('/keranjang', [KatalogController::class, 'lihatKeranjang'])->name('keranjang');
            Route::post('/{alat}/tambah', [KatalogController::class, 'tambahKeKeranjang'])->name('tambah');
            Route::put('/{alat}/jumlah', [KatalogController::class, 'ubahJumlah'])->name('ubah-jumlah');
            Route::delete('/{alatId}/hapus', [KatalogController::class, 'hapusDariKeranjang'])->name('hapus');
            Route::delete('/kosongkan', [KatalogController::class, 'kosongkanKeranjang'])->name('kosongkan');
        });

    // 4.2 - Transaksi Pengajuan Peminjaman (peminjam)
    Route::middleware('permission:peminjaman.ajukan')
        ->prefix('peminjaman')
        ->name('peminjaman.')
        ->group(function () {
            Route::get('/ajukan', [PeminjamanController::class, 'formPengajuan'])->name('ajukan');
            Route::post('/ajukan', [PeminjamanController::class, 'simpanPengajuan'])->name('simpan');
            Route::get('/saya', [PeminjamanController::class, 'daftarSaya'])->name('saya');
            Route::get('/{peminjaman}', [PeminjamanController::class, 'rincian'])->name('rincian');
        });

    // 4.3 - Persetujuan Peminjaman (petugas / admin)
    Route::middleware('permission:peminjaman.setujui')
        ->prefix('persetujuan')
        ->name('persetujuan.')
        ->group(function () {
            Route::get('/', [PersetujuanController::class, 'antrian'])->name('antrian');
            Route::get('/{peminjaman}', [PersetujuanController::class, 'rincian'])->name('rincian');
            Route::post('/{peminjaman}/setujui', [PersetujuanController::class, 'setujui'])->name('setujui');
            Route::post('/{peminjaman}/tolak', [PersetujuanController::class, 'tolak'])->name('tolak');
        });

    // 5.2 - Pengembalian (ajukan pengembalian oleh peminjam)
    Route::middleware('permission:peminjaman.kembalikan')
        ->post('/peminjaman/{peminjaman}/kembalikan', [PengembalianController::class, 'ajukan'])
        ->name('pengembalian.ajukan');

    // 5.2 - Pengembalian (pantau, verifikasi, rincian oleh petugas / admin)
    Route::middleware('permission:pengembalian.pantau')
        ->prefix('pengembalian')
        ->name('pengembalian.')
        ->group(function () {
            Route::get('/pantau', [PengembalianController::class, 'pantau'])->name('pantau');
            Route::get('/antrian', [PengembalianController::class, 'antrian'])->name('antrian');
            Route::get('/{peminjaman}/verifikasi', [PengembalianController::class, 'formVerifikasi'])->name('verifikasi');
            Route::post('/{peminjaman}/verifikasi', [PengembalianController::class, 'simpanVerifikasi'])->name('simpan');
            Route::get('/rincian/{pengembalian}', [PengembalianController::class, 'rincian'])->name('rincian');
        });

    // 6.1 - Log Aktivitas (admin only)
    Route::middleware('permission:log.lihat')
        ->get('/log', [LogAktivitasController::class, 'index'])
        ->name('log.index');

    // 6.2 - Laporan PDF (petugas)
    Route::middleware('permission:laporan.cetak')
        ->prefix('laporan')
        ->name('laporan.')
        ->group(function () {
            Route::get('/', [LaporanController::class, 'form'])->name('index');
            Route::get('/rpt01', [LaporanController::class, 'rpt01'])->name('rpt01');
            Route::get('/rpt02', [LaporanController::class, 'rpt02'])->name('rpt02');
            Route::get('/rpt03', [LaporanController::class, 'rpt03'])->name('rpt03');
        });

    // 7.1 - Koreksi Peminjaman (admin)
    Route::middleware('permission:peminjaman.kelola')
        ->prefix('koreksi/peminjaman')
        ->name('koreksi.peminjaman.')
        ->group(function () {
            Route::get('/', [KoreksiPeminjamanController::class, 'daftar'])->name('daftar');
            Route::get('/{peminjaman}/ubah', [KoreksiPeminjamanController::class, 'formUbah'])->name('ubah');
            Route::put('/{peminjaman}', [KoreksiPeminjamanController::class, 'perbarui'])->name('perbarui');
            Route::delete('/{peminjaman}', [KoreksiPeminjamanController::class, 'hapus'])->name('hapus');
        });

    // 7.1 - Koreksi Pengembalian (admin)
    Route::middleware('permission:pengembalian.kelola')
        ->prefix('koreksi/pengembalian')
        ->name('koreksi.pengembalian.')
        ->group(function () {
            Route::get('/', [KoreksiPengembalianController::class, 'daftar'])->name('daftar');
            Route::get('/{pengembalian}/ubah', [KoreksiPengembalianController::class, 'formUbah'])->name('ubah');
            Route::put('/{pengembalian}', [KoreksiPengembalianController::class, 'perbarui'])->name('perbarui');
        });

    // 7.2 - Pengaturan Sistem (admin)
    Route::middleware('permission:pengaturan.kelola')
        ->prefix('pengaturan')
        ->name('pengaturan.')
        ->group(function () {
            Route::get('/', [PengaturanController::class, 'form'])->name('form');
            Route::put('/', [PengaturanController::class, 'perbarui'])->name('perbarui');
        });
});
