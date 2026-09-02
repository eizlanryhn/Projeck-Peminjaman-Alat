<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function form()
    {
        $daftarKategori = Kategori::orderBy('nama')->get();

        return view('laporan.form', compact('daftarKategori'));
    }

    public function rpt01(Request $request)
    {
        $data = $request->validate([
            'tgl_awal'  => ['required', 'date'],
            'tgl_akhir' => ['required', 'date', 'after_or_equal:tgl_awal'],
            'status'    => ['nullable', 'string'],
        ]);

        $query = Peminjaman::with(['peminjam', 'detail.alat', 'petugas'])
            ->whereBetween('tgl_pinjam', [$data['tgl_awal'], $data['tgl_akhir']]);

        if (!empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        $daftarPeminjaman = $query->orderBy('tgl_pinjam')->get();
        $periode = $data['tgl_awal'] . ' s.d. ' . $data['tgl_akhir'];

        $pdf = Pdf::loadView('laporan.peminjaman', compact('daftarPeminjaman', 'periode'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('RPT-01-Peminjaman.pdf');
    }

    public function rpt02(Request $request)
    {
        $data = $request->validate([
            'tgl_awal'  => ['required', 'date'],
            'tgl_akhir' => ['required', 'date', 'after_or_equal:tgl_awal'],
        ]);

        $daftarPengembalian = Pengembalian::with(['peminjaman.peminjam', 'peminjaman.detail.alat', 'petugas'])
            ->whereBetween('tgl_kembali', [$data['tgl_awal'], $data['tgl_akhir']])
            ->orderBy('tgl_kembali')
            ->get();

        $periode = $data['tgl_awal'] . ' s.d. ' . $data['tgl_akhir'];

        $pdf = Pdf::loadView('laporan.pengembalian', compact('daftarPengembalian', 'periode'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('RPT-02-Pengembalian.pdf');
    }

    public function rpt03(Request $request)
    {
        $data = $request->validate([
            'kategori_id' => ['nullable', 'integer', 'exists:kategori,id'],
        ]);

        $query = Alat::with('kategori');

        if (!empty($data['kategori_id'])) {
            $query->where('kategori_id', $data['kategori_id']);
        }

        $daftarAlat = $query->orderBy('nama')->get();
        $kategoriDipilih = !empty($data['kategori_id'])
            ? Kategori::find($data['kategori_id'])
            : null;

        $pdf = Pdf::loadView('laporan.stok', compact('daftarAlat', 'kategoriDipilih'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('RPT-03-Stok.pdf');
    }
}
