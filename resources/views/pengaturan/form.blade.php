@extends('layouts.utama')
@section('judul', 'Pengaturan Sistem')
@section('konten')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Pengaturan Sistem</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pengaturan.perbarui') }}">
                        @csrf
                        @method('PUT')

                        <x-input name="nama_sekolah" label="Nama Sekolah"
                            :value="$pengaturan['nama_sekolah'] ?? ''"
                            minlength="5" maxlength="150" required />
                        <div class="form-text mb-3">
                            Tercetak sebagai kop pada seluruh laporan PDF.
                        </div>

                        <x-input name="tarif_denda_harian" label="Tarif Denda Harian (Rupiah)" type="number"
                            :value="$pengaturan['tarif_denda_harian'] ?? 0" min="0" step="500" required />
                        <div class="form-text mb-3">
                            Berlaku per hari keterlambatan, per unit alat.
                            Nilai ini dibaca langsung oleh trigger perhitungan denda.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <x-input name="default_hari_pinjam" label="Durasi Pinjam Bawaan (hari)" type="number"
                                    :value="$pengaturan['default_hari_pinjam'] ?? 7" min="1" required />
                            </div>
                            <div class="col-md-6">
                                <x-input name="maks_hari_pinjam" label="Durasi Pinjam Maksimal (hari)" type="number"
                                    :value="$pengaturan['maks_hari_pinjam'] ?? 30" min="1" max="365" required />
                            </div>
                        </div>

                        <div class="alert alert-warning small">
                            Perubahan tarif denda hanya berlaku untuk pengembalian yang
                            diverifikasi <strong>setelah</strong> perubahan disimpan.
                            Denda transaksi yang sudah selesai tidak dihitung ulang.
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
