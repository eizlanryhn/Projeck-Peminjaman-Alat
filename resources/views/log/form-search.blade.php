<form method="GET" action="{{ route('log.index') }}" class="row g-2">
    <div class="col-md-3">
        <select name="aksi" class="form-select form-select-sm">
            <option value="">— Semua Aksi —</option>
            @foreach ($daftarAksi as $aksi)
                <option value="{{ $aksi }}" {{ request('aksi') == $aksi ? 'selected' : '' }}>
                    {{ $aksi }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select name="tabel_tujuan" class="form-select form-select-sm">
            <option value="">— Semua Tabel —</option>
            @foreach ($daftarTabel as $tabel)
                <option value="{{ $tabel }}" {{ request('tabel_tujuan') == $tabel ? 'selected' : '' }}>
                    {{ $tabel }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <input type="text" name="cari" class="form-control form-control-sm"
            placeholder="Cari deskripsi..." value="{{ request('cari') }}">
    </div>
    <div class="col-md-2 d-flex gap-1">
        <button type="submit" class="btn btn-sm btn-primary w-100">Cari</button>
        <a href="{{ route('log.index') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
    </div>
</form>
