<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    protected $table = 'pengembalian';

    protected $fillable = [
        'peminjaman_id', 'petugas_id', 'tgl_kembali',
        'denda_kerusakan', 'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tgl_kembali'     => 'date',
            'denda'           => 'float',
            'denda_kerusakan' => 'float',
            'total_denda'     => 'float',
        ];
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
