<table class="table table-striped align-middle">
    <thead>
        <tr>
            <th>Kode Pinjam</th>
            <th>Peminjam</th>
            <th>Harus Kembali</th>
            <th class="text-center">Jumlah Alat</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($daftarPeminjaman as $peminjaman)
            <tr class="{{ $peminjaman->lewatTenggat() ? 'table-warning' : '' }}">
                <td>{{ $peminjaman->kode_pinjam }}</td>
                <td>{{ $peminjaman->peminjam->nama }}</td>
                <td>
                    {{ $peminjaman->tgl_harus_kembali->format('d/m/Y') }}
                    @if ($peminjaman->lewatTenggat())
                        <span class="badge bg-danger">
                            Terlambat {{ $peminjaman->tgl_harus_kembali->diffInDays(now()) }} hari
                        </span>
                    @endif
                </td>
                <td class="text-center">{{ $peminjaman->detail->count() }}</td>
                <td>
                    <span class="badge bg-{{ $peminjaman->status->warna() }}">
                        {{ $peminjaman->status->label() }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted">
                    Tidak ada peminjaman yang sedang berjalan.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
