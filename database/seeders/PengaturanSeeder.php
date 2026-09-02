<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        $daftarPengaturan = [
            ['kunci' => 'tarif_denda_harian', 'nilai' => '5000'],
            ['kunci' => 'default_hari_pinjam', 'nilai' => '7'],
            ['kunci' => 'maks_hari_pinjam', 'nilai' => '30'],
            ['kunci' => 'nama_sekolah', 'nilai' => 'SMK Negeri 1 Contoh'],
        ];

        foreach ($daftarPengaturan as $data) {
            Pengaturan::firstOrCreate(
                ['kunci' => $data['kunci']],
                ['nilai' => $data['nilai']]
            );
        }
    }
}
