@php
    $user = auth()->user();
    $role = $user->roles->first()?->name ?? 'peminjam';
@endphp

<!-- Card Panduan Onboarding Terpasang di Dasbor -->
<div class="card border-0 shadow-sm mb-4" id="onboardingCard" style="border-left: 5px solid #2563eb !important; border-radius: 1rem;">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-rocket-takeoff-fill fs-4"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="fw-bold mb-0">Panduan Penggunaan Sistem (Onboarding)</h5>
                        <span class="badge bg-primary text-uppercase" style="font-size: 0.7rem;">{{ $role }}</span>
                    </div>
                    <p class="text-muted small mb-0 mt-1">
                        Ikuti 3 tahapan alur kerja utama ini untuk mengoperasikan sistem peminjaman alat secara efektif.
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-primary px-3" onclick="openOnboarding()">
                    <i class="bi bi-window-fullscreen me-1"></i> Buka Mode Interaktif
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary px-2" title="Sembunyikan panduan ini" onclick="tutupOnboardingCard()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>

        <div class="row g-3">
            @if ($role === 'admin')
                <!-- Langkah 1 Admin -->
                <div class="col-md-4">
                    <div class="p-3 rounded-3 bg-light h-100 border border-light-subtle d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-primary px-2 py-1">Langkah 1</span>
                                <i class="bi bi-tools text-primary fs-5"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Master Kategori & Alat</h6>
                            <p class="text-muted small mb-3">
                                Input kelompok kategori dan data alat laboratorium beserta stok total, stok tersedia, kondisi fisik, dan foto inventaris.
                            </p>
                        </div>
                        <a href="{{ route('alat.create') }}" class="btn btn-sm btn-outline-primary w-100 mt-auto">
                            <i class="bi bi-plus-circle me-1"></i> Input Alat Baru
                        </a>
                    </div>
                </div>

                <!-- Langkah 2 Admin -->
                <div class="col-md-4">
                    <div class="p-3 rounded-3 bg-light h-100 border border-light-subtle d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-success px-2 py-1">Langkah 2</span>
                                <i class="bi bi-people-fill text-success fs-5"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Daftarkan Pengguna</h6>
                            <p class="text-muted small mb-3">
                                Buat akun untuk Petugas dan Siswa/Peminjam, atur status akun aktif/nonaktif, dan kelola peran Spatie Permission.
                            </p>
                        </div>
                        <a href="{{ route('pengguna.create') }}" class="btn btn-sm btn-outline-success w-100 mt-auto">
                            <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
                        </a>
                    </div>
                </div>

                <!-- Langkah 3 Admin -->
                <div class="col-md-4">
                    <div class="p-3 rounded-3 bg-light h-100 border border-light-subtle d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-warning px-2 py-1">Langkah 3</span>
                                <i class="bi bi-shield-check text-warning fs-5"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Audit Log & Pengaturan</h6>
                            <p class="text-muted small mb-3">
                                Pantau riwayat aktivitas transaksi secara real-time dan konfigurasi tarif denda per hari serta durasi pinjam maksimal.
                            </p>
                        </div>
                        <a href="{{ route('pengaturan.form') }}" class="btn btn-sm btn-outline-warning text-dark w-100 mt-auto">
                            <i class="bi bi-gear me-1"></i> Atur Sistem
                        </a>
                    </div>
                </div>
            @elseif ($role === 'petugas')
                <!-- Langkah 1 Petugas -->
                <div class="col-md-4">
                    <div class="p-3 rounded-3 bg-light h-100 border border-light-subtle d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-warning text-dark px-2 py-1">Langkah 1</span>
                                <i class="bi bi-check2-circle text-warning fs-5"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Persetujuan Peminjaman</h6>
                            <p class="text-muted small mb-3">
                                Periksa permohonan pinjam alat dari siswa. Klik <strong>Setujui</strong> untuk memotong stok otomatis atau <strong>Tolak</strong> jika stok kurang.
                            </p>
                        </div>
                        <a href="{{ route('persetujuan.antrian') }}" class="btn btn-sm btn-outline-warning text-dark w-100 mt-auto">
                            <i class="bi bi-inbox me-1"></i> Buka Antrian Pengajuan
                        </a>
                    </div>
                </div>

                <!-- Langkah 2 Petugas -->
                <div class="col-md-4">
                    <div class="p-3 rounded-3 bg-light h-100 border border-light-subtle d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-info text-dark px-2 py-1">Langkah 2</span>
                                <i class="bi bi-clipboard-check text-info fs-5"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Verifikasi Pengembalian</h6>
                            <p class="text-muted small mb-3">
                                Cek kondisi fisik alat saat siswa mengembalikan. Trigger database otomatis menghitung denda keterlambatan jika melewati jatuh tempo.
                            </p>
                        </div>
                        <a href="{{ route('pengembalian.antrian') }}" class="btn btn-sm btn-outline-info text-dark w-100 mt-auto">
                            <i class="bi bi-clipboard-check me-1"></i> Buka Verifikasi
                        </a>
                    </div>
                </div>

                <!-- Langkah 3 Petugas -->
                <div class="col-md-4">
                    <div class="p-3 rounded-3 bg-light h-100 border border-light-subtle d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-danger px-2 py-1">Langkah 3</span>
                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Cetak Laporan PDF</h6>
                            <p class="text-muted small mb-3">
                                Unduh dan cetak rekapitulasi transaksi peminjaman (RPT-01), laporan pendapatan denda (RPT-02), dan stok alat laboratorium (RPT-03).
                            </p>
                        </div>
                        <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-outline-danger w-100 mt-auto">
                            <i class="bi bi-printer me-1"></i> Buka Menu Laporan
                        </a>
                    </div>
                </div>
            @else
                <!-- Langkah 1 Peminjam -->
                <div class="col-md-4">
                    <div class="p-3 rounded-3 bg-light h-100 border border-light-subtle d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-primary px-2 py-1">Langkah 1</span>
                                <i class="bi bi-grid text-primary fs-5"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Jelajahi Katalog Alat</h6>
                            <p class="text-muted small mb-3">
                                Cari peralatan praktikum yang Anda butuhkan. Periksa ketersediaan stok, lalu masukkan unit alat ke dalam Keranjang pinjam Anda.
                            </p>
                        </div>
                        <a href="{{ route('katalog.daftar') }}" class="btn btn-sm btn-outline-primary w-100 mt-auto">
                            <i class="bi bi-search me-1"></i> Buka Katalog Alat
                        </a>
                    </div>
                </div>

                <!-- Langkah 2 Peminjam -->
                <div class="col-md-4">
                    <div class="p-3 rounded-3 bg-light h-100 border border-light-subtle d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-success px-2 py-1">Langkah 2</span>
                                <i class="bi bi-send-check text-success fs-5"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Kirim Pengajuan Online</h6>
                            <p class="text-muted small mb-3">
                                Tanggal pinjam otomatis terisi real-time. Tentukan tanggal tenggat pengembalian dan tuliskan keperluan praktikum Anda dengan jelas.
                            </p>
                        </div>
                        <a href="{{ route('katalog.keranjang') }}" class="btn btn-sm btn-outline-success w-100 mt-auto">
                            <i class="bi bi-cart3 me-1"></i> Buka Keranjang
                        </a>
                    </div>
                </div>

                <!-- Langkah 3 Peminjam -->
                <div class="col-md-4">
                    <div class="p-3 rounded-3 bg-light h-100 border border-light-subtle d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-warning text-dark px-2 py-1">Langkah 3</span>
                                <i class="bi bi-arrow-return-left text-warning fs-5"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Kembalikan Tepat Waktu</h6>
                            <p class="text-muted small mb-3">
                                Pantau status pinjaman di menu "Pinjaman Saya". Saat selesai praktikum, klik tombol <strong>Kembalikan</strong> agar diverifikasi petugas.
                            </p>
                        </div>
                        <a href="{{ route('peminjaman.saya') }}" class="btn btn-sm btn-outline-warning text-dark w-100 mt-auto">
                            <i class="bi bi-bookmark-check me-1"></i> Pinjaman Saya
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function tutupOnboardingCard() {
        const card = document.getElementById('onboardingCard');
        if (card) {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            setTimeout(() => card.style.display = 'none', 300);
            localStorage.setItem('onboarding_card_hidden_{{ $user->id }}', 'true');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const isHidden = localStorage.getItem('onboarding_card_hidden_{{ $user->id }}');
        const card = document.getElementById('onboardingCard');
        if (isHidden === 'true' && card) {
            card.style.display = 'none';
        }
    });

    window.tampilkanOnboardingCard = function() {
        const card = document.getElementById('onboardingCard');
        if (card) {
            card.style.display = 'block';
            card.style.opacity = '1';
            localStorage.removeItem('onboarding_card_hidden_{{ $user->id }}');
        }
    };
</script>
