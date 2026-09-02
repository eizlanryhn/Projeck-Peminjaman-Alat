@extends('layouts.utama')
@section('judul', 'Daftar Alat')
@section('konten')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Daftar Alat</h4>
        <x-tombol-tambah :href="route('alat.create')" label="Tambah Alat" />
    </div>
    <div class="card">
        <div class="card-body">
            @include('alat.form-pencarian')
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th style="width: 70px">Foto</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th class="text-center">Stok</th>
                            <th class="text-center">Tersedia</th>
                            <th>Kondisi</th>
                            <th style="width: 160px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarAlat as $alat)
                            <tr>
                                <td>
                                    @if ($alat->foto)
                                        <img src="{{ asset('gambar/alat/' . $alat->foto) }}" alt="{{ $alat->nama }}"
                                            class="rounded" width="48" height="48" style="object-fit: cover;">
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>{{ $alat->kode_alat }}</td>
                                <td>{{ $alat->nama }}</td>
                                <td>{{ $alat->kategori->nama }}</td>
                                <td class="text-center">{{ $alat->stok }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $alat->stok_tersedia > 0 ? 'success' : 'secondary' }}">
                                        {{ $alat->stok_tersedia }}
                                    </span>
                                </td>
                                <td>{{ str_replace('_', ' ', $alat->kondisi) }}</td>
                                <td>
                                    <x-tombol-aksi
                                        :ubah="route('alat.edit', $alat)"
                                        :hapus="route('alat.destroy', $alat)"
                                        pesanHapus="Hapus data alat {{ $alat->nama }}?" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    Data alat tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $daftarAlat->links() }}
        </div>
    </div>
@endsection
