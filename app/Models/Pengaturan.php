<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = ['kunci', 'nilai'];

    public static function ambil(string $kunci, $bawaan = null)
    {
        $baris = static::where('kunci', $kunci)->first();
        return $baris ? $baris->nilai : $bawaan;
    }
}
