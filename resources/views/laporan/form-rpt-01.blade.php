<form method="GET" action="{{ route('laporan.rpt01') }}" target="_blank">
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
    <div class="mb-3">
        <label class="form-label small">Status</label>
        <select name="status" class="form-select form-select-sm">
            <option value="">— Semua Status —</option>
            <option value="diajukan">Diajukan</option>
            <option value="dipinjam">Dipinjam</option>
            <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
            <option value="selesai">Selesai</option>
            <option value="ditolak">Ditolak</option>
        </select>
    </div>
    <button type="submit" class="btn btn-sm btn-primary w-100">Cetak PDF</button>
</form>
