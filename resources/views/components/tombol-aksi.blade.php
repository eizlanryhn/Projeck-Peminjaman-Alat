@props([
    'lihat' => null,
    'ubah' => null,
    'hapus' => null,
    'pesanHapus' => 'Yakin ingin menghapus data ini?',
])
<div class="d-flex gap-1">
    @if ($lihat)
        <a href="{{ $lihat }}" class="btn btn-sm btn-info">Lihat</a>
    @endif
    @if ($ubah)
        <a href="{{ $ubah }}" class="btn btn-sm btn-warning">Ubah</a>
    @endif
    @if ($hapus)
        <form action="{{ $hapus }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $pesanHapus }}')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
        </form>
    @endif
</div>
