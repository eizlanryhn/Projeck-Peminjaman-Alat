<?php

namespace App\Http\Controllers;

use App\Enums\StatusPeminjaman;
use App\Models\Alat;
use App\Models\Kategori;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Illuminate\Http\Request;

class DasborController extends Controller
{
    /**
     * Tampilan Dasbor Administrator
     */
    public function admin()
    {
        $data = [
            'totalAlat'         => Alat::count(),
            'totalStok'         => Alat::sum('stok'),
            'stokTersedia'      => Alat::sum('stok_tersedia'),
            'totalKategori'     => Kategori::count(),
            'totalPengguna'     => User::count(),
            'peminjamanAktif'   => Peminjaman::whereIn('status', [
                StatusPeminjaman::Dipinjam->value,
                StatusPeminjaman::MenungguVerifikasi->value,
            ])->count(),
            'totalDenda'        => Pengembalian::sum('total_denda') ?? 0,
            'logTerbaru'        => LogAktivitas::with('user')->latest()->take(6)->get(),
            'peminjamanTerbaru' => Peminjaman::with(['peminjam', 'detail.alat'])->latest()->take(5)->get(),
        ];

        return view('dasbor.admin', $data);
    }

    /**
     * Tampilan Dasbor Petugas Laboratorium
     */
    public function petugas()
    {
        $hariIni = now()->toDateString();

        $data = [
            'antrianPersetujuan'       => Peminjaman::where('status', StatusPeminjaman::Diajukan->value)->count(),
            'antrianVerifikasi'        => Peminjaman::where('status', StatusPeminjaman::MenungguVerifikasi->value)->count(),
            'peminjamanSedangDipinjam' => Peminjaman::where('status', StatusPeminjaman::Dipinjam->value)->count(),
            'peminjamanLewatTenggat'   => Peminjaman::whereIn('status', [
                StatusPeminjaman::Dipinjam->value,
                StatusPeminjaman::MenungguVerifikasi->value,
            ])->whereDate('tgl_harus_kembali', '<', $hariIni)->count(),
            'daftarPengajuan'          => Peminjaman::with(['peminjam', 'detail.alat'])
                ->where('status', StatusPeminjaman::Diajukan->value)
                ->latest()
                ->take(5)
                ->get(),
            'daftarPerluVerifikasi'    => Peminjaman::with(['peminjam', 'detail.alat'])
                ->where('status', StatusPeminjaman::MenungguVerifikasi->value)
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('dasbor.petugas', $data);
    }

    /**
     * Tampilan Dasbor Peminjam (Siswa / Guru)
     */
    public function peminjam(Request $request)
    {
        $user    = $request->user();
        $hariIni = now()->toDateString();

        $data = [
            'dipinjamCount'           => Peminjaman::where('user_id', $user->id)
                ->where('status', StatusPeminjaman::Dipinjam->value)
                ->count(),
            'diajukanCount'           => Peminjaman::where('user_id', $user->id)
                ->where('status', StatusPeminjaman::Diajukan->value)
                ->count(),
            'menungguVerifikasiCount' => Peminjaman::where('user_id', $user->id)
                ->where('status', StatusPeminjaman::MenungguVerifikasi->value)
                ->count(),
            'selesaiCount'            => Peminjaman::where('user_id', $user->id)
                ->where('status', StatusPeminjaman::Selesai->value)
                ->count(),
            'daftarTunggakan'         => Peminjaman::where('user_id', $user->id)
                ->whereIn('status', [
                    StatusPeminjaman::Dipinjam->value,
                    StatusPeminjaman::MenungguVerifikasi->value,
                ])
                ->whereDate('tgl_harus_kembali', '<', $hariIni)
                ->get(),
            'pinjamanAktif'           => Peminjaman::with('detail.alat')
                ->where('user_id', $user->id)
                ->whereIn('status', [
                    StatusPeminjaman::Diajukan->value,
                    StatusPeminjaman::Dipinjam->value,
                    StatusPeminjaman::MenungguVerifikasi->value,
                ])
                ->latest()
                ->get(),
        ];

        return view('dasbor.peminjam', $data);
    }
}
