<?php

namespace App\Policies;

use App\Enums\StatusPeminjaman;
use App\Models\Peminjaman;
use App\Models\User;

class PeminjamanPolicy
{
    public function update(User $pengguna, Peminjaman $peminjaman): bool
    {
        return $pengguna->can('peminjaman.kelola');
    }

    public function delete(User $pengguna, Peminjaman $peminjaman): bool
    {
        if (! $pengguna->can('peminjaman.kelola')) {
            return false;
        }

        return in_array($peminjaman->status, [
            StatusPeminjaman::Diajukan,
            StatusPeminjaman::Ditolak,
        ], true);
    }
}
