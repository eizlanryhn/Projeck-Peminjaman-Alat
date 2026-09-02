<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        $daftarPengguna = [
            [
                'nama' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@sekolah.sch.id',
                'no_telp' => '081200800001',
                'peran' => 'admin',
            ],
            [
                'nama' => 'Petugas Laboratorium',
                'username' => 'petugas',
                'email' => 'petugas@sekolah.sch.id',
                'no_telp' => '081200800002',
                'peran' => 'petugas',
            ],
            [
                'nama' => 'Siswa Peminjam',
                'username' => 'peminjam',
                'email' => 'peminjam@sekolah.sch.id',
                'no_telp' => '081200800003',
                'peran' => 'peminjam',
            ],
        ];

        foreach ($daftarPengguna as $data) {
            $pengguna = User::firstOrCreate(
                ['username' => $data['username']],
                [
                    'nama' => $data['nama'],
                    'email' => $data['email'],
                    'no_telp' => $data['no_telp'],
                    'password' => 'password123',
                    'is_aktif' => true,
                ]
            );

            $pengguna->syncRoles([$data['peran']]);
        }
    }
}
