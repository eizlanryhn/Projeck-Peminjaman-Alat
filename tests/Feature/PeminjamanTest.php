<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Pengujian BAB 4 – Modul Peminjaman & Persetujuan
 */
class PeminjamanTest extends TestCase
{
    // ====================================================================
    // KATALOG & KERANJANG
    // ====================================================================

    public function test_peminjam_dapat_melihat_katalog(): void
    {
        $peminjam = User::where('username', 'peminjam')->first();

        $this->actingAs($peminjam)
            ->get(route('katalog.daftar'))
            ->assertOk()
            ->assertViewIs('katalog.daftar');
    }

    public function test_peminjam_dapat_tambah_alat_ke_keranjang(): void
    {
        $peminjam = User::where('username', 'peminjam')->first();
        $alat     = Alat::first();

        $this->actingAs($peminjam)
            ->post(route('katalog.tambah', $alat), ['jumlah' => 2])
            ->assertRedirect();

        $this->actingAs($peminjam)
            ->get(route('katalog.keranjang'))
            ->assertSee($alat->nama);
    }

    public function test_jumlah_di_keranjang_tidak_boleh_melebihi_stok(): void
    {
        $peminjam = User::where('username', 'peminjam')->first();
        $alat     = Alat::first();

        $this->actingAs($peminjam)
            ->post(route('katalog.tambah', $alat), ['jumlah' => $alat->stok_tersedia + 100])
            ->assertSessionHasErrors();
    }

    public function test_peminjam_dapat_kosongkan_keranjang(): void
    {
        $peminjam = User::where('username', 'peminjam')->first();
        $alat     = Alat::first();

        // Session format keranjang: [alat_id => jumlah]
        $this->actingAs($peminjam)
            ->withSession(['keranjang' => [$alat->id => 1]])
            ->delete(route('katalog.kosongkan'))
            ->assertRedirect();
    }

    // ====================================================================
    // PENGAJUAN PEMINJAMAN
    // ====================================================================

    public function test_peminjam_dapat_mengajukan_peminjaman(): void
    {
        $peminjam = User::where('username', 'peminjam')->first();
        $alat     = Alat::first();

        // Session format keranjang: [alat_id => jumlah] (sesuai Keranjang service)
        $this->actingAs($peminjam)
            ->withSession(['keranjang' => [$alat->id => 1]])
            ->post(route('peminjaman.simpan'), [
                'tgl_pinjam'        => now()->toDateString(),
                'tgl_harus_kembali' => now()->addDays(7)->toDateString(),
                'keperluan'         => 'Praktikum',
            ])
            ->assertRedirect(route('peminjaman.saya'));

        $this->assertDatabaseHas('peminjaman', [
            'user_id' => $peminjam->id,
            'status'  => 'diajukan',
        ]);
    }

    public function test_tanggal_kembali_wajib_lebih_dari_tanggal_pinjam(): void
    {
        $peminjam = User::where('username', 'peminjam')->first();
        $alat     = Alat::first();

        $this->actingAs($peminjam)
            ->withSession(['keranjang' => [$alat->id => 1]])
            ->post(route('peminjaman.simpan'), [
                'tgl_pinjam'        => now()->toDateString(),
                'tgl_harus_kembali' => now()->subDays(1)->toDateString(),
                'keperluan'         => 'Praktikum',
            ])
            ->assertSessionHasErrors('tgl_harus_kembali');
    }

    public function test_peminjam_dapat_melihat_daftar_pinjaman_saya(): void
    {
        $peminjam = User::where('username', 'peminjam')->first();

        $this->actingAs($peminjam)
            ->get(route('peminjaman.saya'))
            ->assertOk()
            ->assertViewIs('peminjaman.saya');
    }

    // ====================================================================
    // PERSETUJUAN (PETUGAS)
    // ====================================================================

    public function test_petugas_dapat_melihat_antrian_persetujuan(): void
    {
        $petugas = User::where('username', 'petugas')->first();

        $this->actingAs($petugas)
            ->get(route('persetujuan.antrian'))
            ->assertOk()
            ->assertViewIs('persetujuan.antrian');
    }

    public function test_petugas_dapat_menyetujui_peminjaman(): void
    {
        $petugas  = User::where('username', 'petugas')->first();
        $peminjam = User::where('username', 'peminjam')->first();
        $alat     = Alat::first();

        $stokAwal = $alat->stok_tersedia;

        $peminjaman = Peminjaman::create([
            'kode_pinjam'       => 'PJM-SETUJUI-001',
            'user_id'           => $peminjam->id,
            'tgl_pinjam'        => now()->toDateString(),
            'tgl_harus_kembali' => now()->addDays(7)->toDateString(),
            'status'            => 'diajukan',
        ]);
        $peminjaman->detail()->create(['alat_id' => $alat->id, 'jumlah' => 1]);

        // Controller setujui() butuh tgl_harus_kembali dalam request
        $this->actingAs($petugas)
            ->post(route('persetujuan.setujui', $peminjaman), [
                'tgl_harus_kembali' => now()->addDays(7)->toDateString(),
            ])
            ->assertRedirect(route('persetujuan.antrian'));

        $this->assertDatabaseHas('peminjaman', [
            'id'     => $peminjaman->id,
            'status' => 'dipinjam',
        ]);

        // Stok tersedia harus berkurang (dihandle stored procedure)
        $alat->refresh();
        $this->assertEquals($stokAwal - 1, $alat->stok_tersedia);
    }

    public function test_petugas_dapat_menolak_peminjaman(): void
    {
        $petugas  = User::where('username', 'petugas')->first();
        $peminjam = User::where('username', 'peminjam')->first();
        $alat     = Alat::first();

        $peminjaman = Peminjaman::create([
            'kode_pinjam'       => 'PJM-TOLAK-001',
            'user_id'           => $peminjam->id,
            'tgl_pinjam'        => now()->toDateString(),
            'tgl_harus_kembali' => now()->addDays(7)->toDateString(),
            'status'            => 'diajukan',
        ]);
        $peminjaman->detail()->create(['alat_id' => $alat->id, 'jumlah' => 1]);

        $this->actingAs($petugas)
            ->post(route('persetujuan.tolak', $peminjaman), [
                'alasan_tolak' => 'Stok tidak mencukupi untuk saat ini',
            ])
            ->assertRedirect(route('persetujuan.antrian'));

        $this->assertDatabaseHas('peminjaman', [
            'id'     => $peminjaman->id,
            'status' => 'ditolak',
        ]);
    }

    public function test_peminjam_tidak_bisa_akses_persetujuan(): void
    {
        $peminjam = User::where('username', 'peminjam')->first();

        $this->actingAs($peminjam)
            ->get(route('persetujuan.antrian'))
            ->assertForbidden();
    }
}
