@props(['action', 'kataKunci' => '', 'nama' => 'cari', 'placeholder' => 'Cari...'])
<form method="GET" action="{{ $action }}" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="{{ $nama }}" class="form-control" placeholder="{{ $placeholder }}"
            value="{{ $kataKunci }}">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-outline-secondary">Cari</button>
        <a href="{{ $action }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>
