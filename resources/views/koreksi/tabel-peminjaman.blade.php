<table class="table table-striped align-middle">
    <thead>
        <tr>
            <th>Kode Pinjam</th>
            <th>Peminjam</th>
            <th>Tanggal Pinjam</th>
            <th>Harus Kembali</th>
            <th>Status</th>
            <th style="width: 160px">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($daftarPeminjaman as $peminjaman)
            <tr>
                <td>{{ $peminjaman->kode_pinjam }}</td>
                <td>{{ $peminjaman->peminjam->nama }}</td>
                <td>{{ $peminjaman->tgl_pinjam->format('d/m/Y') }}</td>
                <td>{{ $peminjaman->tgl_harus_kembali->format('d/m/Y') }}</td>
                <td>
                    <span class="badge bg-{{ $peminjaman->status->warna() }}">
                        {{ $peminjaman->status->label() }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('koreksi.peminjaman.ubah', $peminjaman) }}"
                        class="btn btn-sm btn-warning">Ubah</a>
                    @can('delete', $peminjaman)
                        <form method="POST" action="{{ route('koreksi.peminjaman.hapus', $peminjaman) }}" class="d-inline"
                            onsubmit="return confirm('Hapus data peminjaman ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    @else
                        <button class="btn btn-sm btn-secondary" disabled
                            title="Hanya berstatus diajukan atau ditolak yang dapat dihapus">
                            Hapus
                        </button>
                    @endcan
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted">
                    Belum ada data peminjaman.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
