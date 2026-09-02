@extends('layouts.utama')
@section('judul', 'Daftar Pengguna')
@section('konten')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Daftar Pengguna</h4>
        <x-tombol-tambah href="{{ route('pengguna.create') }}" label="Tambah Pengguna" />
    </div>
    <div class="card">
        <div class="card-body">
            @include('pengguna.form-pencarian')
            @include('pengguna.table')
            {{ $daftarPengguna->links() }}
        </div>
    </div>
@endsection
