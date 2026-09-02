@extends('layouts.utama')
@section('judul', 'Keranjang Peminjaman')
@section('konten')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Keranjang Peminjaman</h4>
        <a href="{{ route('katalog.daftar') }}" class="btn btn-outline-secondary">
            Kembali ke Katalog
        </a>
    </div>
    @if ($isiKeranjang->isEmpty())
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                Keranjang masih kosong. Pilih alat dari Katalog terlebih dahulu.
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    @include('katalog.tabel-keranjang')
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <form method="POST" action="{{ route('katalog.kosongkan') }}"
                        onsubmit="return confirm('Kosongkan seluruh keranjang?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">Kosongkan</button>
                    </form>
                    <a href="{{ route('peminjaman.ajukan') }}" class="btn btn-success">
                        Lanjut ke Pengajuan
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection
