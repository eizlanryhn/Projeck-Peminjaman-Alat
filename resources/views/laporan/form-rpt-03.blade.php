<form method="GET" action="{{ route('laporan.rpt03') }}" target="_blank">
    <div class="mb-2">
        <label class="form-label small">Kategori</label>
        <select name="kategori_id" class="form-select form-select-sm">
            <option value="">— Semua Kategori —</option>
            @foreach ($daftarKategori as $kategori)
                <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-sm btn-primary w-100 mt-3">Cetak PDF</button>
</form>
