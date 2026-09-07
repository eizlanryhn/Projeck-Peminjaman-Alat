@extends('layouts.utama')

@section('judul', 'Dasbor Peminjam')

@section('konten')
    <!-- Alert Tunggakan / Terlambat -->
    @if ($daftarTunggakan->isNotEmpty())
        <div class="alert alert-danger d-flex align-items-center rounded-4 shadow-sm p-4 mb-4 border-0">
            <i class="bi bi-exclamation-octagon-fill fs-2 me-3 text-danger"></i>
            <div>
                <h5 class="alert-heading fw-bold mb-1">Perhatian: Ada Peminjaman yang Melewati Batas Waktu!</h5>
                <p class="mb-2 small">
                    Anda memiliki {{ $daftarTunggakan->count() }} transaksi peminjaman yang telah melewati batas tenggat kembali. Harap segera kembalikan ke petugas laboratorium untuk menghindari akumulasi denda keterlambatan.
                </p>
                <a href="{{ route('peminjaman.saya') }}" class="btn btn-sm btn-danger fw-semibold px-3">
                    Lihat Pinjaman Terlambat <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    @endif

    <!-- Hero Banner Selamat Datang Peminjam -->
    <div class="card border-0 mb-4 text-white overflow-hidden shadow" style="background: linear-gradient(135deg, #047857 0%, #064e3b 100%); border-radius: 1.25rem;">
        <div class="card-body p-4 p-md-5 position-relative">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-20 border border-white border-opacity-25 text-white mb-3 small fw-semibold">
                        <i class="bi bi-person-badge-fill"></i> Panel Peminjam Laboratorium
                    </div>
                    <h2 class="fw-bold mb-2 text-white">Halo, {{ auth()->user()->nama }}! 👋</h2>
                    <p class="text-white-50 mb-4" style="max-width: 600px;">
                        Siap melakukan praktikum hari ini? Temukan berbagai alat laboratorium yang Anda butuhkan, ajukan peminjaman secara online, dan pantau statusnya dengan mudah.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-light text-dark fw-semibold px-4" onclick="openOnboarding()">
                            <i class="bi bi-compass me-1 text-success"></i> Panduan Peminjam
                        </button>
                        <a href="{{ route('katalog.daftar') }}" class="btn btn-warning text-dark fw-semibold px-3">
                            <i class="bi bi-grid-fill me-1"></i> Jelajahi Katalog Alat
                        </a>
                        <a href="{{ route('katalog.keranjang') }}" class="btn btn-outline-light px-3">
                            <i class="bi bi-cart3 me-1"></i> Keranjang
                            @if (count(session('keranjang', [])) > 0)
                                <span class="badge bg-warning text-dark ms-1">{{ count(session('keranjang', [])) }}</span>
                            @endif
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <i class="bi bi-mortarboard text-white-50" style="font-size: 8rem; opacity: 0.15;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Panduan Onboarding Terpasang di Dasbor -->
    <x-onboarding-card />

    <!-- 4 Kartu Status Transaksi Peminjam -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Sedang Dipinjam -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-success h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fw-bold small text-uppercase">Sedang Dipinjam</span>
                        <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-success-emphasis">{{ number_format($dipinjamCount) }}</h3>
                    <div class="small text-muted">
                        Alat yang saat ini Anda gunakan
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Menunggu Persetujuan -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-warning h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fw-bold small text-uppercase">Menunggu Persetujuan</span>
                        <div class="stat-icon-wrapper bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-warning-emphasis">{{ number_format($diajukanCount) }}</h3>
                    <div class="small text-muted">
                        Sedang ditinjau oleh petugas
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Menunggu Verifikasi Kembali -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-accent h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fw-bold small text-uppercase">Menunggu Verifikasi</span>
                        <div class="stat-icon-wrapper bg-info bg-opacity-10 text-info">
                            <i class="bi bi-clipboard-check-fill"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-info-emphasis">{{ number_format($menungguVerifikasiCount) }}</h3>
                    <div class="small text-muted">
                        Alat sudah Anda ajukan kembali
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Riwayat Selesai -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-primary h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fw-bold small text-uppercase">Riwayat Selesai</span>
                        <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-check-all"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">{{ number_format($selesaiCount) }}</h3>
                    <div class="small text-muted">
                        Transaksi peminjaman yang tuntas
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Peminjaman Aktif Anda -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-bookmark-check-fill text-success"></i>
                <span class="fw-bold">Peminjaman Aktif Anda</span>
            </div>
            <a href="{{ route('peminjaman.saya') }}" class="btn btn-sm btn-outline-success py-1 px-3 small fw-semibold">
                Lihat Semua Riwayat <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Kode Pinjam</th>
                            <th>Daftar Alat</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tenggat Kembali</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pinjamanAktif as $peminjaman)
                            @php
                                $terlambat = ($peminjaman->status->value === \App\Enums\StatusPeminjaman::Dipinjam->value || $peminjaman->status->value === \App\Enums\StatusPeminjaman::MenungguVerifikasi->value) && $peminjaman->tgl_harus_kembali->isPast();
                            @endphp
                            <tr class="{{ $terlambat ? 'table-danger' : '' }}">
                                <td class="fw-semibold small text-nowrap">
                                    {{ $peminjaman->kode_pinjam }}
                                </td>
                                <td class="small">
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($peminjaman->detail as $item)
                                            <li>
                                                <i class="bi bi-dot text-success"></i>
                                                {{ $item->alat->nama }}
                                                <span class="badge bg-light text-dark border">x{{ $item->jumlah }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="small text-muted text-nowrap">
                                    {{ $peminjaman->tgl_pinjam->format('d/m/Y') }}
                                </td>
                                <td class="small text-nowrap">
                                    <span class="{{ $terlambat ? 'text-danger fw-bold' : '' }}">
                                        {{ $peminjaman->tgl_harus_kembali->format('d/m/Y') }}
                                    </span>
                                    @if ($terlambat)
                                        <span class="badge bg-danger ms-1" style="font-size: 0.68rem;">Terlambat</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $peminjaman->status->warna() }}" style="font-size: 0.75rem;">
                                        {{ $peminjaman->status->label() }}
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    @if ($peminjaman->status->value === \App\Enums\StatusPeminjaman::Dipinjam->value)
                                        <form method="POST" action="{{ route('pengembalian.ajukan', $peminjaman) }}" class="d-inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin mengajukan pengembalian untuk alat ini?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success py-1 px-2" style="font-size: 0.75rem;">
                                                <i class="bi bi-arrow-return-left me-1"></i> Kembalikan
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('peminjaman.rincian', $peminjaman) }}" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;">
                                            Rincian <i class="bi bi-chevron-right"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-muted opacity-50"></i>
                                    <div>Anda belum memiliki peminjaman yang sedang aktif.</div>
                                    <a href="{{ route('katalog.daftar') }}" class="btn btn-sm btn-success mt-2">
                                        <i class="bi bi-plus-circle me-1"></i> Ajukan Peminjaman Sekarang
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
