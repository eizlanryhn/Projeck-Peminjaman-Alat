<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $fillable = ['nama', 'deskripsi'];

    public function daftarAlat()
    {
        return $this->hasMany(Alat::class, 'kategori_id');
    }
}
