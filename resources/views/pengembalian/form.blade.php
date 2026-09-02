@extends('layouts.utama')
@section('judul', 'Verifikasi Pengembalian')
@section('konten')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Verifikasi {{ $peminjaman->kode_pinjam }}</h4>
        <a href="{{ route('pengembalian.antrian') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
    <form method="POST" action="{{ route('pengembalian.simpan', $peminjaman) }}">
        @csrf
        <div class="row">
            <div class="col-md-8 mb-3">
                @include('pengembalian.kondisi-alat')
                <div class="alert alert-info mt-3 mb-0 small">
                    Alat berkondisi <strong>baik</strong> dan <strong>rusak ringan</strong>
                    kembali menambah stok tersedia. Alat berkondisi
                    <strong>rusak berat</strong> dan <strong>hilang</strong> mengurangi stok total.
                </div>
            </div>
            <div class="col-md-4">
                @include('pengembalian.data-pengembalian')
            </div>
        </div>
    </form>
@endsection
