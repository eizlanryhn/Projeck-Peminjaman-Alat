<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PeranIzinSeeder extends Seeder
{
    public function run(): void
    {
        $daftarIzin = [
            // Izin milik Admin
            'user.kelola',
            'alat.kelola',
            'kategori.kelola',
            'peminjaman.kelola',
            'pengembalian.kelola',
            'log.lihat',
            'pengaturan.kelola',
            // Izin milik Petugas
            'peminjaman.setujui',
            'pengembalian.pantau',
            'laporan.cetak',
            // Izin milik Peminjam
            'alat.lihat',
            'peminjaman.ajukan',
            'peminjaman.kembalikan',
        ];

        foreach ($daftarIzin as $namaIzin) {
            Permission::firstOrCreate([
                'name' => $namaIzin,
                'guard_name' => 'web',
            ]);
        }

        $peranAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $peranPetugas = Role::firstOrCreate(['name' => 'petugas', 'guard_name' => 'web']);
        $peranPeminjam = Role::firstOrCreate(['name' => 'peminjam', 'guard_name' => 'web']);

        $peranAdmin->syncPermissions([
            'user.kelola',
            'alat.kelola',
            'kategori.kelola',
            'peminjaman.kelola',
            'pengembalian.kelola',
            'log.lihat',
            'pengaturan.kelola',
        ]);

        $peranPetugas->syncPermissions([
            'peminjaman.setujui',
            'pengembalian.pantau',
            'laporan.cetak',
        ]);

        $peranPeminjam->syncPermissions([
            'alat.lihat',
            'peminjaman.ajukan',
            'peminjaman.kembalikan',
        ]);
    }
}
