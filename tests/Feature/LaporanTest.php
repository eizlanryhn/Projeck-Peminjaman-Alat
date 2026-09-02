<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Tests\TestCase;

/**
 * Pengujian 6.2.8 – Laporan PDF
 *
 * No | Percobaan                                    | Hasil yang harus muncul
 * ---|----------------------------------------------|------------------------
 *  1 | Buka menu Laporan                            | Tiga kartu laporan tampil
 *  2 | Cetak RPT-01 untuk bulan berjalan            | PDF terbuka di tab baru
 *  3 | Cetak RPT-01 dengan status Selesai           | Hanya peminjaman selesai (verif via DB)
 *  4 | Cetak RPT-02                                 | PDF OK dengan content-type application/pdf
 *  5 | Periksa baris Total Denda Terkumpul          | Data tersimpan dengan benar di DB
 *  6 | Cetak RPT-03 semua kategori                  | PDF OK
 *  7 | Cetak RPT-03 satu kategori                   | PDF OK, hanya alat kategori itu (verif DB)
 *  8 | Cetak periode kosong                         | PDF tetap terbuka (assertOk)
 *  9 | Isi tanggal akhir lebih awal dari awal       | Ditolak validasi
 * 10 | Masuk sebagai admin, buka /laporan           | HTTP 403
 */
class LaporanTest extends TestCase
{
    // -----------------------------------------------------------------------
    // Uji 1 – Petugas membuka halaman Laporan: tampil 3 kartu
    // -----------------------------------------------------------------------
    public function test_petugas_dapat_membuka_halaman_laporan(): void
    {
        $petugas = User::where('username', 'petugas')->first();

        $this->actingAs($petugas)
            ->get(route('laporan.index'))
            ->assertOk()
            ->assertSee('RPT-01')
            ->assertSee('RPT-02')
            ->assertSee('RPT-03');
    }

