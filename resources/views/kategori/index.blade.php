@extends('layouts.utama')
@section('judul', 'Daftar Kategori')
@section('konten')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Daftar Kategori</h4>
        <x-tombol-tambah :href="route('kategori.create')" label="Tambah Kategori" />
    </div>
    <div class="card">
        <div class="card-body">
            <x-form-pencarian :action="route('kategori.index')" :kataKunci="$kataKunci" />
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th style="width: 60px">No</th>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th style="width: 110px">Jumlah Alat</th>
                            <th style="width: 160px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarKategori as $nomor => $kategori)
                            <tr>
                                <td>{{ $daftarKategori->firstItem() + $nomor }}</td>
                                <td>{{ $kategori->nama }}</td>
                                <td>{{ $kategori->deskripsi ?: '-' }}</td>
                                <td>{{ $kategori->daftar_alat_count }}</td>
                                <td>
                                    <x-tombol-aksi
                                        :ubah="route('kategori.edit', $kategori)"
                                        :hapus="route('kategori.destroy', $kategori)"
                                        pesanHapus="Yakin ingin menghapus kategori {{ $kategori->nama }}?"
                                    />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Belum ada data kategori.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $daftarKategori->links() }}
        </div>
    </div>
@endsection
