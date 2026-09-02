@extends('layouts.utama')
@section('judul', 'Koreksi Pengembalian')
@section('konten')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Koreksi {{ $pengembalian->peminjaman->kode_pinjam }}</h4>
        <a href="{{ route('koreksi.pengembalian.daftar') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="row">
        <div class="col-md-5 mb-3">
            <div class="card">
                <div class="card-header">Data yang Tidak Dapat Dikoreksi</div>
                <div class="card-body">
                    <dl class="row small mb-0">
                        <dt class="col-7">Peminjam</dt>
                        <dd class="col-5 text-end">{{ $pengembalian->peminjaman->peminjam->nama }}</dd>

                        <dt class="col-7">Tanggal Kembali</dt>
                        <dd class="col-5 text-end">{{ $pengembalian->tgl_kembali->format('d/m/Y') }}</dd>

                        <dt class="col-7">Hari Terlambat</dt>
                        <dd class="col-5 text-end">{{ $pengembalian->hari_terlambat }} hari</dd>

                        <dt class="col-7">Denda Keterlambatan</dt>
                        <dd class="col-5 text-end">
                            Rp {{ number_format($pengembalian->denda, 0, ',', '.') }}
                        </dd>

                        <dt class="col-7">Total Denda</dt>
                        <dd class="col-5 text-end">
                            Rp {{ number_format($pengembalian->total_denda, 0, ',', '.') }}
                        </dd>
                    </dl>

                    <hr>

                    <div class="small text-muted">
                        Denda keterlambatan dan hari terlambat dikunci trigger.
                        Total denda dihitung ulang sistem setiap koreksi disimpan.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header">Isian yang Dapat Dikoreksi</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('koreksi.pengembalian.perbarui', $pengembalian) }}">
                        @csrf
                        @method('PUT')

                        <x-input label="Denda Kerusakan" name="denda_kerusakan" type="number"
                            :value="(int) $pengembalian->denda_kerusakan" min="0" step="1000" required />

                        <x-textarea label="Catatan" name="catatan" :value="$pengembalian->catatan" />

                        <button type="submit" class="btn btn-primary">Simpan Koreksi</button>
                        <a href="{{ route('koreksi.pengembalian.daftar') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
