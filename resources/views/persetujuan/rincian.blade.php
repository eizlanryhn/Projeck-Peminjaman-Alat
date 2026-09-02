@extends('layouts.utama')
@section('judul', 'Proses Pengajuan')
@section('konten')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ $peminjaman->kode_pinjam }}</h4>
        <a href="{{ route('persetujuan.antrian') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
    <div class="row">
        <div class="col-md-7 mb-3">
            @include('persetujuan.alat-diajukan')
        </div>
        <div class="col-md-5">
            @include('persetujuan.form-setujui')
        </div>
    </div>
@endsection
