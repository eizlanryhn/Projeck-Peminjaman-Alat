@php
    $urlDasbor = route('login');
    if (auth()->check()) {
        if (auth()->user()->hasRole('admin')) {
            $urlDasbor = route('admin.dasbor');
        } elseif (auth()->user()->hasRole('petugas')) {
            $urlDasbor = route('petugas.dasbor');
        } else {
            $urlDasbor = route('peminjam.dasbor');
        }
    }
@endphp

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ $urlDasbor }}">
            <i class="bi bi-box-seam-fill text-primary"></i>
            <span>Peminjaman Alat</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menuUtama" aria-controls="menuUtama" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuUtama">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @auth
                    {{-- Menu Dasbor Utama --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('*dasbor*') ? 'active' : '' }}" href="{{ $urlDasbor }}">
                            <i class="bi bi-speedometer2 me-1"></i> Dasbor
                        </a>
                    </li>
                @endauth

                {{-- Menu Administrator --}}
                @can('kategori.kelola')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('kategori*') ? 'active' : '' }}" href="{{ route('kategori.index') }}">
                            <i class="bi bi-tags me-1"></i> Kategori
                        </a>
                    </li>
                @endcan
                @can('alat.kelola')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('alat*') ? 'active' : '' }}" href="{{ route('alat.index') }}">
                            <i class="bi bi-tools me-1"></i> Alat
                        </a>
                    </li>
                @endcan
                @can('user.kelola')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('pengguna*') ? 'active' : '' }}" href="{{ route('pengguna.index') }}">
                            <i class="bi bi-people me-1"></i> Pengguna
                        </a>
                    </li>
                @endcan
                @can('peminjaman.kelola')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('koreksi/peminjaman*') ? 'active' : '' }}" href="{{ route('koreksi.peminjaman.daftar') }}">
                            <i class="bi bi-card-checklist me-1"></i> Data Peminjaman
                        </a>
                    </li>
                @endcan
                @can('pengembalian.kelola')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('koreksi/pengembalian*') ? 'active' : '' }}" href="{{ route('koreksi.pengembalian.daftar') }}">
                            <i class="bi bi-arrow-return-left me-1"></i> Data Pengembalian
                        </a>
                    </li>
                @endcan
                @can('log.lihat')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('log*') ? 'active' : '' }}" href="{{ route('log.index') }}">
                            <i class="bi bi-journal-text me-1"></i> Log Aktivitas
                        </a>
                    </li>
                @endcan
                @can('pengaturan.kelola')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('pengaturan*') ? 'active' : '' }}" href="{{ route('pengaturan.form') }}">
                            <i class="bi bi-gear me-1"></i> Pengaturan
                        </a>
                    </li>
                @endcan

                {{-- Menu Petugas --}}
                @can('peminjaman.setujui')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('persetujuan*') ? 'active' : '' }}" href="{{ route('persetujuan.antrian') }}">
                            <i class="bi bi-check2-circle me-1"></i> Persetujuan
                        </a>
                    </li>
                @endcan
                @can('pengembalian.pantau')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('pengembalian/pantau*') ? 'active' : '' }}" href="{{ route('pengembalian.pantau') }}">
                            <i class="bi bi-clock-history me-1"></i> Pemantauan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('pengembalian/antrian*') ? 'active' : '' }}" href="{{ route('pengembalian.antrian') }}">
                            <i class="bi bi-clipboard-check me-1"></i> Verifikasi
                        </a>
                    </li>
                @endcan
                @can('laporan.cetak')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Laporan
                        </a>
                    </li>
                @endcan

                {{-- Menu Peminjam --}}
                @can('alat.lihat')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('katalog') ? 'active' : '' }}" href="{{ route('katalog.daftar') }}">
                            <i class="bi bi-grid me-1"></i> Katalog Alat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('katalog/keranjang*') ? 'active' : '' }}" href="{{ route('katalog.keranjang') }}">
                            <i class="bi bi-cart3 me-1"></i> Keranjang
                            @if (count(session('keranjang', [])) > 0)
                                <span class="badge bg-warning text-dark ms-1">
                                    {{ count(session('keranjang', [])) }}
                                </span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('peminjaman/saya*') ? 'active' : '' }}" href="{{ route('peminjaman.saya') }}">
                            <i class="bi bi-bookmark-check me-1"></i> Pinjaman Saya
                        </a>
                    </li>
                @endcan
            </ul>

            @auth
                <ul class="navbar-nav align-items-center">
                    {{-- Tombol Panduan Onboarding --}}
                    <li class="nav-item me-2">
                        <button type="button" class="btn btn-sm btn-outline-light border-opacity-25" onclick="openOnboarding()" title="Buka Panduan Penggunaan Sistem">
                            <i class="bi bi-lightbulb me-1 text-warning"></i> Panduan
                        </button>
                    </li>

                    {{-- Nama Pengguna & Peran --}}
                    <li class="nav-item me-3 text-white text-end">
                        <span class="fw-semibold d-block" style="font-size: 0.9rem;">{{ auth()->user()->nama }}</span>
                        @if (auth()->user()->hasRole('admin'))
                            <span class="badge bg-danger" style="font-size: 0.72rem;">Admin</span>
                        @elseif (auth()->user()->hasRole('petugas'))
                            <span class="badge bg-warning text-dark" style="font-size: 0.72rem;">Petugas</span>
                        @else
                            <span class="badge bg-info text-dark" style="font-size: 0.72rem;">Peminjam</span>
                        @endif
                    </li>

                    {{-- Tombol Keluar --}}
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger px-3" title="Keluar dari akun">
                                <i class="bi bi-box-arrow-right me-1"></i> Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            @endauth
        </div>
    </div>
</nav>
