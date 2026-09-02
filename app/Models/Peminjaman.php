<?php

namespace App\Models;

use App\Enums\StatusPeminjaman;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'kode_pinjam', 'user_id', 'petugas_id', 'tgl_pinjam',
        'tgl_harus_kembali', 'tgl_diajukan_kembali', 'status',
        'keperluan', 'alasan_tolak',
    ];

    protected function casts(): array
    {
        return [
            'status'               => StatusPeminjaman::class,
            'tgl_pinjam'           => 'date',
            'tgl_harus_kembali'    => 'date',
            'tgl_diajukan_kembali' => 'date',
        ];
    }

    public function peminjam()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function detail()
    {
        return $this->hasMany(DetailPeminjaman::class, 'peminjaman_id');
    }

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class, 'peminjaman_id');
    }

    public function lewatTenggat(): bool
    {
        return in_array($this->status, [
            StatusPeminjaman::Dipinjam,
            StatusPeminjaman::MenungguVerifikasi,
        ], true) && $this->tgl_harus_kembali->isPast();
    }
}
