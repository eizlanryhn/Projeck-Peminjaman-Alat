@extends('layouts.utama')
@section('judul', $alat->exists ? 'Ubah Alat' : 'Tambah Alat')
@section('konten')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        {{ $alat->exists ? 'Ubah Data Alat' : 'Tambah Data Alat' }}
                    </h5>
                    <form method="POST"
                        action="{{ $alat->exists ? route('alat.update', $alat) : route('alat.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        @if ($alat->exists)
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-md-4">
                                <x-input name="kode_alat" label="Kode Alat" :value="$alat->kode_alat"
                                    required
                                    minlength="3" maxlength="30"
                                    pattern="[A-Za-z0-9\-]+"
                                    placeholder="Contoh: AUK-001"
                                    title="Hanya huruf, angka, dan tanda hubung" />
                            </div>
                            <div class="col-md-8">
                                <x-input name="nama" label="Nama Alat" :value="$alat->nama"
                                    required
                                    minlength="3" maxlength="150" />
                            </div>
                        </div>
                        <x-select name="kategori_id" label="Kategori" :opsi="$daftarKategori" :value="$alat->kategori_id" keyValue="id"
                            keyLabel="nama" placeholder="Pilih kategori" required />
                        <div class="row">
                            <div class="col-md-4">
                                <x-input name="stok" label="Stok Total" :value="$alat->stok" type="number" min="0" max="9999" required />
                            </div>
                            <div class="col-md-4">
                                <x-input name="stok_tersedia" label="Stok Tersedia" :value="$alat->stok_tersedia" type="number" min="0" max="9999" required />
                            </div>
                            <div class="col-md-4">
                                <x-select name="kondisi" label="Kondisi" :opsi="[
                                    ['key' => 'baik', 'label' => 'Baik'],
                                    ['key' => 'rusak_ringan', 'label' => 'Rusak Ringan'],
                                    ['key' => 'rusak_berat', 'label' => 'Rusak Berat'],
                                ]" :value="$alat->kondisi"
                                    placeholder="Pilih kondisi" required />
                            </div>
                        </div>
                        <x-textarea name="deskripsi" label="Deskripsi" rows="3" :value="$alat->deskripsi" />
                        @include('alat.input-foto')
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('alat.index') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
