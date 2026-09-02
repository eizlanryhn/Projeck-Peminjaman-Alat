<div class="card">
    <div class="card-header">Ringkasan</div>
    <div class="card-body">
        <dl class="row mb-0">
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
            <dt class="col-7">Denda Kerusakan</dt>
            <dd class="col-5 text-end">
                Rp {{ number_format($pengembalian->denda_kerusakan, 0, ',', '.') }}
            </dd>
        </dl>
        <hr>
        <div class="d-flex justify-content-between">
            <strong>Total Denda</strong>
            <strong class="text-danger fs-5">
                Rp {{ number_format($pengembalian->total_denda, 0, ',', '.') }}
            </strong>
        </div>
        @if ($pengembalian->catatan)
            <hr>
            <div class="small text-muted">{{ $pengembalian->catatan }}</div>
        @endif
    </div>
</div>
