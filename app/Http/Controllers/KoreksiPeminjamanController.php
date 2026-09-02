<?php

namespace App\Http\Controllers;

use App\Http\Requests\KoreksiPeminjamanRequest;
use App\Models\Peminjaman;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class KoreksiPeminjamanController extends Controller
{
    use AuthorizesRequests;

    public function daftar()
    {
        $daftarPeminjaman = Peminjaman::with(['peminjam', 'petugas'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('koreksi.peminjaman-daftar', compact('daftarPeminjaman'));
    }

    public function formUbah(Peminjaman $peminjaman)
    {
        $this->authorize('update', $peminjaman);

        $peminjaman->load('detail.alat');

        return view('koreksi.peminjaman-form', compact('peminjaman'));
    }

    public function perbarui(KoreksiPeminjamanRequest $request, Peminjaman $peminjaman)
    {
        $this->authorize('update', $peminjaman);

        $peminjaman->update($request->validated());

        return redirect()
            ->route('koreksi.peminjaman.daftar')
            ->with('sukses', 'Data peminjaman ' . $peminjaman->kode_pinjam . ' dikoreksi.');
    }

    public function hapus(Peminjaman $peminjaman)
    {
        $this->authorize('delete', $peminjaman);

        try {
            $peminjaman->delete();
        } catch (QueryException $e) {
            return back()->with('gagal', $e->errorInfo[2] ?? 'Data tidak dapat dihapus.');
        }

        return redirect()
            ->route('koreksi.peminjaman.daftar')
            ->with('sukses', 'Data peminjaman dihapus.');
    }
}
