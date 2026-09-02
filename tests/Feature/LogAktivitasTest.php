<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LogAktivitas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pengujian 6.1.7 – Log Aktivitas
 *
 * No | Aktivitas                            | Aksi yang harus muncul
 * ---|--------------------------------------|------------------------
 *  1 | Masuk sebagai admin                  | login
 *  2 | Keluar lalu masuk lagi               | logout
 *  3 | Masuk dengan password salah          | login_gagal
 *  4 | Tambah kategori baru                 | create pada tabel kategori
 *  5 | Ubah data alat                       | update pada tabel alat
 *  6 | Hapus kategori kosong                | delete pada tabel kategori
 *  7 | Tambah pengguna baru dengan peran    | create dan beri_peran
 *  8 | Petugas menyetujui peminjaman        | setujui
 *  9 | Petugas memverifikasi pengembalian   | verifikasi_kembali
 */
class LogAktivitasTest extends TestCase
{
    // -----------------------------------------------------------------------
    // Uji 1 – Login admin tercatat sebagai 'login'
    // -----------------------------------------------------------------------
    public function test_login_admin_tercatat_di_log(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->post(route('login'), [
            'username' => 'admin',
            'password' => 'password123',
        ]);

        $this->assertDatabaseHas('log_aktivitas', [
            'user_id' => $admin->id,
            'aksi'    => 'login',
        ]);
    }

    // -----------------------------------------------------------------------
    // Uji 2 – Logout tercatat sebagai 'logout'
    // -----------------------------------------------------------------------
    public function test_logout_tercatat_di_log(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin)
            ->post(route('logout'));

