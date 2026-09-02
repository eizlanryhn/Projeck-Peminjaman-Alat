<table class="table align-middle">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Alat</th>
            <th>Kategori</th>
            <th class="text-center">Tersedia</th>
            <th style="width: 160px">Jumlah Pinjam</th>
            <th style="width: 90px">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($isiKeranjang as $baris)
            <tr>
                <td>{{ $baris->alat->kode_alat }}</td>
                <td>{{ $baris->alat->nama }}</td>
                <td>{{ $baris->alat->kategori->nama }}</td>
                <td class="text-center">{{ $baris->alat->stok_tersedia }}</td>
                <td>
                    <form method="POST" action="{{ route('katalog.ubah-jumlah', $baris->alat) }}" class="d-flex gap-1">
                        @csrf
                        @method('PUT')
                        <input type="number" name="jumlah" class="form-control form-control-sm"
                            value="{{ $baris->jumlah }}" min="1" max="{{ $baris->alat->stok_tersedia }}">
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            Ubah
                        </button>
                    </form>
                </td>
                <td>
                    <form method="POST" action="{{ route('katalog.hapus', $baris->alat->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
