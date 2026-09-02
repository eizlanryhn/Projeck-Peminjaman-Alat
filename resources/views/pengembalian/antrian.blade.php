@extends('layouts.utama')
@section('judul', 'Antrian Verifikasi Pengembalian')
@section('konten')
    <h4 class="mb-3">Antrian Verifikasi Pengembalian</h4>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                @include('pengembalian.tabel-antrian')
            </div>
            {{ $daftarAntrian->links() }}
        </div>
    </div>
@endsection