        $this->assertDatabaseHas('log_aktivitas', [
            'user_id' => $admin->id,
            'aksi'    => 'logout',
        ]);
    }

    // -----------------------------------------------------------------------
    // Uji 3 – Login dengan password salah tercatat sebagai 'login_gagal'
    // -----------------------------------------------------------------------
    public function test_login_gagal_tercatat_di_log(): void
    {
        $this->post(route('login'), [
            'username' => 'admin',
            'password' => 'salah_banget',
        ]);

        $this->assertDatabaseHas('log_aktivitas', [
            'aksi' => 'login_gagal',
        ]);
    }

    // -----------------------------------------------------------------------
    // Uji 4 – Tambah kategori tercatat sebagai 'create' di tabel kategori
    // -----------------------------------------------------------------------
    public function test_tambah_kategori_tercatat_di_log(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin)
            ->post(route('kategori.store'), [
                'nama'     => 'Kategori Uji Log',
                'deskripsi' => 'Deskripsi uji',
            ]);

        $this->assertDatabaseHas('log_aktivitas', [
            'user_id'      => $admin->id,
            'aksi'         => 'create',
            'tabel_tujuan' => 'kategori',
        ]);
    }

    // -----------------------------------------------------------------------
    // Uji 5 – Ubah data alat tercatat sebagai 'update' di tabel alat
    // -----------------------------------------------------------------------
    public function test_ubah_alat_tercatat_di_log(): void
    {
        $admin = User::where('username', 'admin')->first();
        $alat  = \App\Models\Alat::first();

        $this->actingAs($admin)
            ->put(route('alat.update', $alat), [
                'kode_alat'     => $alat->kode_alat,
                'nama'          => $alat->nama . ' (diubah)',
                'kategori_id'   => $alat->kategori_id,
                'stok'          => $alat->stok,
                'stok_tersedia' => $alat->stok_tersedia,
                'kondisi'       => 'baik',
            ]);

        $this->assertDatabaseHas('log_aktivitas', [
            'user_id'      => $admin->id,
            'aksi'         => 'update',
            'tabel_tujuan' => 'alat',
        ]);
    }

    // -----------------------------------------------------------------------
    // Uji 6 – Hapus kategori kosong tercatat sebagai 'delete' di tabel kategori
    // -----------------------------------------------------------------------
    public function test_hapus_kategori_tercatat_di_log(): void
    {
        $admin = User::where('username', 'admin')->first();

        // Buat kategori kosong (tanpa alat)
        $kategori = \App\Models\Kategori::create([
            'nama'      => 'Kategori Hapus Uji',
            'deskripsi' => '-',
        ]);

        $this->actingAs($admin)
            ->delete(route('kategori.destroy', $kategori));

        $this->assertDatabaseHas('log_aktivitas', [
            'user_id'      => $admin->id,
            'aksi'         => 'delete',
            'tabel_tujuan' => 'kategori',
        ]);
    }

    // -----------------------------------------------------------------------
    // Uji 7 – Tambah pengguna baru dengan peran: muncul 'create' & 'beri_peran'
    // -----------------------------------------------------------------------
    public function test_tambah_pengguna_tercatat_create_dan_beri_peran(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin)
            ->post(route('pengguna.store'), [
                'nama'     => 'Pengguna Test Log',
                'username' => 'pengguna_log_test',
                'email'    => 'logtest@sekolah.sch.id',
                'no_telp'  => '089900000001',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'peran'    => 'peminjam',
                'is_aktif' => true,
            ]);

        // Observer mencatat create
        $this->assertDatabaseHas('log_aktivitas', [
            'user_id'      => $admin->id,
            'aksi'         => 'create',
            'tabel_tujuan' => 'users',
        ]);

        // Trigger trg_beri_peran mencatat beri_peran
        $this->assertDatabaseHas('log_aktivitas', [
            'aksi'         => 'beri_peran',
            'tabel_tujuan' => 'model_has_roles',
        ]);
    }

    // -----------------------------------------------------------------------
    // Uji 8 – Petugas menyetujui peminjaman tercatat 'setujui'
    // -----------------------------------------------------------------------
    public function test_setujui_peminjaman_tercatat_di_log(): void
    {
        $petugas   = User::where('username', 'petugas')->first();
        $peminjam  = User::where('username', 'peminjam')->first();
        $alat      = \App\Models\Alat::first();

        // Buat peminjaman dalam status diajukan
        $peminjaman = \App\Models\Peminjaman::create([
            'kode_pinjam'       => 'PJM-TEST-LOG-001',
            'user_id'           => $peminjam->id,
            'tgl_pinjam'        => now()->toDateString(),
            'tgl_harus_kembali' => now()->addDays(7)->toDateString(),
            'status'            => 'diajukan',
        ]);
        $peminjaman->detail()->create(['alat_id' => $alat->id, 'jumlah' => 1]);

        $this->actingAs($petugas)
            ->post(route('persetujuan.setujui', $peminjaman), [
                'tgl_harus_kembali' => now()->addDays(7)->toDateString(),
            ]);

        $this->assertDatabaseHas('log_aktivitas', [
            'aksi' => 'setujui',
        ]);
    }

    // -----------------------------------------------------------------------
    // Uji 9 – Petugas memverifikasi pengembalian tercatat 'verifikasi_kembali'
    // -----------------------------------------------------------------------
    public function test_verifikasi_pengembalian_tercatat_di_log(): void
    {
        $petugas  = User::where('username', 'petugas')->first();
        $peminjam = User::where('username', 'peminjam')->first();
        $alat     = \App\Models\Alat::first();

        // Buat peminjaman yang sudah disetujui (stok dikurangi SP)
        $peminjaman = \App\Models\Peminjaman::create([
            'kode_pinjam'       => 'PJM-TEST-LOG-002',
            'user_id'           => $peminjam->id,
            'tgl_pinjam'        => now()->toDateString(),
            'tgl_harus_kembali' => now()->addDays(7)->toDateString(),
            'status'            => 'diajukan',
        ]);
        $peminjaman->detail()->create(['alat_id' => $alat->id, 'jumlah' => 1]);

        // Setujui via SP agar stok_tersedia berkurang
        \Illuminate\Support\Facades\DB::statement(
            'CALL sp_setujui_peminjaman(?, ?)',
            [$peminjaman->id, $petugas->id]
        );

        $peminjaman->update([
            'status'               => 'menunggu_verifikasi',
            'tgl_diajukan_kembali' => now()->toDateString(),
        ]);

        $detail = $peminjaman->detail()->first();

        $this->actingAs($petugas)
            ->post(route('pengembalian.simpan', $peminjaman), [
                'tgl_kembali'     => now()->toDateString(),
                'denda_kerusakan' => 0,
                'kondisi'         => [$detail->id => 'baik'],
            ]);

        $this->assertDatabaseHas('log_aktivitas', [
            'aksi' => 'verifikasi_kembali',
        ]);
    }

    // -----------------------------------------------------------------------
    // Uji tambahan – Halaman log hanya bisa dibuka admin (403 untuk petugas)
    // -----------------------------------------------------------------------
    public function test_halaman_log_hanya_untuk_admin(): void
    {
        $petugas = User::where('username', 'petugas')->first();

        $this->actingAs($petugas)
            ->get(route('log.index'))
            ->assertForbidden();
    }

    public function test_admin_dapat_membuka_halaman_log(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin)
            ->get(route('log.index'))
            ->assertOk()
            ->assertViewIs('log.daftar');
    }

    // -----------------------------------------------------------------------
    // Uji – Filter log berdasarkan aksi
    // -----------------------------------------------------------------------
    public function test_filter_log_berdasarkan_aksi(): void
    {
        $admin = User::where('username', 'admin')->first();

        // Buat satu log manual
        LogAktivitas::create([
            'user_id'      => $admin->id,
            'aksi'         => 'login',
            'tabel_tujuan' => 'users',
            'deskripsi'    => 'Test filter',
            'created_at'   => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('log.index', ['aksi' => 'login']))
            ->assertOk()
            ->assertSee('login');
    }
}
