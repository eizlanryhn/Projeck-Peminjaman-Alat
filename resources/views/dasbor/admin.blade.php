@extends('layouts.utama')

@section('judul', 'Dasbor Administrator')

@section('konten')
    <!-- Hero Banner Selamat Datang -->
    <div class="card border-0 mb-4 text-white overflow-hidden shadow" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 1.25rem;">
        <div class="card-body p-4 p-md-5 position-relative">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-danger bg-opacity-25 border border-danger border-opacity-25 text-danger-emphasis mb-3 small fw-semibold">
                        <i class="bi bi-shield-lock-fill"></i> Panel Kendali Administrator
                    </div>
                    <h2 class="fw-bold mb-2 text-white">Selamat Datang, {{ auth()->user()->nama }}! 👋</h2>
                    <p class="text-white-50 mb-4" style="max-width: 600px;">
                        Kelola seluruh inventaris laboratorium, hak akses pengguna, pemantauan transaksi, audit log aktivitas, serta konfigurasi sistem sekolah.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-light text-dark fw-semibold px-4" onclick="openOnboarding()">
                            <i class="bi bi-compass me-1 text-primary"></i> Panduan Sistem
                        </button>
                        <a href="{{ route('alat.create') }}" class="btn btn-primary px-3">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Alat
                        </a>
                        <a href="{{ route('pengguna.create') }}" class="btn btn-outline-light px-3">
                            <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <i class="bi bi-motherboard text-white-50" style="font-size: 8rem; opacity: 0.15;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Panduan Onboarding Terpasang di Dasbor -->
    <x-onboarding-card />

    <!-- 4 Kartu Statistik Metrik Utama -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Alat & Stok -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-primary h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fw-bold small text-uppercase">Inventaris Alat</span>
                        <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-tools"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">{{ number_format($totalAlat) }}</h3>
                    <div class="small text-muted">
                        <span class="text-success fw-semibold">{{ number_format($stokTersedia) }} unit</span> siap pinjam dari {{ number_format($totalStok) }} total
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Kategori -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-accent h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fw-bold small text-uppercase">Kategori Alat</span>
                        <div class="stat-icon-wrapper bg-info bg-opacity-10 text-info">
                            <i class="bi bi-tags-fill"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">{{ number_format($totalKategori) }}</h3>
                    <div class="small text-muted">
                        Kelompok & jenis alat praktikum
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Pengguna -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-success h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fw-bold small text-uppercase">Pengguna Terdaftar</span>
                        <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">{{ number_format($totalPengguna) }}</h3>
                    <div class="small text-muted">
                        Akun aktif: Admin, Petugas, Siswa
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Total Denda Terkumpul -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-warning h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fw-bold small text-uppercase">Kas Denda Terkumpul</span>
                        <div class="stat-icon-wrapper bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-warning-emphasis">
                        Rp {{ number_format($totalDenda, 0, ',', '.') }}
                    </h3>
                    <div class="small text-muted">
                        Dari {{ number_format($peminjamanAktif) }} transaksi aktif saat ini
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Aksi Cepat (Quick Actions Grid) -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-lightning-charge-fill text-warning"></i>
                <span class="fw-bold">Aksi Cepat & Navigasi Utama</span>
            </div>
        </div>
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-6 col-md-3">
                    <a href="{{ route('alat.index') }}" class="btn btn-outline-secondary w-100 py-3 text-start d-flex align-items-center gap-3">
                        <i class="bi bi-box-seam fs-3 text-primary"></i>
                        <div>
                            <div class="fw-bold text-dark">Data Alat</div>
                            <small class="text-muted d-none d-sm-block">Katalog & stok laboratorium</small>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary w-100 py-3 text-start d-flex align-items-center gap-3">
                        <i class="bi bi-person-gear fs-3 text-success"></i>
                        <div>
                            <div class="fw-bold text-dark">Pengguna</div>
                            <small class="text-muted d-none d-sm-block">Hak akses & data akun</small>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('koreksi.peminjaman.daftar') }}" class="btn btn-outline-secondary w-100 py-3 text-start d-flex align-items-center gap-3">
                        <i class="bi bi-pencil-square fs-3 text-warning"></i>
                        <div>
                            <div class="fw-bold text-dark">Koreksi Data</div>
                            <small class="text-muted d-none d-sm-block">Peminjaman & denda</small>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('pengaturan.form') }}" class="btn btn-outline-secondary w-100 py-3 text-start d-flex align-items-center gap-3">
                        <i class="bi bi-gear-wide-connected fs-3 text-danger"></i>
                        <div>
                            <div class="fw-bold text-dark">Pengaturan</div>
                            <small class="text-muted d-none d-sm-block">Tarif denda & durasi</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Pratinjau Tabel Log Aktivitas & Transaksi Peminjaman -->
    <div class="row g-4">
        <!-- Kolom Kiri: 6 Log Aktivitas Terkini -->
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-journal-text text-primary"></i>
                        <span class="fw-bold">Log Aktivitas Terkini</span>
                    </div>
                    <a href="{{ route('log.index') }}" class="btn btn-sm btn-outline-primary py-1 px-2 small">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Pengguna</th>
                                    <th>Aksi</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logTerbaru as $log)
                                    <tr>
                                        <td class="small text-muted text-nowrap">
                                            {{ $log->created_at->format('H:i') }}
                                            <span class="d-block" style="font-size: 0.72rem;">{{ $log->created_at->format('d/m/y') }}</span>
                                        </td>
                                        <td class="small fw-semibold text-nowrap">
                                            {{ $log->user?->nama ?? 'Sistem' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary" style="font-size: 0.7rem;">
                                                {{ $log->aksi }}
                                            </span>
                                        </td>
                                        <td class="small text-truncate" style="max-width: 200px;" title="{{ $log->deskripsi }}">
                                            {{ $log->deskripsi }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">
                                            Belum ada log aktivitas yang tercatat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: 5 Transaksi Peminjaman Terbaru -->
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-info"></i>
                        <span class="fw-bold">Transaksi Peminjaman Terbaru</span>
                    </div>
                    <a href="{{ route('koreksi.peminjaman.daftar') }}" class="btn btn-sm btn-outline-primary py-1 px-2 small">
                        Kelola Data <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Kode Pinjam</th>
                                    <th>Peminjam</th>
                                    <th>Alat</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($peminjamanTerbaru as $pj)
                                    <tr>
                                        <td class="fw-semibold small text-nowrap">
                                            {{ $pj->kode_pinjam }}
                                        </td>
                                        <td class="small">
                                            {{ $pj->peminjam->nama }}
                                        </td>
                                        <td class="small">
                                            {{ $pj->detail->count() }} jenis alat
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $pj->status->warna() }}" style="font-size: 0.72rem;">
                                                {{ $pj->status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">
                                            Belum ada transaksi peminjaman.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
