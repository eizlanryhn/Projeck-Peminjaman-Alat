<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Services\Keranjang;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function __construct(private Keranjang $keranjang)
    {
    }

    public function katalog(Request $request)
    {
        $kataKunci  = $request->query('cari');
        $kategoriId = $request->query('kategori_id');

        $daftarAlat = Alat::with('kategori')
            ->when($kataKunci, function ($query, $kataKunci) {
                $query->where(function ($cabang) use ($kataKunci) {
                    $cabang->where('nama', 'like', '%' . $kataKunci . '%')
                        ->orWhere('kode_alat', 'like', '%' . $kataKunci . '%');
                });
            })
            ->when($kategoriId, function ($query, $kategoriId) {
                $query->where('kategori_id', $kategoriId);
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        $daftarKategori = Kategori::orderBy('nama')->get();

        return view('katalog.daftar', compact(
            'daftarAlat',
            'daftarKategori',
            'kataKunci',
            'kategoriId'
        ));
    }

    public function tambahKeKeranjang(Request $request, Alat $alat)
    {
        $data = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1', 'max:' . $alat->stok_tersedia],
        ], [
            'jumlah.max' => 'Jumlah melebihi stok yang tersedia (' . $alat->stok_tersedia . ' unit).',
            'jumlah.min' => 'Jumlah minimal 1 unit.',
        ]);

        if ($alat->stok_tersedia < 1) {
            return back()->with('gagal', 'Alat ini sedang tidak tersedia.');
        }

        $this->keranjang->tambah($alat, (int) $data['jumlah']);

        return back()->with('sukses', $alat->nama . ' ditambahkan ke keranjang.');
    }

    public function lihatKeranjang()
    {
        $isiKeranjang = $this->keranjang->isi();

        return view('katalog.keranjang', compact('isiKeranjang'));
    }

    public function ubahJumlah(Request $request, Alat $alat)
    {
        $data = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1', 'max:' . $alat->stok_tersedia],
        ]);

        $this->keranjang->ubahJumlah($alat, (int) $data['jumlah']);

        return back()->with('sukses', 'Jumlah diperbarui.');
    }

    public function hapusDariKeranjang(int $alatId)
    {
        $this->keranjang->hapus($alatId);

        return back()->with('sukses', 'Alat dikeluarkan dari keranjang.');
    }

    public function kosongkanKeranjang()
    {
        $this->keranjang->kosongkan();

        return back()->with('sukses', 'Keranjang dikosongkan.');
    }
}
