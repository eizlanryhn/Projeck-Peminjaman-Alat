<?php

namespace App\Http\Controllers;

use App\Http\Requests\KoreksiPengembalianRequest;
use App\Models\Pengembalian;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class KoreksiPengembalianController extends Controller
{
    use AuthorizesRequests;

    public function daftar()
    {
        $daftarPengembalian = Pengembalian::with([
            'peminjaman.peminjam',
            'petugas',
        ])
        ->orderByDesc('tgl_kembali')
        ->paginate(10);

        return view('koreksi.pengembalian-daftar', compact('daftarPengembalian'));
    }

    public function formUbah(Pengembalian $pengembalian)
    {
        $this->authorize('update', $pengembalian);

        $pengembalian->load('peminjaman.detail.alat');

        return view('koreksi.pengembalian-form', compact('pengembalian'));
    }

    public function perbarui(KoreksiPengembalianRequest $request, Pengembalian $pengembalian)
    {
        $this->authorize('update', $pengembalian);

        $pengembalian->update($request->validated());

        return redirect()
            ->route('koreksi.pengembalian.daftar')
            ->with('sukses', 'Data pengembalian dikoreksi. Total denda dihitung ulang sistem.');
    }
}