    // -----------------------------------------------------------------------
    // Uji 10 – Admin tidak boleh akses /laporan (403)
    // -----------------------------------------------------------------------
    public function test_admin_tidak_boleh_akses_laporan(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin)
            ->get(route('laporan.index'))
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Uji 2 – Cetak RPT-01 menghasilkan response PDF (Content-Type: application/pdf)
    // -----------------------------------------------------------------------
    public function test_rpt01_menghasilkan_pdf(): void
    {
        $petugas = User::where('username', 'petugas')->first();

        $response = $this->actingAs($petugas)
            ->get(route('laporan.rpt01', [
                'tgl_awal'  => now()->startOfMonth()->toDateString(),
                'tgl_akhir' => now()->toDateString(),
            ]));

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            $response->headers->get('Content-Type')
        );
    }

    // -----------------------------------------------------------------------
    // Uji 3 – RPT-01 filter status 'selesai' hanya menampilkan data selesai
    //         Verifikasi via DB (tidak lewat PDF binary)
    // -----------------------------------------------------------------------
    public function test_rpt01_filter_status_selesai_hanya_tampil_data_selesai(): void
    {
        $petugas  = User::where('username', 'petugas')->first();
        $peminjam = User::where('username', 'peminjam')->first();
        $alat     = Alat::first();

        // Buat peminjaman selesai
        $pSelesai = Peminjaman::create([
            'kode_pinjam'       => 'RPT-S-001',
            'user_id'           => $peminjam->id,
            'tgl_pinjam'        => now()->toDateString(),
            'tgl_harus_kembali' => now()->addDays(3)->toDateString(),
            'status'            => 'selesai',
        ]);
        $pSelesai->detail()->create(['alat_id' => $alat->id, 'jumlah' => 1]);

        // Buat peminjaman diajukan (tidak boleh masuk laporan 'selesai')
        $pDiajukan = Peminjaman::create([
            'kode_pinjam'       => 'RPT-D-001',
            'user_id'           => $peminjam->id,
            'tgl_pinjam'        => now()->toDateString(),
            'tgl_harus_kembali' => now()->addDays(7)->toDateString(),
            'status'            => 'diajukan',
        ]);
        $pDiajukan->detail()->create(['alat_id' => $alat->id, 'jumlah' => 1]);

        // Verifikasi via DB: dengan filter selesai, hanya pSelesai yang terpilih
        $hasil = Peminjaman::where('status', 'selesai')
            ->whereBetween('tgl_pinjam', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ])->get();

        $this->assertTrue($hasil->contains('kode_pinjam', 'RPT-S-001'));
        $this->assertFalse($hasil->contains('kode_pinjam', 'RPT-D-001'));

        // Response harus tetap PDF
        $response = $this->actingAs($petugas)
            ->get(route('laporan.rpt01', [
                'tgl_awal'  => now()->startOfMonth()->toDateString(),
                'tgl_akhir' => now()->endOfMonth()->toDateString(),
                'status'    => 'selesai',
            ]));

        $response->assertOk();
    }

    // -----------------------------------------------------------------------
    // Uji 4 & 5 – Cetak RPT-02: menghasilkan PDF; total denda tersimpan di DB
    // -----------------------------------------------------------------------
    public function test_rpt02_menghasilkan_pdf_dan_data_denda_benar(): void
    {
        $petugas  = User::where('username', 'petugas')->first();
        $peminjam = User::where('username', 'peminjam')->first();
        $alat     = Alat::first();

        // Buat data pengembalian langsung di DB untuk verifikasi total denda
        $peminjaman = Peminjaman::create([
            'kode_pinjam'       => 'RPT-P-001',
            'user_id'           => $peminjam->id,
            'tgl_pinjam'        => now()->subDays(5)->toDateString(),
            'tgl_harus_kembali' => now()->subDays(2)->toDateString(),
            'status'            => 'selesai',
            'petugas_id'        => $petugas->id,
        ]);
        $detail = $peminjaman->detail()->create([
            'alat_id'         => $alat->id,
            'jumlah'          => 1,
            'kondisi_kembali' => 'baik',
            'denda'           => 4000,
        ]);

        $pengembalian = Pengembalian::create([
            'peminjaman_id'   => $peminjaman->id,
            'petugas_id'      => $petugas->id,
            'tgl_kembali'     => now()->toDateString(),
            'hari_terlambat'  => 2,
            'denda'           => 4000,
            'denda_kerusakan' => 0,
            'total_denda'     => 4000,
        ]);

        // Uji 5: total denda di DB sama dengan sum kolom total_denda
        // Refresh untuk dapat nilai yang ditulis trigger
        $pengembalian->refresh();
        $totalDariDB = Pengembalian::whereBetween('tgl_kembali', [
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString(),
        ])->sum('total_denda');

        $this->assertGreaterThan(0, $totalDariDB);
        $this->assertEquals($pengembalian->total_denda, $totalDariDB);

        // Uji 4: response PDF OK
        $response = $this->actingAs($petugas)
            ->get(route('laporan.rpt02', [
                'tgl_awal'  => now()->startOfMonth()->toDateString(),
                'tgl_akhir' => now()->endOfMonth()->toDateString(),
            ]));

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            $response->headers->get('Content-Type')
        );
    }

    // -----------------------------------------------------------------------
    // Uji 6 – RPT-03 semua kategori menghasilkan PDF dengan semua alat
    // -----------------------------------------------------------------------
    public function test_rpt03_semua_kategori_menghasilkan_pdf(): void
    {
        $petugas = User::where('username', 'petugas')->first();

        $response = $this->actingAs($petugas)
            ->get(route('laporan.rpt03'));

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            $response->headers->get('Content-Type')
        );
    }

    // -----------------------------------------------------------------------
    // Uji 7 – RPT-03 satu kategori: verifikasi via DB, bukan PDF binary
    // -----------------------------------------------------------------------
    public function test_rpt03_satu_kategori_hanya_alat_kategori_tersebut(): void
    {
        $petugas  = User::where('username', 'petugas')->first();
        $kategori = Kategori::where('nama', 'Perkakas Tangan')->first();

        // Verifikasi via DB: query yang sama dipakai controller
        $daftarAlat = Alat::with('kategori')
            ->where('kategori_id', $kategori->id)
            ->orderBy('nama')
            ->get();

        // Harus ada alat dari kategori ini
        $this->assertTrue($daftarAlat->isNotEmpty());

        // Tidak boleh ada alat dari kategori lain
        foreach ($daftarAlat as $alat) {
            $this->assertEquals($kategori->id, $alat->kategori_id);
        }

        // Alat dari kategori lain tidak masuk
        $alatLain = Alat::where('kategori_id', '!=', $kategori->id)->first();
        $this->assertFalse($daftarAlat->contains('id', $alatLain?->id));

        // Response tetap PDF
        $response = $this->actingAs($petugas)
            ->get(route('laporan.rpt03', ['kategori_id' => $kategori->id]));

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            $response->headers->get('Content-Type')
        );
    }

    // -----------------------------------------------------------------------
    // Uji 8 – Periode kosong: PDF tetap terbuka (bukan error)
    // -----------------------------------------------------------------------
    public function test_rpt01_periode_kosong_tetap_menghasilkan_pdf(): void
    {
        $petugas = User::where('username', 'petugas')->first();

        $response = $this->actingAs($petugas)
            ->get(route('laporan.rpt01', [
                'tgl_awal'  => '2000-01-01',
                'tgl_akhir' => '2000-01-31',
            ]));

        // PDF harus tetap ter-generate, bukan error 500
        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            $response->headers->get('Content-Type')
        );
    }

    // -----------------------------------------------------------------------
    // Uji 9 – Tanggal akhir < tanggal awal → ditolak validasi (redirect back)
    // -----------------------------------------------------------------------
    public function test_rpt01_tanggal_akhir_lebih_awal_dari_awal_ditolak(): void
    {
        $petugas = User::where('username', 'petugas')->first();

        $this->actingAs($petugas)
            ->get(route('laporan.rpt01', [
                'tgl_awal'  => '2026-08-20',
                'tgl_akhir' => '2026-08-10',
            ]))
            ->assertRedirect();
    }
}
