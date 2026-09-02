@extends('layouts.utama')
@section('judul', 'Pinjaman Saya')
@section('konten')
    <h4 class="mb-3">Pinjaman Saya</h4>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                @include('peminjaman.tabel-pinjam')
            </div>
            {{ $daftarPeminjaman->links() }}
        </div>
    </div>
@endsection
