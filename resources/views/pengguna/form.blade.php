@extends('layouts.utama')
@section('judul', $pengguna->exists ? 'Ubah Pengguna' : 'Tambah Pengguna')
@section('konten')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        {{ $pengguna->exists ? 'Ubah Data Pengguna' : 'Tambah Data Pengguna' }}
                    </h5>
                    <form method="POST"
                        action="{{ $pengguna->exists ? route('pengguna.update', $pengguna) : route('pengguna.store') }}">
                        @csrf
                        @if ($pengguna->exists)
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <x-input label="Nama Lengkap" name="nama" :value="$pengguna->nama" />
                            </div>
                            <div class="col-md-6">
                                <x-input label="Nama Pengguna" name="username" :value="$pengguna->username" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-input label="Email" name="email" :value="$pengguna->email" type="email" />
                            </div>
                            <div class="col-md-6">
                                <x-input label="Nomor Telepon" name="no_telp" :value="$pengguna->no_telp" />
                            </div>
                        </div>
                        @if ($pengguna->exists)
                            <span class="text-muted small">(Password kosongkan bila tidak diganti)</span>
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <x-input label="Kata Sandi" name="password" type="password" />
                            </div>
                            <div class="col-md-6">
                                <x-input label="Konfirmasi Kata Sandi" name="password_confirmation" type="password" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="peran" class="form-label">Peran</label>
                                <select class="form-select @error('peran') is-invalid @enderror" id="peran"
                                    name="peran">
                                    <option value="">-- Pilih Peran --</option>
                                    @foreach ($daftarPeran as $pilihanPeran)
                                        <option value="{{ $pilihanPeran->name }}"
                                            {{ old('peran', $pengguna->roles->first()?->name) == $pilihanPeran->name ? 'selected' : '' }}>
                                            {{ ucfirst($pilihanPeran->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('peran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <x-select label="Aktif" name="is_aktif" :value="$pengguna->is_aktif" placeholder="Pilih status aktif"
                                    :opsi="[['key' => 1, 'label' => 'Aktif'], ['key' => 0, 'label' => 'Nonaktif']]" />
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('pengguna.index') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
