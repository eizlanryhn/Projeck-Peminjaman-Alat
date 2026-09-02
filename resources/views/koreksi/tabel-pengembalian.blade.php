<table class="table table-striped align-middle">
    <thead>
        <tr>
            <th>Kode Pinjam</th>
            <th>Peminjam</th>
            <th>Tanggal Kembali</th>
            <th class="text-end">Denda Keterlambatan</th>
            <th class="text-end">Denda Kerusakan</th>
            <th class="text-end">Total Denda</th>
            <th style="width: 90px">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($daftarPengembalian as $pengembalian)
            <tr>
                <td>{{ $pengembalian->peminjaman->kode_pinjam }}</td>
                <td>{{ $pengembalian->peminjaman->peminjam->nama }}</td>
                <td>{{ $pengembalian->tgl_kembali->format('d/m/Y') }}</td>
                <td class="text-end">
                    Rp {{ number_format($pengembalian->denda, 0, ',', '.') }}
                </td>
                <td class="text-end">
                    Rp {{ number_format($pengembalian->denda_kerusakan, 0, ',', '.') }}
                </td>
                <td class="text-end">
                    Rp {{ number_format($pengembalian->total_denda, 0, ',', '.') }}
                </td>
                <td>
                    <a href="{{ route('koreksi.pengembalian.ubah', $pengembalian) }}"
                        class="btn btn-sm btn-warning">Ubah</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted">
                    Belum ada data pengembalian.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
