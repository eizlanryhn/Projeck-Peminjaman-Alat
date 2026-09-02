<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $query = LogAktivitas::with('pengguna')
            ->orderByDesc('created_at');

        if ($request->filled('aksi')) {
            $query->where('aksi', $request->aksi);
        }

        if ($request->filled('tabel_tujuan')) {
            $query->where('tabel_tujuan', $request->tabel_tujuan);
        }

        if ($request->filled('cari')) {
            $query->where('deskripsi', 'like', '%' . $request->cari . '%');
        }

        $daftarAksi = LogAktivitas::select('aksi')->distinct()->orderBy('aksi')->pluck('aksi');
        $daftarTabel = LogAktivitas::select('tabel_tujuan')->distinct()->orderBy('tabel_tujuan')->pluck('tabel_tujuan');

        $daftarLog = $query->paginate(20)->withQueryString();

        return view('log.daftar', compact('daftarLog', 'daftarAksi', 'daftarTabel'));
    }
}
