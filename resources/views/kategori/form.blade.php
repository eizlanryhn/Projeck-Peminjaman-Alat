@extends('layouts.utama')
@section('judul', $kategori->exists ? 'Ubah Kategori' : 'Tambah Kategori')
@section('konten')
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        {{ $kategori->exists ? 'Ubah Kategori' : 'Tambah Kategori' }}
                    </h5>
                    <form method="POST"
                        action="{{ $kategori->exists ? route('kategori.update', $kategori) : route('kategori.store') }}">
                        @csrf
                        @if ($kategori->exists)
                            @method('PUT')
                        @endif
                        <x-input name="nama" label="Nama Kategori" :value="$kategori->nama"
                            required
                            minlength="2" maxlength="160" />
                        <x-textarea name="deskripsi" label="Deskripsi Kategori" :value="$kategori->deskripsi" />
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
