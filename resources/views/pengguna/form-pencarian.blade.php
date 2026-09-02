<form method="GET" action="{{ route('pengguna.index') }}" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="cari" class="form-control" placeholder="Cari nama atau nama pengguna"
            value="{{ $kataKunci }}">
    </div>
    <div class="col-md-3">
        <select name="peran" class="form-select">
            <option value="">Semua Peran</option>
            @foreach ($daftarPeran as $pilihanPeran)
                <option value="{{ $pilihanPeran->name }}" {{ $peran == $pilihanPeran->name ? 'selected' : '' }}>
                    {{ ucfirst($pilihanPeran->name) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-outline-secondary">Filter</button>
        <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>
