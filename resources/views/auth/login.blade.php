@extends('layouts.utama')

@section('judul', 'Selamat Datang & Masuk ke Sistem')

@section('konten')
<div class="row justify-content-center py-2">
    <div class="col-lg-7 col-md-9">

        <!-- ================================================================= -->
        <!-- 1. TAMPILAN ONBOARDING (MUNCUL SEBELUM MENU LOGIN)               -->
        <!-- ================================================================= -->
        <div id="sectionOnboarding" class="card border-0 shadow-lg rounded-4 overflow-hidden" style="{{ $errors->any() ? 'display: none;' : '' }}">
            <!-- Header Onboarding -->
            <div class="card-header border-0 text-white p-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 p-2 bg-primary bg-opacity-25 text-primary">
                            <i class="bi bi-box-seam-fill text-white fs-5"></i>
                        </div>
                        <div>
                            <span class="badge bg-primary px-3 py-1 text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Panduan Onboarding</span>
                            <div class="text-white-50 small mt-1">{{ \App\Models\Pengaturan::ambil('nama_sekolah', 'SMK Negeri 1') }}</div>
                        </div>
                    </div>
                    <!-- Indikator Langkah -->
                    <div class="d-flex align-items-center gap-1" id="stepperDots">
                        <span class="badge rounded-pill bg-primary px-2" id="stepPill">Langkah 1 dari 3</span>
                    </div>
                </div>
            </div>

            <!-- Body Onboarding (3 Slide Interaktif) -->
            <div class="card-body p-4 p-md-5 text-center bg-white position-relative" style="min-height: 380px;">

                <!-- Slide 1: Inventaris & Katalog -->
                <div class="onboarding-slide active" id="slide1">
                    <div class="mx-auto mb-4 rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 90px; height: 90px;">
                        <i class="bi bi-tools fs-1"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-2">1. Katalog Alat Praktikum Lengkap</h3>
                    <p class="text-muted mx-auto mb-4" style="max-width: 520px; font-size: 0.98rem; line-height: 1.6;">
                        Telusuri seluruh inventaris peralatan laboratorium sains, komputer, dan perbengkelan sekolah dengan status ketersediaan stok fisik yang diperbarui secara real-time.
                    </p>
                    <div class="d-inline-flex gap-2 flex-wrap justify-content-center">
                        <span class="badge bg-light text-secondary border px-3 py-2"><i class="bi bi-check2 text-success me-1"></i> Stok Real-Time</span>
                        <span class="badge bg-light text-secondary border px-3 py-2"><i class="bi bi-check2 text-success me-1"></i> Foto Inventaris</span>
                        <span class="badge bg-light text-secondary border px-3 py-2"><i class="bi bi-check2 text-success me-1"></i> Kondisi Fisik Alat</span>
                    </div>
                </div>

                <!-- Slide 2: Pengajuan Mandiri -->
                <div class="onboarding-slide d-none" id="slide2">
                    <div class="mx-auto mb-4 rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success" style="width: 90px; height: 90px;">
                        <i class="bi bi-send-check-fill fs-1"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-2">2. Pengajuan Peminjaman Online</h3>
                    <p class="text-muted mx-auto mb-4" style="max-width: 520px; font-size: 0.98rem; line-height: 1.6;">
                        Pilih peralatan yang Anda butuhkan ke Keranjang pinjam. Tanggal pinjam otomatis tercatat hari ini, tentukan tenggat waktu kembali, dan permohonan Anda siap ditinjau oleh petugas.
                    </p>
                    <div class="d-inline-flex gap-2 flex-wrap justify-content-center">
                        <span class="badge bg-light text-secondary border px-3 py-2"><i class="bi bi-check2 text-success me-1"></i> Bebas Antre Manual</span>
                        <span class="badge bg-light text-secondary border px-3 py-2"><i class="bi bi-check2 text-success me-1"></i> Tanggal Otomatis</span>
                        <span class="badge bg-light text-secondary border px-3 py-2"><i class="bi bi-check2 text-success me-1"></i> Persetujuan Cepat</span>
                    </div>
                </div>

                <!-- Slide 3: Pengembalian & Laporan -->
                <div class="onboarding-slide d-none" id="slide3">
                    <div class="mx-auto mb-4 rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning" style="width: 90px; height: 90px;">
                        <i class="bi bi-shield-check fs-1"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-2">3. Pengembalian Tertib & Akuntabel</h3>
                    <p class="text-muted mx-auto mb-4" style="max-width: 520px; font-size: 0.98rem; line-height: 1.6;">
                        Ajukan pengembalian dengan satu klik setelah praktikum usai. Petugas memeriksa kondisi alat, sistem secara otomatis mengalkulasi denda keterlambatan jika melewati tenggat, dan mencetak laporan resmi.
                    </p>
                    <div class="d-inline-flex gap-2 flex-wrap justify-content-center">
                        <span class="badge bg-light text-secondary border px-3 py-2"><i class="bi bi-check2 text-success me-1"></i> Notifikasi Jatuh Tempo</span>
                        <span class="badge bg-light text-secondary border px-3 py-2"><i class="bi bi-check2 text-success me-1"></i> Hitung Denda Otomatis</span>
                        <span class="badge bg-light text-secondary border px-3 py-2"><i class="bi bi-check2 text-success me-1"></i> Cetak Laporan PDF</span>
                    </div>
                </div>

            </div>

            <!-- Footer Navigasi Onboarding -->
            <div class="card-footer bg-light p-4 border-0 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                <button type="button" class="btn btn-outline-secondary px-3" onclick="tampilkanFormLogin()">
                    Lewati Panduan & Masuk
                </button>

                <!-- Dots Navigasi -->
                <div class="d-flex align-items-center gap-2">
                    <span class="dot-indicator active" onclick="pindahSlide(1)"></span>
                    <span class="dot-indicator" onclick="pindahSlide(2)"></span>
                    <span class="dot-indicator" onclick="pindahSlide(3)"></span>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary px-3 d-none" id="btnPrevSlide" onclick="mundurSlide()">
                        <i class="bi bi-arrow-left me-1"></i> Sebelumnya
                    </button>
                    <button type="button" class="btn btn-primary px-4 fw-semibold" id="btnNextSlide" onclick="majuSlide()">
                        Selanjutnya <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                    <button type="button" class="btn btn-success px-4 fw-semibold d-none" id="btnLoginDirect" onclick="tampilkanFormLogin()">
                        Mulai Masuk ke Sistem <i class="bi bi-box-arrow-in-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- 2. FORMULIR LOGIN (TERBUKA SETELAH ONBOARDING ATAU SAAT ERROR)   -->
        <!-- ================================================================= -->
        <div id="sectionLogin" class="card border-0 shadow-lg rounded-4 overflow-hidden" style="{{ $errors->any() ? '' : 'display: none;' }}">
            <div class="card-header border-0 bg-white p-4 pb-0 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="tampilkanOnboarding()">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Panduan
                </button>
                <span class="badge bg-light text-muted border">Login Akses</span>
            </div>

            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex p-3 rounded-circle bg-primary bg-opacity-10 text-primary mb-3">
                        <i class="bi bi-shield-lock-fill fs-2"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">Masuk ke Sistem</h3>
                    <p class="text-muted small">
                        Masukkan nama pengguna dan kata sandi untuk mengakses layanan laboratorium
                    </p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <x-input name="username" label="Nama Pengguna" required autofocus placeholder="Masukkan username Anda" />
                    <x-input-password name="password" label="Kata Sandi" required placeholder="Masukkan kata sandi Anda" />

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold fs-6 mt-3 shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                    </button>
                </form>

                <div class="alert alert-light border d-flex align-items-center rounded-3 mt-4 mb-0 py-2 px-3 small text-muted">
                    <i class="bi bi-info-circle text-primary me-2 fs-5"></i>
                    <div>
                        Akun bawaan sistem: <strong>admin</strong>, <strong>petugas</strong>, atau <strong>peminjam</strong> (Kata sandi: <code>password123</code>).
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .dot-indicator {
        width: 10px;
        height: 10px;
        background-color: #cbd5e1;
        border-radius: 50%;
        display: inline-block;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .dot-indicator.active {
        width: 28px;
        background-color: #2563eb;
        border-radius: 9999px;
    }
</style>

@push('scripts')
<script>
    let slideSaatIni = 1;
    const totalSlide = 3;

    function perbaruiSlideUI() {
        // Tampilkan/sembunyikan slide
        for (let i = 1; i <= totalSlide; i++) {
            const slideEl = document.getElementById('slide' + i);
            if (slideEl) {
                if (i === slideSaatIni) {
                    slideEl.classList.remove('d-none');
                } else {
                    slideEl.classList.add('d-none');
                }
            }
        }

        // Perbarui dots
        const dots = document.querySelectorAll('.dot-indicator');
        dots.forEach((dot, idx) => {
            if (idx + 1 === slideSaatIni) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });

        // Perbarui teks pill langkah
        const stepPill = document.getElementById('stepPill');
        if (stepPill) {
            stepPill.textContent = 'Langkah ' + slideSaatIni + ' dari ' + totalSlide;
        }

        // Perbarui tombol Previous
        const btnPrev = document.getElementById('btnPrevSlide');
        if (btnPrev) {
            if (slideSaatIni > 1) {
                btnPrev.classList.remove('d-none');
            } else {
                btnPrev.classList.add('d-none');
            }
        }

        // Perbarui tombol Next vs Login
        const btnNext = document.getElementById('btnNextSlide');
        const btnLoginDirect = document.getElementById('btnLoginDirect');
        if (slideSaatIni === totalSlide) {
            if (btnNext) btnNext.classList.add('d-none');
            if (btnLoginDirect) btnLoginDirect.classList.remove('d-none');
        } else {
            if (btnNext) btnNext.classList.remove('d-none');
            if (btnLoginDirect) btnLoginDirect.classList.add('d-none');
        }
    }

    function majuSlide() {
        if (slideSaatIni < totalSlide) {
            slideSaatIni++;
            perbaruiSlideUI();
        } else {
            tampilkanFormLogin();
        }
    }

    function mundurSlide() {
        if (slideSaatIni > 1) {
            slideSaatIni--;
            perbaruiSlideUI();
        }
    }

    function pindahSlide(nomor) {
        slideSaatIni = nomor;
        perbaruiSlideUI();
    }

    function tampilkanFormLogin() {
        const sectionOnboarding = document.getElementById('sectionOnboarding');
        const sectionLogin = document.getElementById('sectionLogin');
        if (sectionOnboarding && sectionLogin) {
            sectionOnboarding.style.display = 'none';
            sectionLogin.style.display = 'block';
            const inputUser = document.getElementById('username');
            if (inputUser) inputUser.focus();
        }
    }

    function tampilkanOnboarding() {
        const sectionOnboarding = document.getElementById('sectionOnboarding');
        const sectionLogin = document.getElementById('sectionLogin');
        if (sectionOnboarding && sectionLogin) {
            sectionLogin.style.display = 'none';
            sectionOnboarding.style.display = 'block';
        }
    }
</script>
@endpush
@endsection
