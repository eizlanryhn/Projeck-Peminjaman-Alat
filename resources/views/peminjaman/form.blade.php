@extends('layouts.utama')
@section('judul', 'Ajukan Peminjaman')
@section('konten')
    <h4 class="mb-3">Ajukan Peminjaman</h4>
    @if ($daftarTunggakan->isNotEmpty())
        <div class="alert alert-danger">
            <strong>Pengajuan tidak dapat dikirim.</strong>
            Anda masih memiliki peminjaman yang lewat tenggat:
            <ul class="mb-0 mt-2">
                @foreach ($daftarTunggakan as $tunggakan)
                    <li>
                        {{ $tunggakan->kode_pinjam }} &mdash; jatuh tempo
                        {{ $tunggakan->tgl_harus_kembali->format('d/m/Y') }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row">
        <div class="col-md-7 mb-3">
            @include('peminjaman.alat-diajukan')
        </div>
        <div class="col-md-5">
            @include('peminjaman.data-peminjam')
        </div>
    </div>
@endsection
