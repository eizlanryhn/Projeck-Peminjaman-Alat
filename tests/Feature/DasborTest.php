<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DasborTest extends TestCase
{
    public function test_admin_dapat_melihat_dasbor_admin(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin)
            ->get(route('admin.dasbor'))
            ->assertOk()
            ->assertViewIs('dasbor.admin')
            ->assertViewHas([
                'totalAlat',
                'totalStok',
                'stokTersedia',
                'totalKategori',
                'totalPengguna',
                'peminjamanAktif',
                'totalDenda',
                'logTerbaru',
                'peminjamanTerbaru',
            ]);
    }

    public function test_petugas_dapat_melihat_dasbor_petugas(): void
    {
        $petugas = User::where('username', 'petugas')->first();

        $this->actingAs($petugas)
            ->get(route('petugas.dasbor'))
            ->assertOk()
            ->assertViewIs('dasbor.petugas')
            ->assertViewHas([
                'antrianPersetujuan',
                'antrianVerifikasi',
                'peminjamanSedangDipinjam',
                'peminjamanLewatTenggat',
                'daftarPengajuan',
                'daftarPerluVerifikasi',
            ]);
    }

    public function test_peminjam_dapat_melihat_dasbor_peminjam(): void
    {
        $peminjam = User::where('username', 'peminjam')->first();

        $this->actingAs($peminjam)
            ->get(route('peminjam.dasbor'))
            ->assertOk()
            ->assertViewIs('dasbor.peminjam')
            ->assertViewHas([
                'dipinjamCount',
                'diajukanCount',
                'menungguVerifikasiCount',
                'selesaiCount',
                'daftarTunggakan',
                'pinjamanAktif',
            ]);
    }

    public function test_peminjam_tidak_bisa_akses_dasbor_admin(): void
    {
        $peminjam = User::where('username', 'peminjam')->first();

        $this->actingAs($peminjam)
            ->get(route('admin.dasbor'))
            ->assertForbidden();
    }

    public function test_peminjam_tidak_bisa_akses_dasbor_petugas(): void
    {
        $peminjam = User::where('username', 'peminjam')->first();

        $this->actingAs($peminjam)
            ->get(route('petugas.dasbor'))
            ->assertForbidden();
    }

    public function test_guest_diarahkan_ke_login_saat_akses_dasbor(): void
    {
        $this->get(route('admin.dasbor'))
            ->assertRedirect(route('login'));
    }
}
