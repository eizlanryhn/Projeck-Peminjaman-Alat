@extends('layouts.utama')
@section('judul', 'Rincian Peminjaman')
@section('konten')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ $peminjaman->kode_pinjam }}</h4>
        <a href="{{ route('peminjaman.saya') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    <span class="badge bg-{{ $peminjaman->status->warna() }}">
                        {{ $peminjaman->status->label() }}
                    </span>
                </dd>
                <dt class="col-sm-3">Tanggal Pinjam</dt>
                <dd class="col-sm-9">{{ $peminjaman->tgl_pinjam->format('d/m/Y') }}</dd>
                <dt class="col-sm-3">Harus Kembali</dt>
                <dd class="col-sm-9">{{ $peminjaman->tgl_harus_kembali->format('d/m/Y') }}</dd>
                <dt class="col-sm-3">Keperluan</dt>
                <dd class="col-sm-9">{{ $peminjaman->keperluan ?: '-' }}</dd>
                <dt class="col-sm-3">Petugas</dt>
                <dd class="col-sm-9">{{ $peminjaman->petugas->nama ?? 'Belum diproses' }}</dd>
                @if ($peminjaman->alasan_tolak)
                    <dt class="col-sm-3">Alasan Ditolak</dt>
                    <dd class="col-sm-9 text-danger">{{ $peminjaman->alasan_tolak }}</dd>
                @endif
            </dl>
        </div>
    </div>
    <div class="card">
        <div class="card-header">Daftar Alat</div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Alat</th>
                        <th class="text-center">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($peminjaman->detail as $baris)
                        <tr>
                            <td>{{ $baris->alat->kode_alat }}</td>
                            <td>{{ $baris->alat->nama }}</td>
                            <td class="text-center">{{ $baris->jumlah }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
