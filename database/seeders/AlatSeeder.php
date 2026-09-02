<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class AlatSeeder extends Seeder
{
    public function run(): void
    {
        $daftarAlat = [
            ['kode_alat' => 'PKT-001', 'nama' => 'Obeng Plus Set', 'kategori' => 'Perkakas Tangan', 'stok' => 10],
            ['kode_alat' => 'PKT-002', 'nama' => 'Tang Kombinasi', 'kategori' => 'Perkakas Tangan', 'stok' => 8],
            ['kode_alat' => 'PKT-003', 'nama' => 'Kunci Pas Set', 'kategori' => 'Perkakas Tangan', 'stok' => 6],
            ['kode_alat' => 'AUK-001', 'nama' => 'Multimeter Digital', 'kategori' => 'Alat Ukur', 'stok' => 12],
            ['kode_alat' => 'AUK-002', 'nama' => 'Jangka Sorong', 'kategori' => 'Alat Ukur', 'stok' => 9],
            ['kode_alat' => 'AUK-003', 'nama' => 'Mistar Baja 30 cm', 'kategori' => 'Alat Ukur', 'stok' => 15],
            ['kode_alat' => 'JAR-001', 'nama' => 'Tang Crimping RJ45', 'kategori' => 'Perangkat Jaringan', 'stok' => 10],
            ['kode_alat' => 'JAR-002', 'nama' => 'LAN Tester', 'kategori' => 'Perangkat Jaringan', 'stok' => 5],
            ['kode_alat' => 'JAR-003', 'nama' => 'Switch 8 Port', 'kategori' => 'Perangkat Jaringan', 'stok' => 4],
            ['kode_alat' => 'AVI-001', 'nama' => 'Proyektor Portabel', 'kategori' => 'Perangkat Audio Visual', 'stok' => 3],
            ['kode_alat' => 'AVI-002', 'nama' => 'Tripod Kamera', 'kategori' => 'Perangkat Audio Visual', 'stok' => 6],
            ['kode_alat' => 'AVI-003', 'nama' => 'Kamera Mirrorless', 'kategori' => 'Perangkat Audio Visual', 'stok' => 2],
        ];

        foreach ($daftarAlat as $data) {
            $kategori = Kategori::where('nama', $data['kategori'])->first();
            Alat::firstOrCreate(
                ['kode_alat' => $data['kode_alat']],
                [
                    'kategori_id' => $kategori->id,
                    'nama' => $data['nama'],
                    'stok' => $data['stok'],
                    'stok_tersedia' => $data['stok'],
                    'kondisi' => 'baik',
                ]
            );
        }
    }
}
