<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use Tests\TestCase;

/**
 * Pengujian BAB 3 – Master Data
 * Mencakup CRUD Kategori, CRUD Alat, CRUD Pengguna.
 */
class MasterDataTest extends TestCase
{
    // ====================================================================
    // KATEGORI
    // ====================================================================

    public function test_admin_dapat_melihat_daftar_kategori(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin)
            ->get(route('kategori.index'))
            ->assertOk()
            ->assertViewIs('kategori.index');
    }

    public function test_admin_dapat_menambah_kategori(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin)
            ->post(route('kategori.store'), [
                'nama'      => 'Elektronik Uji',
                'deskripsi' => 'Kategori untuk pengujian',
            ])
            ->assertRedirect(route('kategori.index'));

        $this->assertDatabaseHas('kategori', ['nama' => 'Elektronik Uji']);
    }

    public function test_admin_dapat_mengubah_kategori(): void
    {
        $admin    = User::where('username', 'admin')->first();
        $kategori = Kategori::first();

        $this->actingAs($admin)
            ->put(route('kategori.update', $kategori), [
                'nama'      => 'Nama Baru Kategori',
                'deskripsi' => 'Diubah',
            ])
            ->assertRedirect(route('kategori.index'));

        $this->assertDatabaseHas('kategori', ['nama' => 'Nama Baru Kategori']);
    }

    public function test_admin_dapat_menghapus_kategori_kosong(): void
    {
        $admin    = User::where('username', 'admin')->first();
        $kategori = Kategori::create(['nama' => 'Kategori Hapus', 'deskripsi' => '-']);

        $this->actingAs($admin)
            ->delete(route('kategori.destroy', $kategori))
            ->assertRedirect(route('kategori.index'));

        $this->assertDatabaseMissing('kategori', ['id' => $kategori->id]);
    }

    public function test_peminjam_tidak_bisa_akses_kategori(): void
    {
        $peminjam = User::where('username', 'peminjam')->first();

        $this->actingAs($peminjam)
            ->get(route('kategori.index'))
            ->assertForbidden();
    }

    // ====================================================================
    // ALAT
    // ====================================================================

    public function test_admin_dapat_melihat_daftar_alat(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin)
            ->get(route('alat.index'))
            ->assertOk()
            ->assertViewIs('alat.index');
    }

    public function test_admin_dapat_menambah_alat(): void
    {
        $admin    = User::where('username', 'admin')->first();
        $kategori = Kategori::first();

        $this->actingAs($admin)
            ->post(route('alat.store'), [
                'kode_alat'     => 'UJI-999',
                'nama'          => 'Alat Uji Baru',
                'kategori_id'   => $kategori->id,
                'stok'          => 5,
                'stok_tersedia' => 5,  // field wajib sesuai AlatRequest
                'kondisi'       => 'baik',
            ])
            ->assertRedirect(route('alat.index'));

        $this->assertDatabaseHas('alat', ['kode_alat' => 'UJI-999']);
    }

    public function test_admin_dapat_mengubah_alat(): void
    {
        $admin = User::where('username', 'admin')->first();
        $alat  = Alat::first();

        $this->actingAs($admin)
            ->put(route('alat.update', $alat), [
                'kode_alat'     => $alat->kode_alat,
                'nama'          => 'Nama Alat Diubah',
                'kategori_id'   => $alat->kategori_id,
                'stok'          => $alat->stok,
                'stok_tersedia' => $alat->stok_tersedia,  // field wajib sesuai AlatRequest
                'kondisi'       => 'baik',
            ])
            ->assertRedirect(route('alat.index'));

        $this->assertDatabaseHas('alat', ['nama' => 'Nama Alat Diubah']);
    }

    public function test_stok_harus_angka_positif(): void
    {
        $admin    = User::where('username', 'admin')->first();
        $kategori = Kategori::first();

        $this->actingAs($admin)
            ->post(route('alat.store'), [
                'kode_alat'     => 'UJI-INVALID',
                'nama'          => 'Alat Stok Salah',
                'kategori_id'   => $kategori->id,
                'stok'          => -1,
                'stok_tersedia' => 0,
                'kondisi'       => 'baik',
            ])
            ->assertSessionHasErrors('stok');
    }

    // ====================================================================
    // PENGGUNA
    // ====================================================================

    public function test_admin_dapat_melihat_daftar_pengguna(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin)
            ->get(route('pengguna.index'))
            ->assertOk()
            ->assertViewIs('pengguna.index');
    }

    public function test_admin_dapat_menambah_pengguna(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin)
            ->post(route('pengguna.store'), [
                'nama'                  => 'Pengguna Baru Test',
                'username'              => 'pengguna_baru_999',
                'email'                 => 'baru999@sekolah.sch.id',
                'no_telp'               => '089900000099',
                'password'              => 'password123',
                'password_confirmation' => 'password123',
                'peran'                 => 'peminjam',
                'is_aktif'              => true,
            ])
            ->assertRedirect(route('pengguna.index'));

        $this->assertDatabaseHas('users', ['username' => 'pengguna_baru_999']);
    }

    public function test_username_harus_unik(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin)
            ->post(route('pengguna.store'), [
                'nama'                  => 'Duplikat Admin',
                'username'              => 'admin', // sudah ada
                'email'                 => 'baru@sekolah.sch.id',
                'no_telp'               => '089900000050',
                'password'              => 'password123',
                'password_confirmation' => 'password123',
                'peran'                 => 'admin',
                'is_aktif'              => true,
            ])
            ->assertSessionHasErrors('username');
    }
}
