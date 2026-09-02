<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $daftarKategori = [
            ['nama' => 'Perkakas Tangan', 'deskripsi' => 'Obeng, tang, kunci, palu'],
            ['nama' => 'Alat Ukur', 'deskripsi' => 'Multimeter, jangka sorong, mistar baja'],
            ['nama' => 'Perangkat Jaringan', 'deskripsi' => 'Switch, router, tang crimping'],
            ['nama' => 'Perangkat Audio Visual', 'deskripsi' => 'Proyektor, kamera, tripod'],
        ];

        foreach ($daftarKategori as $data) {
            Kategori::firstOrCreate(['nama' => $data['nama']], $data);
        }
    }
}
