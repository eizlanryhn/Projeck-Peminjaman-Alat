<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Pengujian BAB 7 - 6.2.6 Halaman Pengaturan Sistem
 */
class PengaturanTest extends TestCase
{
    // -----------------------------------------------------------------------
    // Uji 1 - Buka menu Pengaturan: tampil isian terisi
    // -----------------------------------------------------------------------
    public function test_admin_dapat_melihat_halaman_pengaturan(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin)
            ->get(route('pengaturan.form'))
            ->assertOk()
            ->assertViewIs('pengaturan.form')
            ->assertSee('tarif_denda_harian')
            ->assertSee('nama_sekolah');
    }

    // -----------------------------------------------------------------------
    // Uji 2 & 8 - Ubah pengaturan, simpan dan periksa log aktivitas
    // -----------------------------------------------------------------------
    public function test_admin_dapat_mengubah_pengaturan_dan_tercatat_di_log(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin)
            ->put(route('pengaturan.perbarui'), [
                'nama_sekolah'        => 'SMK Negeri 1 Uji Coba',
                'tarif_denda_harian'  => 6000,
                'default_hari_pinjam' => 5,
                'maks_hari_pinjam'    => 20,
            ])
            ->assertRedirect(route('pengaturan.form'));

        $this->assertDatabaseHas('pengaturan', [
            'kunci' => 'nama_sekolah',
            'nilai' => 'SMK Negeri 1 Uji Coba',
        ]);

        $this->assertDatabaseHas('log_aktivitas', [
            'user_id'      => $admin->id,
            'aksi'         => 'ubah_pengaturan',
            'tabel_tujuan' => 'pengaturan',
        ]);
    }

    // -----------------------------------------------------------------------
    // Uji 3 - Durasi bawaan > maksimal ditolak
    // -----------------------------------------------------------------------
    public function test_durasi_bawaan_melebihi_maksimal_ditolak(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin)
            ->put(route('pengaturan.perbarui'), [
                'nama_sekolah'        => 'SMK Test',
                'tarif_denda_harian'  => 5000,
                'default_hari_pinjam' => 40,
                'maks_hari_pinjam'    => 30,
            ])
            ->assertSessionHasErrors('default_hari_pinjam');
    }

    // -----------------------------------------------------------------------
    // Uji 4 - Tarif denda negatif ditolak
    // -----------------------------------------------------------------------
    public function test_tarif_denda_negatif_ditolak(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin)
            ->put(route('pengaturan.perbarui'), [
                'nama_sekolah'        => 'SMK Test',
                'tarif_denda_harian'  => -1000,
                'default_hari_pinjam' => 7,
                'maks_hari_pinjam'    => 30,
            ])
            ->assertSessionHasErrors('tarif_denda_harian');
    }

    // -----------------------------------------------------------------------
    // Uji 9 - Trigger menolak penghapusan baris pengaturan
    // -----------------------------------------------------------------------
    public function test_trigger_menolak_penghapusan_pengaturan_di_db(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->expectExceptionMessage('Data pengaturan tidak dapat dihapus, hanya dapat diubah');

        DB::delete("DELETE FROM pengaturan LIMIT 1");
    }

    // -----------------------------------------------------------------------
    // Uji 10 - Petugas tidak boleh akses pengaturan (403 Forbidden)
    // -----------------------------------------------------------------------
    public function test_petugas_tidak_boleh_akses_pengaturan(): void
    {
        $petugas = User::where('username', 'petugas')->first();

        $this->actingAs($petugas)
            ->get(route('pengaturan.form'))
            ->assertForbidden();
    }
}
