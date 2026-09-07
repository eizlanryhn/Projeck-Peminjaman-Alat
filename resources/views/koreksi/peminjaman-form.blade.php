@extends('layouts.utama')
@section('judul', 'Koreksi Peminjaman')
@section('konten')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Koreksi {{ $peminjaman->kode_pinjam }}</h4>
        <a href="{{ route('koreksi.peminjaman.daftar') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="row">
        <div class="col-md-5 mb-3">
            <div class="card">
                <div class="card-header">Data yang Tidak Dapat Dikoreksi</div>
                <div class="card-body">
                    <dl class="row small mb-0">
                        <dt class="col-5">Kode Pinjam</dt>
                        <dd class="col-7">{{ $peminjaman->kode_pinjam }}</dd>

                        <dt class="col-5">Peminjam</dt>
                        <dd class="col-7">{{ $peminjaman->peminjam->nama }}</dd>

                        <dt class="col-5">Status</dt>
                        <dd class="col-7">
                            <span class="badge bg-{{ $peminjaman->status->warna() }}">
                                {{ $peminjaman->status->label() }}
                            </span>
                        </dd>

                        <dt class="col-5">Daftar Alat</dt>
                        <dd class="col-7">
                            <ul class="list-unstyled mb-0">
                                @foreach ($peminjaman->detail as $baris)
                                    <li>{{ $baris->alat->nama }} ({{ $baris->jumlah }})</li>
                                @endforeach
                            </ul>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header">Isian yang Dapat Dikoreksi</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('koreksi.peminjaman.perbarui', $peminjaman) }}">
                        @csrf
                        @method('PUT')

                        <x-input label="Tanggal Pinjam" name="tgl_pinjam" type="date"
                            :value="$peminjaman->tgl_pinjam->toDateString()" required />

                        <x-input label="Tanggal Harus Kembali" name="tgl_harus_kembali" type="date"
                            :value="$peminjaman->tgl_harus_kembali->toDateString()" required />

                        <x-textarea label="Keperluan" name="keperluan" :value="$peminjaman->keperluan"
                            minlength="5" maxlength="500" />

                        <x-textarea label="Alasan Tolak" name="alasan_tolak" :value="$peminjaman->alasan_tolak"
                            minlength="5" maxlength="500" />

                        <div class="form-text mb-3">
                            BR-12: hanya keempat isian di atas yang boleh dikoreksi.
                            Perubahan tercatat di log aktivitas.
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Koreksi</button>
                        <a href="{{ route('koreksi.peminjaman.daftar') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
