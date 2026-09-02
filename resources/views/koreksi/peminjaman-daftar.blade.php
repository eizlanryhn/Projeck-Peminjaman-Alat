@extends('layouts.utama')
@section('judul', 'Koreksi Data Peminjaman')
@section('konten')
    <h4 class="mb-3">Koreksi Data Peminjaman</h4>

    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                @include('koreksi.tabel-peminjaman')
            </div>

            {{ $daftarPeminjaman->links() }}

        </div>
    </div>
@endsection
