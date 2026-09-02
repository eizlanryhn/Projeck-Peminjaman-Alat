<?php

namespace App\Listeners;

use App\Models\LogAktivitas;
use Illuminate\Auth\Events\Logout;

class CatatLogout
{
    public function handle(Logout $event): void
    {
        if (! $event->user) {
            return;
        }

        LogAktivitas::create([
            'user_id'      => $event->user->id,
            'aksi'         => 'logout',
            'tabel_tujuan' => 'users',
            'deskripsi'    => 'Pengguna ' . $event->user->username . ' keluar dari sistem',
            'ip_address'   => request()->ip(),
        ]);
    }
}
