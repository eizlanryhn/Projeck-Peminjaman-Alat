<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'nama', 'username', 'email', 'password', 'no_telp', 'is_aktif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function peminjamanDiajukan()
    {
        return $this->hasMany(Peminjaman::class, 'user_id');
    }
}
