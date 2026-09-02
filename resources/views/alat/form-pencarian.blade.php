<form method="GET" action="{{ route('alat.index') }}" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="cari" class="form-control" placeholder="Cari nama atau kode alat"
            value="{{ $kataKunci }}">
    </div>
    <div class="col-md-3">
        <select name="kategori_id" class="form-select">
            <option value="">Semua Kategori</option>
            @foreach ($daftarKategori as $kategori)
                <option value="{{ $kategori->id }}" {{ $kategoriId == $kategori->id ? 'selected' : '' }}>
                    {{ $kategori->nama }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-outline-secondary">Saring</button>
        <a href="{{ route('alat.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>
