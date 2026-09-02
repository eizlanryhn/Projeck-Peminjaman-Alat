@extends('layouts.utama')
@section('judul', 'Koreksi Data Pengembalian')
@section('konten')
    <h4 class="mb-3">Koreksi Data Pengembalian</h4>

    <div class="card">
        <div class="card-body">

            <div class="alert alert-info small">
                Data pengembalian tidak dapat dihapus. Yang dapat dikoreksi hanya
                denda kerusakan dan catatan.
            </div>

            <div class="table-responsive">
                @include('koreksi.tabel-pengembalian')
            </div>

            {{ $daftarPengembalian->links() }}

        </div>
    </div>
@endsection
