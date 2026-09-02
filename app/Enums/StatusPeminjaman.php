<?php

namespace App\Enums;

enum StatusPeminjaman: string
{
    case Diajukan           = 'diajukan';
    case Ditolak            = 'ditolak';
    case Dipinjam           = 'dipinjam';
    case MenungguVerifikasi = 'menunggu_verifikasi';
    case Selesai            = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::Diajukan           => 'Diajukan',
            self::Ditolak            => 'Ditolak',
            self::Dipinjam           => 'Dipinjam',
            self::MenungguVerifikasi => 'Menunggu Verifikasi',
            self::Selesai            => 'Selesai',
        };
    }

    public function warna(): string
    {
        return match ($this) {
            self::Diajukan           => 'warning',
            self::Ditolak            => 'danger',
            self::Dipinjam           => 'primary',
            self::MenungguVerifikasi => 'info',
            self::Selesai            => 'success',
        };
    }

    public function bolehKe(self $tujuan): bool
    {
        return match ($this) {
            self::Diajukan => in_array($tujuan, [
                self::Dipinjam,
                self::Ditolak,
            ], true),
            self::Dipinjam           => $tujuan === self::MenungguVerifikasi,
            self::MenungguVerifikasi => $tujuan === self::Selesai,
            self::Ditolak, self::Selesai => false,
        };
    }
}
