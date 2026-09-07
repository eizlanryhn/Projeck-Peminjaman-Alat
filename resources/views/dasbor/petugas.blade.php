@extends('layouts.utama')

@section('judul', 'Dasbor Petugas')

@section('konten')
    <!-- Hero Banner Selamat Datang Petugas -->
    <div class="card border-0 mb-4 text-white overflow-hidden shadow" style="background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); border-radius: 1.25rem;">
        <div class="card-body p-4 p-md-5 position-relative">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-warning bg-opacity-25 border border-warning border-opacity-25 text-warning-emphasis mb-3 small fw-semibold">
                        <i class="bi bi-clipboard2-check-fill"></i> Panel Kerja Petugas
                    </div>
                    <h2 class="fw-bold mb-2 text-white">Semangat Bertugas, {{ auth()->user()->nama }}! 👋</h2>
                    <p class="text-white-50 mb-4" style="max-width: 600px;">
                        Tinjau antrian permohonan alat dari siswa, verifikasi pengembalian fisik alat praktikum, periksa denda keterlambatan, dan cetak berkas laporan PDF resmi.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-light text-dark fw-semibold px-4" onclick="openOnboarding()">
                            <i class="bi bi-compass me-1 text-primary"></i> Panduan Petugas
                        </button>
                        <a href="{{ route('persetujuan.antrian') }}" class="btn btn-warning text-dark fw-semibold px-3">
                            <i class="bi bi-check2-circle me-1"></i> Antrian Persetujuan
                            @if ($antrianPersetujuan > 0)
                                <span class="badge bg-danger ms-1 text-white">{{ $antrianPersetujuan }}</span>
                            @endif
                        </a>
                        <a href="{{ route('pengembalian.antrian') }}" class="btn btn-outline-light px-3">
                            <i class="bi bi-clipboard-check me-1"></i> Verifikasi Pengembalian
                            @if ($antrianVerifikasi > 0)
                                <span class="badge bg-info ms-1 text-dark">{{ $antrianVerifikasi }}</span>
                            @endif
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <i class="bi bi-clipboard-pulse text-white-50" style="font-size: 8rem; opacity: 0.15;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Panduan Onboarding Terpasang di Dasbor -->
    <x-onboarding-card />

    <!-- 4 Kartu Metrik Antrian Petugas -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Antrian Persetujuan -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-warning h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fw-bold small text-uppercase">Perlu Persetujuan</span>
                        <div class="stat-icon-wrapper bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 {{ $antrianPersetujuan > 0 ? 'text-warning-emphasis' : '' }}">
                        {{ number_format($antrianPersetujuan) }}
                    </h3>
                    <div class="small text-muted">
                        Pengajuan alat menunggu konfirmasi
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Menunggu Verifikasi Pengembalian -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-accent h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fw-bold small text-uppercase">Perlu Verifikasi</span>
                        <div class="stat-icon-wrapper bg-info bg-opacity-10 text-info">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 {{ $antrianVerifikasi > 0 ? 'text-info-emphasis' : '' }}">
                        {{ number_format($antrianVerifikasi) }}
                    </h3>
                    <div class="small text-muted">
                        Peminjam telah mengajukan kembali
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Alat Sedang Dipinjam -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-primary h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fw-bold small text-uppercase">Sedang Dipinjam</span>
                        <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">{{ number_format($peminjamanSedangDipinjam) }}</h3>
                    <div class="small text-muted">
                        Transaksi aktif di laboratorium
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Lewat Tenggat / Terlambat -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-danger h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fw-bold small text-uppercase">Lewat Tenggat</span>
                        <div class="stat-icon-wrapper bg-danger bg-opacity-10 text-danger">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 {{ $peminjamanLewatTenggat > 0 ? 'text-danger' : '' }}">
                        {{ number_format($peminjamanLewatTenggat) }}
                    </h3>
                    <div class="small text-muted">
                        {{ $peminjamanLewatTenggat > 0 ? 'Peminjaman melewati batas tanggal kembali' : 'Semua transaksi masih dalam tenggat' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Aksi Cepat Petugas -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-lightning-charge-fill text-warning"></i>
                <span class="fw-bold">Aksi Cepat Petugas</span>
            </div>
        </div>
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-6 col-md-3">
                    <a href="{{ route('persetujuan.antrian') }}" class="btn btn-outline-warning w-100 py-3 text-start d-flex align-items-center gap-3">
                        <i class="bi bi-check2-circle fs-3 text-warning"></i>
                        <div>
                            <div class="fw-bold text-dark">Persetujuan</div>
                            <small class="text-muted d-none d-sm-block">Setujui/tolak pengajuan</small>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('pengembalian.antrian') }}" class="btn btn-outline-info w-100 py-3 text-start d-flex align-items-center gap-3">
                        <i class="bi bi-clipboard-check fs-3 text-info"></i>
                        <div>
                            <div class="fw-bold text-dark">Verifikasi</div>
                            <small class="text-muted d-none d-sm-block">Cek kondisi alat kembali</small>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('pengembalian.pantau') }}" class="btn btn-outline-primary w-100 py-3 text-start d-flex align-items-center gap-3">
                        <i class="bi bi-clock-history fs-3 text-primary"></i>
                        <div>
                            <div class="fw-bold text-dark">Pemantauan</div>
                            <small class="text-muted d-none d-sm-block">Pantau status & tenggat</small>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('laporan.index') }}" class="btn btn-outline-danger w-100 py-3 text-start d-flex align-items-center gap-3">
                        <i class="bi bi-file-earmark-pdf fs-3 text-danger"></i>
                        <div>
                            <div class="fw-bold text-dark">Laporan PDF</div>
                            <small class="text-muted d-none d-sm-block">Cetak rekapitulasi resmi</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2 Tabel Antrian Transaksi -->
    <div class="row g-4">
        <!-- Kolom Kiri: Pengajuan Menunggu Persetujuan -->
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-inbox-fill text-warning"></i>
                        <span class="fw-bold">Pengajuan Menunggu Persetujuan</span>
                    </div>
                    <a href="{{ route('persetujuan.antrian') }}" class="btn btn-sm btn-outline-warning text-dark py-1 px-2 small">
                        Buka Antrian <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Kode Pinjam</th>
                                    <th>Peminjam</th>
                                    <th>Tenggat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($daftarPengajuan as $pj)
                                    <tr>
                                        <td class="fw-semibold small text-nowrap">
                                            {{ $pj->kode_pinjam }}
                                            <span class="d-block text-muted" style="font-size: 0.72rem;">{{ $pj->detail->count() }} jenis alat</span>
                                        </td>
                                        <td class="small">{{ $pj->peminjam->nama }}</td>
                                        <td class="small text-muted text-nowrap">
                                            {{ $pj->tgl_harus_kembali->format('d/m/Y') }}
                                        </td>
                                        <td class="text-nowrap">
                                            <a href="{{ route('persetujuan.rincian', $pj) }}" class="btn btn-sm btn-primary py-1 px-2" style="font-size: 0.75rem;">
                                                Proses <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">
                                            <i class="bi bi-check-circle-fill text-success fs-4 d-block mb-1"></i>
                                            Tidak ada antrian persetujuan saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Pengembalian Menunggu Verifikasi -->
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-clipboard-check-fill text-info"></i>
                        <span class="fw-bold">Menunggu Verifikasi Pengembalian</span>
                    </div>
                    <a href="{{ route('pengembalian.antrian') }}" class="btn btn-sm btn-outline-info text-dark py-1 px-2 small">
                        Buka Verifikasi <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Kode Pinjam</th>
                                    <th>Peminjam</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($daftarPerluVerifikasi as $pv)
                                    <tr>
                                        <td class="fw-semibold small text-nowrap">
                                            {{ $pv->kode_pinjam }}
                                        </td>
                                        <td class="small">{{ $pv->peminjam->nama }}</td>
                                        <td>
                                            <span class="badge bg-info text-dark" style="font-size: 0.72rem;">
                                                Menunggu Verifikasi
                                            </span>
                                        </td>
                                        <td class="text-nowrap">
                                            <a href="{{ route('pengembalian.verifikasi', $pv) }}" class="btn btn-sm btn-info text-dark fw-semibold py-1 px-2" style="font-size: 0.75rem;">
                                                Verifikasi <i class="bi bi-check2"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">
                                            <i class="bi bi-check-circle-fill text-success fs-4 d-block mb-1"></i>
                                            Tidak ada antrian pengembalian yang menunggu verifikasi.
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
