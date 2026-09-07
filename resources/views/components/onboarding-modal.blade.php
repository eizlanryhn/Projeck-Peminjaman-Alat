@php
    $user = auth()->user();
    $role = $user->roles->first()?->name ?? 'peminjam';
@endphp

<!-- Modal Onboarding / Panduan Cepat -->
<div class="modal fade" id="modalOnboarding" tabindex="-1" aria-labelledby="modalOnboardingLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem; overflow: hidden;">
            <!-- Modal Header dengan Gradient -->
            <div class="modal-header border-0 text-white p-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="bi bi-compass-fill fs-3 text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-1" id="modalOnboardingLabel">
                            Selamat Datang, {{ $user->nama }}! 👋
                        </h5>
                        <p class="mb-0 text-white-50 small">
                            Panduan Cepat Penggunaan Sistem Peminjaman Alat Laboratorium
                            <span class="badge bg-primary ms-1 text-uppercase">{{ $role }}</span>
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body: 3 Langkah Panduan Berdasarkan Peran -->
            <div class="modal-body p-4 bg-light">
                <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.78rem; letter-spacing: 0.8px;">
                    Alur Kerja Utama Anda Sebagai {{ ucfirst($role) }}
                </h6>

                <div class="row g-3">
                    @if ($role === 'admin')
                        <!-- Langkah Admin 1 -->
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm p-3 rounded-4 bg-white">
                                <div class="text-primary mb-3">
                                    <div class="d-inline-flex p-2 rounded-3 bg-primary bg-opacity-10">
                                        <i class="bi bi-box-seam fs-4"></i>
                                    </div>
                                    <span class="badge bg-light text-primary float-end fw-bold">01</span>
                                </div>
                                <h6 class="fw-bold mb-1">Master Data Alat</h6>
                                <p class="text-muted small mb-0">
                                    Kelola kategori dan inventaris alat laboratorium, stok total, stok tersedia, kondisi fisik, dan foto alat.
                                </p>
                            </div>
                        </div>

                        <!-- Langkah Admin 2 -->
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm p-3 rounded-4 bg-white">
                                <div class="text-success mb-3">
                                    <div class="d-inline-flex p-2 rounded-3 bg-success bg-opacity-10">
                                        <i class="bi bi-people-fill fs-4"></i>
                                    </div>
                                    <span class="badge bg-light text-success float-end fw-bold">02</span>
                                </div>
                                <h6 class="fw-bold mb-1">Kelola Pengguna</h6>
                                <p class="text-muted small mb-0">
                                    Daftarkan akun Petugas dan Siswa/Peminjam, atur status aktif/nonaktif, serta hak akses peran sistem.
                                </p>
                            </div>
                        </div>

                        <!-- Langkah Admin 3 -->
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm p-3 rounded-4 bg-white">
                                <div class="text-warning mb-3">
                                    <div class="d-inline-flex p-2 rounded-3 bg-warning bg-opacity-10">
                                        <i class="bi bi-sliders fs-4"></i>
                                    </div>
                                    <span class="badge bg-light text-warning float-end fw-bold">03</span>
                                </div>
                                <h6 class="fw-bold mb-1">Audit & Pengaturan</h6>
                                <p class="text-muted small mb-0">
                                    Pantau rekam jejak Log Aktivitas secara real-time dan atur nama sekolah, batas hari pinjam, serta tarif denda.
                                </p>
                            </div>
                        </div>
                    @elseif ($role === 'petugas')
                        <!-- Langkah Petugas 1 -->
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm p-3 rounded-4 bg-white">
                                <div class="text-warning mb-3">
                                    <div class="d-inline-flex p-2 rounded-3 bg-warning bg-opacity-10">
                                        <i class="bi bi-check2-circle fs-4"></i>
                                    </div>
                                    <span class="badge bg-light text-warning float-end fw-bold">01</span>
                                </div>
                                <h6 class="fw-bold mb-1">Persetujuan Pinjam</h6>
                                <p class="text-muted small mb-0">
                                    Tinjau permohonan alat yang diajukan peminjam. Setujui untuk mengurangi stok secara otomatis atau tolak jika alat belum siap.
                                </p>
                            </div>
                        </div>

                        <!-- Langkah Petugas 2 -->
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm p-3 rounded-4 bg-white">
                                <div class="text-info mb-3">
                                    <div class="d-inline-flex p-2 rounded-3 bg-info bg-opacity-10">
                                        <i class="bi bi-clipboard-check-fill fs-4"></i>
                                    </div>
                                    <span class="badge bg-light text-info float-end fw-bold">02</span>
                                </div>
                                <h6 class="fw-bold mb-1">Verifikasi Kembali</h6>
                                <p class="text-muted small mb-0">
                                    Periksa kondisi fisik alat saat dikembalikan. Denda keterlambatan dan pemulihan stok alat dihitung otomatis oleh basis data.
                                </p>
                            </div>
                        </div>

                        <!-- Langkah Petugas 3 -->
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm p-3 rounded-4 bg-white">
                                <div class="text-danger mb-3">
                                    <div class="d-inline-flex p-2 rounded-3 bg-danger bg-opacity-10">
                                        <i class="bi bi-file-earmark-pdf-fill fs-4"></i>
                                    </div>
                                    <span class="badge bg-light text-danger float-end fw-bold">03</span>
                                </div>
                                <h6 class="fw-bold mb-1">Cetak Laporan</h6>
                                <p class="text-muted small mb-0">
                                    Generate berkas PDF rekapitulasi resmi: RPT-01 (Peminjaman), RPT-02 (Pengembalian & Denda), dan RPT-03 (Kondisi Stok Alat).
                                </p>
                            </div>
                        </div>
                    @else
                        <!-- Langkah Peminjam 1 -->
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm p-3 rounded-4 bg-white">
                                <div class="text-primary mb-3">
                                    <div class="d-inline-flex p-2 rounded-3 bg-primary bg-opacity-10">
                                        <i class="bi bi-grid-fill fs-4"></i>
                                    </div>
                                    <span class="badge bg-light text-primary float-end fw-bold">01</span>
                                </div>
                                <h6 class="fw-bold mb-1">Pilih di Katalog</h6>
                                <p class="text-muted small mb-0">
                                    Lihat inventaris alat laboratorium yang siap dipinjam. Tentukan jumlah unit alat lalu masukkan ke Keranjang Anda.
                                </p>
                            </div>
                        </div>

                        <!-- Langkah Peminjam 2 -->
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm p-3 rounded-4 bg-white">
                                <div class="text-success mb-3">
                                    <div class="d-inline-flex p-2 rounded-3 bg-success bg-opacity-10">
                                        <i class="bi bi-send-check-fill fs-4"></i>
                                    </div>
                                    <span class="badge bg-light text-success float-end fw-bold">02</span>
                                </div>
                                <h6 class="fw-bold mb-1">Ajukan Peminjaman</h6>
                                <p class="text-muted small mb-0">
                                    Tanggal pinjam otomatis terisi real-time. Tentukan tanggal tenggat kembali dan keperluan praktikum Anda, lalu kirim pengajuan.
                                </p>
                            </div>
                        </div>

                        <!-- Langkah Peminjam 3 -->
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm p-3 rounded-4 bg-white">
                                <div class="text-warning mb-3">
                                    <div class="d-inline-flex p-2 rounded-3 bg-warning bg-opacity-10">
                                        <i class="bi bi-arrow-return-left fs-4"></i>
                                    </div>
                                    <span class="badge bg-light text-warning float-end fw-bold">03</span>
                                </div>
                                <h6 class="fw-bold mb-1">Kembalikan Tepat Waktu</h6>
                                <p class="text-muted small mb-0">
                                    Buka menu "Pinjaman Saya" dan klik tombol "Kembalikan Alat" setelah selesai praktikum untuk diperiksa oleh petugas laboratorium.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="alert alert-primary d-flex align-items-center rounded-3 mt-3 mb-0 py-2 px-3 small border-0 bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-info-circle-fill me-2 fs-6"></i>
                    <div>
                        Anda dapat membuka kembali panduan ini kapan saja melalui tombol <strong>Panduan</strong> di bilah navigasi atas.
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-0 bg-white p-3 justify-content-between">
                <div class="form-check ms-2">
                    <input class="form-check-input" type="checkbox" id="checkJanganTampilkan">
                    <label class="form-check-label text-muted small user-select-none" for="checkJanganTampilkan">
                        Jangan tampilkan panduan ini secara otomatis lagi
                    </label>
                </div>
                <button type="button" class="btn btn-primary px-4 fw-semibold" onclick="tutupDanSimpanOnboarding()">
                    Mulai Bekerja <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const USER_ID = "{{ $user->id }}";
    const ONBOARDING_KEY = 'onboarding_v2_' + USER_ID;

    // Fungsi global untuk membuka modal panduan onboarding
    window.openOnboarding = function() {
        const modalEl = document.getElementById('modalOnboarding');
        if (!modalEl) return;

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } else {
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
        }
    };

    // Fungsi untuk menutup modal dan menyimpan preferensi
    window.tutupDanSimpanOnboarding = function() {
        const modalEl = document.getElementById('modalOnboarding');
        const checkJangan = document.getElementById('checkJanganTampilkan');

        if (checkJangan && checkJangan.checked) {
            localStorage.setItem(ONBOARDING_KEY, 'true');
        }

        if (modalEl) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            } else {
                modalEl.classList.remove('show');
                modalEl.style.display = 'none';
            }
        }
    };

    // Tampilkan otomatis saat pertama kali dibuka jika belum ditandai pernah dilihat
    function cekDanBukaOnboarding() {
        const sudahDilihat = localStorage.getItem(ONBOARDING_KEY);
        if (!sudahDilihat) {
            setTimeout(function() {
                window.openOnboarding();
            }, 300);
        }
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        cekDanBukaOnboarding();
    } else {
        window.addEventListener('load', cekDanBukaOnboarding);
    }
</script>
@endpush
