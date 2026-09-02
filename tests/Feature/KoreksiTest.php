<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Pengujian BAB 7 - 6.1.8 Uji Seluruh Lapis Pertahanan (Koreksi Admin)
 */
class KoreksiTest extends TestCase
{
    private static int $counter = 0;

    private function buatPeminjaman(string $status, ?User $peminjam = null, ?User $petugas = null, int $terlambatHari = 0): Peminjaman
    {
        self::$counter++;
        $peminjam = $peminjam ?? User::where('username', 'peminjam')->first();
        $petugas  = $petugas ?? User::where('username', 'petugas')->first();
        $alat     = Alat::first();

        $tglHarus = $terlambatHari > 0
            ? now()->subDays($terlambatHari)->toDateString()
            : now()->addDays(2)->toDateString();

        $peminjaman = Peminjaman::create([
            'kode_pinjam'       => 'PJM-KOR-' . str_pad((string) self::$counter, 3, '0', STR_PAD_LEFT),
            'user_id'           => $peminjam->id,
            'tgl_pinjam'        => now()->subDays($terlambatHari + 5)->toDateString(),
            'tgl_harus_kembali' => $tglHarus,
            'status'            => $status,
            'petugas_id'        => in_array($status, ['dipinjam', 'menunggu_verifikasi', 'selesai', 'ditolak']) ? $petugas->id : null,
        ]);
        $peminjaman->detail()->create(['alat_id' => $alat->id, 'jumlah' => 1]);

        return $peminjaman;
    }

    // -----------------------------------------------------------------------
    // Uji 1 - Admin membuka menu Data Peminjaman: seluruh peminjaman tampil
    // -----------------------------------------------------------------------
    public function test_admin_dapat_melihat_daftar_koreksi_peminjaman(): void
    {
        $admin = User::where('username', 'admin')->first();
        $this->buatPeminjaman('diajukan');

        $this->actingAs($admin)
            ->get(route('koreksi.peminjaman.daftar'))
            ->assertOk()
            ->assertViewIs('koreksi.peminjaman-daftar');
    }

    // -----------------------------------------------------------------------
    // Uji 2, 3 & 4 - Peminjaman diajukan bisa dihapus, peminjaman selesai tidak bisa
    // -----------------------------------------------------------------------
    public function test_admin_dapat_menghapus_peminjaman_diajukan(): void
    {
        $admin      = User::where('username', 'admin')->first();
        $peminjaman = $this->buatPeminjaman('diajukan');

        $this->actingAs($admin)
            ->delete(route('koreksi.peminjaman.hapus', $peminjaman))
            ->assertRedirect(route('koreksi.peminjaman.daftar'));

        $this->assertDatabaseMissing('peminjaman', ['id' => $peminjaman->id]);
    }

    public function test_admin_tidak_bisa_menghapus_peminjaman_selesai_dicegah_policy(): void
    {
        $admin      = User::where('username', 'admin')->first();
        $peminjaman = $this->buatPeminjaman('selesai');

        // Policy harus memblokir dengan 403 Forbidden
        $this->actingAs($admin)
            ->delete(route('koreksi.peminjaman.hapus', $peminjaman))
            ->assertForbidden();

        $this->assertDatabaseHas('peminjaman', ['id' => $peminjaman->id]);
    }

    // -----------------------------------------------------------------------
    // Uji 5 - Form koreksi peminjaman dan update data yang diizinkan
    // -----------------------------------------------------------------------
    public function test_admin_dapat_membuka_form_dan_mengoreksi_peminjaman(): void
    {
        $admin      = User::where('username', 'admin')->first();
        $peminjaman = $this->buatPeminjaman('diajukan');

        $this->actingAs($admin)
            ->get(route('koreksi.peminjaman.ubah', $peminjaman))
            ->assertOk()
            ->assertViewIs('koreksi.peminjaman-form');

        $this->actingAs($admin)
            ->put(route('koreksi.peminjaman.perbarui', $peminjaman), [
                'tgl_pinjam'        => now()->subDays(3)->toDateString(),
                'tgl_harus_kembali' => now()->addDays(4)->toDateString(),
                'keperluan'         => 'Keperluan diperbaiki oleh admin',
                'alasan_tolak'      => null,
            ])
            ->assertRedirect(route('koreksi.peminjaman.daftar'));

        $this->assertDatabaseHas('peminjaman', [
            'id'        => $peminjaman->id,
            'keperluan' => 'Keperluan diperbaiki oleh admin',
        ]);
    }

