@extends('layouts.utama')
@section('judul', 'Pemantauan Peminjaman')
@section('konten')
    <h4 class="mb-3">Pemantauan Peminjaman Berjalan</h4>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                @include('pengembalian.tabel-pantau')
            </div>
            {{ $daftarPeminjaman->links() }}
        </div>
    </div>
@endsection
