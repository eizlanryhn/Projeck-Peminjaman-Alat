<form method="GET" action="{{ route('laporan.rpt02') }}" target="_blank">
    <div class="mb-2">
        <label class="form-label small">Tanggal Awal</label>
        <input type="date" name="tgl_awal" class="form-control form-control-sm"
            value="{{ now()->startOfMonth()->toDateString() }}" required>
    </div>
    <div class="mb-2">
        <label class="form-label small">Tanggal Akhir</label>
        <input type="date" name="tgl_akhir" class="form-control form-control-sm"
            value="{{ now()->toDateString() }}" required>
    </div>
    <button type="submit" class="btn btn-sm btn-primary w-100 mt-3">Cetak PDF</button>
</form>
