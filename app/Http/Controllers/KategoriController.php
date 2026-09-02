<?php

namespace App\Http\Controllers;

use App\Http\Requests\KategoriRequest;
use App\Models\Kategori;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $kataKunci = $request->query('cari');
        $daftarKategori = Kategori::withCount('daftarAlat')
            ->when($kataKunci, function ($query, $kataKunci) {
                $query->where('nama', 'like', '%' . $kataKunci . '%');
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();
        return view('kategori.index', compact('daftarKategori', 'kataKunci'));
    }

    public function create()
    {
        $kategori = new Kategori();
        return view('kategori.form', compact('kategori'));
    }

    public function store(KategoriRequest $request)
    {
        Kategori::create($request->validated());
        return redirect()
            ->route('kategori.index')
            ->with('sukses', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori)
    {
        return view('kategori.form', compact('kategori'));
    }

    public function update(KategoriRequest $request, Kategori $kategori)
    {
        $kategori->update($request->validated());
        return redirect()
            ->route('kategori.index')
            ->with('sukses', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        try {
            $kategori->delete();
        } catch (QueryException $e) {
            return redirect()
                ->route('kategori.index')
                ->with('gagal', 'Kategori tidak dapat dihapus karena masih dipakai oleh data alat.');
        }
        return redirect()
            ->route('kategori.index')
            ->with('sukses', 'Kategori berhasil dihapus.');
    }
}
