@extends('layouts.utama')
@section('judul', 'Rincian Pengembalian')
@section('konten')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Rincian {{ $pengembalian->peminjaman->kode_pinjam }}</h4>
        <a href="{{ route('pengembalian.antrian') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
    <div class="row">
        <div class="col-md-8 mb-3">
            @include('pengembalian.tabel-denda')
        </div>
        <div class="col-md-4">
            @include('pengembalian.ringkasan-denda')
        </div>
    </div>
@endsection
