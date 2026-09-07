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
                                <x-input label="Nama Lengkap" name="nama" :value="$pengguna->nama" required minlength="3" maxlength="100" />
                            </div>
                            <div class="col-md-6">
                                <x-input label="Nama Pengguna" name="username" :value="$pengguna->username" required minlength="3" maxlength="50" pattern="[A-Za-z0-9_\-]+" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-input label="Email" name="email" :value="$pengguna->email" type="email" required maxlength="180" />
                            </div>
                            <div class="col-md-6">
                                <x-input label="Nomor Telepon" name="no_telp" type="tel"
                                    :value="$pengguna->no_telp"
                                    required
                                    minlength="8" maxlength="15"
                                    pattern="[0-9]{8,15}"
                                    inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    placeholder="Contoh: 081234567890" />
                                <div class="form-text">Isi dengan angka saja, 8–15 digit.</div>
                            </div>
                        </div>

                        @if (!$pengguna->exists)
                            <div class="row">
                                <div class="col-md-6">
                                    <x-input-password label="Kata Sandi" name="password" required minlength="8" maxlength="64" />
                                </div>
                                <div class="col-md-6">
                                    <x-input-password label="Konfirmasi Kata Sandi" name="password_confirmation" required minlength="8" maxlength="64" />
                                </div>
                            </div>
                        @else
                            <span class="text-muted small">(Password kosongkan bila tidak diganti)</span>
                            <div class="row">
                                <div class="col-md-6">
                                    <x-input-password label="Kata Sandi" name="password" minlength="8" maxlength="64" />
                                </div>
                                <div class="col-md-6">
                                    <x-input-password label="Konfirmasi Kata Sandi" name="password_confirmation" minlength="8" maxlength="64" />
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="peran" class="form-label">Peran</label>
                                <select class="form-select @error('peran') is-invalid @enderror" id="peran"
                                    name="peran" required>
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
                                <x-select label="Aktif" name="is_aktif" :value="$pengguna->is_aktif" placeholder="-- Pilih Status Aktif --"
                                    :opsi="[['key' => 1, 'label' => 'Aktif'], ['key' => 0, 'label' => 'Nonaktif']]" required />
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
