@extends('layouts.utama')
@section('judul', 'Laporan')
@section('konten')
    <h4 class="mb-4">Laporan</h4>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">RPT-01 &mdash; Peminjaman</h5>
                    <p class="card-text small text-muted">
                        Rekap seluruh transaksi peminjaman pada periode tertentu.
                    </p>
                    @include('laporan.form-rpt-01')
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">RPT-02 &mdash; Pengembalian</h5>
                    <p class="card-text small text-muted">
                        Rekap seluruh pengembalian beserta denda pada periode tertentu.
                    </p>
                    @include('laporan.form-rpt-02')
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">RPT-03 &mdash; Rekapitulasi Stok Alat</h5>
                    <p class="card-text small text-muted">
                        Rekap stok alat tersedia dan sedang dipinjam.
                    </p>
                    @include('laporan.form-rpt-03')
                </div>
            </div>
        </div>
    </div>
@endsection
