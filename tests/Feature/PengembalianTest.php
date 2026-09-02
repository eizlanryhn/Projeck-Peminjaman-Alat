<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Pengujian BAB 5 – Modul Pengembalian
 */
class PengembalianTest extends TestCase
{
    /**
     * Buat peminjaman yang sudah berstatus 'dipinjam' dengan cara yang benar
     * melalui stored procedure supaya stok_tersedia otomatis berkurang.
     */
    private static int $counter = 0;

    private function buatPeminjamanDipinjam(
        User $peminjam,
        User $petugas,
        Alat $alat,
        int $terlambatHari = 0
    ): Peminjaman {
        self::$counter++;
        $kode = 'PJM-T-' . str_pad(self::$counter, 3, '0', STR_PAD_LEFT);

        $tglHarus = $terlambatHari > 0
            ? now()->subDays($terlambatHari)->toDateString()
            : now()->addDays(7)->toDateString();

        $peminjaman = Peminjaman::create([
            'kode_pinjam'       => $kode,
            'user_id'           => $peminjam->id,
            'tgl_pinjam'        => now()->subDays($terlambatHari + 7)->toDateString(),
            'tgl_harus_kembali' => $tglHarus,
            'status'            => 'diajukan',
        ]);
        $peminjaman->detail()->create(['alat_id' => $alat->id, 'jumlah' => 1]);

        // Gunakan stored procedure supaya stok_tersedia dikurangi dengan benar
        DB::statement('CALL sp_setujui_peminjaman(?, ?)', [$peminjaman->id, $petugas->id]);
        $peminjaman->refresh();

        return $peminjaman;
    }

    /**
     * Buat peminjaman menunggu_verifikasi dengan pengajuan pengembalian.
     */
    private function buatPeminjamanMenunggu(
        User $peminjam,
        User $petugas,
        Alat $alat,
        int $terlambatHari = 0
    ): Peminjaman {
        $peminjaman = $this->buatPeminjamanDipinjam($peminjam, $petugas, $alat, $terlambatHari);

        $peminjaman->update([
            'status'               => 'menunggu_verifikasi',
            'tgl_diajukan_kembali' => now()->toDateString(),
        ]);

        return $peminjaman;
    }

    // ====================================================================
    // PENGAJUAN PENGEMBALIAN (PEMINJAM)
    // ====================================================================

    public function test_peminjam_dapat_mengajukan_pengembalian(): void
    {
        $peminjam = User::where('username', 'peminjam')->first();
        $petugas  = User::where('username', 'petugas')->first();
        $alat     = Alat::first();

        $peminjaman = $this->buatPeminjamanDipinjam($peminjam, $petugas, $alat);

        $this->actingAs($peminjam)
            ->post(route('pengembalian.ajukan', $peminjaman))
            ->assertRedirect(route('peminjaman.saya'));

        $this->assertDatabaseHas('peminjaman', [
            'id'     => $peminjaman->id,
            'status' => 'menunggu_verifikasi',
        ]);
    }

    public function test_peminjam_lain_tidak_bisa_ajukan_pengembalian_milik_orang_lain(): void
    {
        $peminjam  = User::where('username', 'peminjam')->first();
        $petugas   = User::where('username', 'petugas')->first();
        $alat      = Alat::first();

        $peminjaman = $this->buatPeminjamanDipinjam($peminjam, $petugas, $alat);

        // Buat pengguna peminjam lain
        $peminjamLain = User::create([
            'nama'     => 'Peminjam Lain',
            'username' => 'peminjam_lain_99',
            'email'    => 'lain99@sekolah.sch.id',
            'no_telp'  => '089900000099',
            'password' => 'password123',
            'is_aktif' => true,
        ]);
        $peminjamLain->assignRole('peminjam');

        $this->actingAs($peminjamLain)
            ->post(route('pengembalian.ajukan', $peminjaman))
            ->assertForbidden();
    }

    // ====================================================================
    // ANTRIAN DAN PANTAU (PETUGAS)
    // ====================================================================

    public function test_petugas_dapat_melihat_antrian_pengembalian(): void
    {
        $petugas = User::where('username', 'petugas')->first();

        $this->actingAs($petugas)
            ->get(route('pengembalian.antrian'))
            ->assertOk()
            ->assertViewIs('pengembalian.antrian');
    }

    public function test_petugas_dapat_melihat_pemantauan_peminjaman(): void
    {
        $petugas = User::where('username', 'petugas')->first();

        $this->actingAs($petugas)
            ->get(route('pengembalian.pantau'))
            ->assertOk()
            ->assertViewIs('pengembalian.pantau');
    }

    // ====================================================================
    // VERIFIKASI PENGEMBALIAN
    // ====================================================================

    public function test_petugas_dapat_memverifikasi_pengembalian_tanpa_keterlambatan(): void
    {
        $petugas  = User::where('username', 'petugas')->first();
        $peminjam = User::where('username', 'peminjam')->first();
        $alat     = Alat::first();
        $stokAwal = $alat->stok_tersedia;

        // Peminjaman tanpa keterlambatan — stok_tersedia dikurangi oleh SP
        $peminjaman = $this->buatPeminjamanMenunggu($peminjam, $petugas, $alat);
        $detail     = $peminjaman->detail()->first();

        $this->actingAs($petugas)
            ->post(route('pengembalian.simpan', $peminjaman), [
                'tgl_kembali'     => now()->toDateString(),
                'denda_kerusakan' => 0,
                'kondisi'         => [$detail->id => 'baik'],
            ])
            ->assertRedirect();

        // Status peminjaman harus selesai (diset oleh trigger after insert)
        $this->assertDatabaseHas('peminjaman', [
            'id'     => $peminjaman->id,
            'status' => 'selesai',
        ]);

        // Stok tersedia kembali ke awal (trigger restore stok untuk kondisi baik)
        $alat->refresh();
        $this->assertEquals($stokAwal, $alat->stok_tersedia);

        // Denda 0 karena tidak terlambat
        $this->assertDatabaseHas('pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'total_denda'   => 0,
        ]);
    }

    public function test_verifikasi_alat_rusak_berat_mengurangi_stok_total(): void
    {
        $petugas  = User::where('username', 'petugas')->first();
        $peminjam = User::where('username', 'peminjam')->first();
        $alat     = Alat::first();

        $peminjaman = $this->buatPeminjamanMenunggu($peminjam, $petugas, $alat);
        $detail     = $peminjaman->detail()->first();
        $stokAwal   = $alat->fresh()->stok;

        $this->actingAs($petugas)
            ->post(route('pengembalian.simpan', $peminjaman), [
                'tgl_kembali'     => now()->toDateString(),
                'denda_kerusakan' => 50000,
                'kondisi'         => [$detail->id => 'rusak_berat'],
            ])
            ->assertRedirect();

        // Stok total harus berkurang (trigger kurangi stok untuk rusak_berat)
        $alat->refresh();
        $this->assertEquals($stokAwal - 1, $alat->stok);
    }

    public function test_denda_keterlambatan_dihitung_oleh_trigger(): void
    {
        $petugas  = User::where('username', 'petugas')->first();
        $peminjam = User::where('username', 'peminjam')->first();
        $alat     = Alat::first();

        // Peminjaman sudah terlambat 3 hari
        $peminjaman = $this->buatPeminjamanMenunggu($peminjam, $petugas, $alat, terlambatHari: 3);
        $detail     = $peminjaman->detail()->first();

        $this->actingAs($petugas)
            ->post(route('pengembalian.simpan', $peminjaman), [
                'tgl_kembali'     => now()->toDateString(),
                'denda_kerusakan' => 0,
                'kondisi'         => [$detail->id => 'baik'],
            ])
            ->assertRedirect();

        $pengembalian = Pengembalian::where('peminjaman_id', $peminjaman->id)->first();
        $this->assertNotNull($pengembalian);
        $this->assertGreaterThan(0, $pengembalian->denda);
        $this->assertEquals(3, $pengembalian->hari_terlambat);
    }

    public function test_form_verifikasi_wajib_isi_kondisi_semua_alat(): void
    {
        $petugas  = User::where('username', 'petugas')->first();
        $peminjam = User::where('username', 'peminjam')->first();
        $alat     = Alat::first();

        $peminjaman = $this->buatPeminjamanMenunggu($peminjam, $petugas, $alat);

        $this->actingAs($petugas)
            ->post(route('pengembalian.simpan', $peminjaman), [
                'tgl_kembali'     => now()->toDateString(),
                'denda_kerusakan' => 0,
                // 'kondisi' sengaja tidak diisi
            ])
            ->assertSessionHasErrors('kondisi');
    }

    public function test_halaman_rincian_pengembalian_dapat_diakses_petugas(): void
    {
        $petugas  = User::where('username', 'petugas')->first();
        $peminjam = User::where('username', 'peminjam')->first();
        $alat     = Alat::first();

        $peminjaman = $this->buatPeminjamanMenunggu($peminjam, $petugas, $alat);
        $detail     = $peminjaman->detail()->first();

        // Verifikasi untuk mendapat record pengembalian
        $this->actingAs($petugas)
            ->post(route('pengembalian.simpan', $peminjaman), [
                'tgl_kembali'     => now()->toDateString(),
                'denda_kerusakan' => 0,
                'kondisi'         => [$detail->id => 'baik'],
            ]);

        $pengembalian = Pengembalian::where('peminjaman_id', $peminjaman->id)->first();
        $this->assertNotNull($pengembalian);

        $this->actingAs($petugas)
            ->get(route('pengembalian.rincian', $pengembalian))
            ->assertOk()
            ->assertViewIs('pengembalian.rincian');
    }
}
