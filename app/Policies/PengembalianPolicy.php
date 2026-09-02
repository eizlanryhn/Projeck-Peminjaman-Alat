<?php

namespace App\Policies;

use App\Models\Pengembalian;
use App\Models\User;

class PengembalianPolicy
{
    public function update(User $pengguna, Pengembalian $pengembalian): bool
    {
        return $pengguna->can('pengembalian.kelola');
    }

    public function delete(User $user, Pengembalian $pengembalian): bool
    {
        return false;
    }
}