    // -----------------------------------------------------------------------
    // Uji 6 - Menu Data Pengembalian tampil
    // -----------------------------------------------------------------------
    public function test_admin_dapat_melihat_daftar_koreksi_pengembalian(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin)
            ->get(route('koreksi.pengembalian.daftar'))
            ->assertOk()
            ->assertViewIs('koreksi.pengembalian-daftar');
    }

    // -----------------------------------------------------------------------
    // Uji 7 - Koreksi denda kerusakan: total denda dihitung ulang oleh trigger
    // -----------------------------------------------------------------------
    public function test_koreksi_denda_kerusakan_menghitung_ulang_total_denda_via_trigger(): void
    {
        $admin    = User::where('username', 'admin')->first();
        $petugas  = User::where('username', 'petugas')->first();
        $peminjam = User::where('username', 'peminjam')->first();

        $peminjaman = $this->buatPeminjaman('selesai', $peminjam, $petugas, terlambatHari: 2);
        $detail     = $peminjaman->detail()->first();
        $detail->update(['kondisi_kembali' => 'rusak_ringan', 'denda' => 10000]);

        $pengembalian = Pengembalian::create([
            'peminjaman_id'   => $peminjaman->id,
            'petugas_id'      => $petugas->id,
            'tgl_kembali'     => now()->toDateString(),
            'hari_terlambat'  => 2,
            'denda'           => 10000,
            'denda_kerusakan' => 150000,
            'total_denda'     => 160000,
            'catatan'         => 'Rusak ringan',
        ]);

        $this->actingAs($admin)
            ->put(route('koreksi.pengembalian.perbarui', $pengembalian), [
                'denda_kerusakan' => 50000,
                'catatan'         => 'Denda kerusakan dikoreksi',
            ])
            ->assertRedirect(route('koreksi.pengembalian.daftar'));

        $pengembalian->refresh();
        $this->assertEquals(50000, (float) $pengembalian->denda_kerusakan);
        $this->assertEquals(10000, (float) $pengembalian->denda); // Denda keterlambatan tetap
        $this->assertEquals(60000, (float) $pengembalian->total_denda); // 10000 + 50000
    }

    // -----------------------------------------------------------------------
    // Uji Lapis Trigger Penjaga (Database Trigger Level)
    // -----------------------------------------------------------------------
    public function test_trigger_menolak_penghapusan_peminjaman_selesai_di_db(): void
    {
        $peminjaman = $this->buatPeminjaman('selesai');

        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->expectExceptionMessage('Peminjaman yang sudah diproses tidak dapat dihapus');

        DB::delete("DELETE FROM peminjaman WHERE id = ?", [$peminjaman->id]);
    }

    public function test_trigger_menolak_penghapusan_data_pengembalian_di_db(): void
    {
        $petugas    = User::where('username', 'petugas')->first();
        $peminjaman = $this->buatPeminjaman('selesai');

        $pengembalian = Pengembalian::create([
            'peminjaman_id'   => $peminjaman->id,
            'petugas_id'      => $petugas->id,
            'tgl_kembali'     => now()->toDateString(),
            'hari_terlambat'  => 0,
            'denda'           => 0,
            'denda_kerusakan' => 0,
            'total_denda'     => 0,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->expectExceptionMessage('Data pengembalian tidak dapat dihapus');

        DB::delete("DELETE FROM pengembalian WHERE id = ?", [$pengembalian->id]);
    }

    public function test_trigger_mengunci_denda_dan_hari_terlambat_saat_update_langsung(): void
    {
        $petugas    = User::where('username', 'petugas')->first();
        $peminjaman = $this->buatPeminjaman('selesai', terlambatHari: 3);
        $detail     = $peminjaman->detail()->first();
        $detail->update(['kondisi_kembali' => 'baik', 'denda' => 15000]);

        $pengembalian = Pengembalian::create([
            'peminjaman_id'   => $peminjaman->id,
            'petugas_id'      => $petugas->id,
            'tgl_kembali'     => now()->toDateString(),
            'hari_terlambat'  => 3,
            'denda'           => 15000,
            'denda_kerusakan' => 20000,
            'total_denda'     => 35000,
        ]);

        // Coba manipulasi denda menjadi 0 secara langsung di DB
        DB::update("UPDATE pengembalian SET denda = 0, denda_kerusakan = 50000 WHERE id = ?", [$pengembalian->id]);

        $pengembalian->refresh();
        // Trigger harus mengembalikan denda ke OLD.denda (15000) dan total_denda = 15000 + 50000 = 65000
        $this->assertEquals(15000, (float) $pengembalian->denda);
        $this->assertEquals(65000, (float) $pengembalian->total_denda);
    }
}
