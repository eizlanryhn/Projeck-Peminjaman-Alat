<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Services\PeminjamanService;
use App\Services\PengembalianService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PengembalianController extends Controller
{
    public function __construct(
        private PengembalianService $layanan,
        private PeminjamanService $layananPeminjaman
    ) {
    }

    public function ajukan(Peminjaman $peminjaman)
    {
        abort_unless($peminjaman->user_id === auth()->id(), 403);
        $this->layananPeminjaman->ajukanPengembalian($peminjaman);

        return redirect()
            ->route('peminjaman.saya')
            ->with('sukses', 'Pengembalian ' . $peminjaman->kode_pinjam . ' diajukan. Menunggu verifikasi petugas.');
    }

    public function pantau()
    {
        $daftarPeminjaman = $this->layanan->daftarSedangDipinjam();

        return view('pengembalian.pantau', compact('daftarPeminjaman'));
    }

    public function antrian()
    {
        $daftarAntrian = $this->layanan->antrianVerifikasi();

        return view('pengembalian.antrian', compact('daftarAntrian'));
    }

    public function formVerifikasi(Peminjaman $peminjaman)
    {
        $peminjaman->load(['peminjam', 'detail.alat']);

        return view('pengembalian.form', compact('peminjaman'));
    }

    public function simpanVerifikasi(Request $request, Peminjaman $peminjaman)
    {
        $daftarDetailId = $peminjaman->detail->pluck('id')->all();
        $data = $request->validate([
            'tgl_kembali'     => ['required', 'date'],
            'denda_kerusakan' => ['nullable', 'numeric', 'min:0'],
            'catatan'         => ['nullable', 'string', 'max:500'],
            'kondisi'         => ['required', 'array', 'size:' . count($daftarDetailId)],
            'kondisi.*'       => [
                'required',
                Rule::in(['baik', 'rusak_ringan', 'rusak_berat', 'hilang']),
            ],
        ], [
            'kondisi.required' => 'Kondisi setiap alat wajib diisi.',
            'kondisi.size'     => 'Kondisi setiap alat wajib diisi.',
        ]);

        $pengembalian = $this->layanan->verifikasi(
            $peminjaman,
            auth()->id(),
            $data['kondisi'],
            $data['tgl_kembali'],
            (float) ($data['denda_kerusakan'] ?? 0),
            $data['catatan'] ?? null
        );

        return redirect()
            ->route('pengembalian.rincian', $pengembalian)
            ->with('sukses', 'Pengembalian berhasil diverifikasi.');
    }

    public function rincian(Pengembalian $pengembalian)
    {
        $pengembalian->load(['peminjaman.detail.alat', 'peminjaman.peminjam', 'petugas']);

        return view('pengembalian.rincian', compact('pengembalian'));
    }
}
