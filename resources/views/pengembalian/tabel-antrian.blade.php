<table class="table table-striped align-middle">
    <thead>
        <tr>
            <th>Kode Pinjam</th>
            <th>Peminjam</th>
            <th>Diajukan Kembali</th>
            <th>Harus Kembali</th>
            <th class="text-center">Jumlah Alat</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($daftarAntrian as $peminjaman)
            <tr class="{{ $peminjaman->lewatTenggat() ? 'table-warning' : '' }}">
                <td>{{ $peminjaman->kode_pinjam }}</td>
                <td>{{ $peminjaman->peminjam->nama }}</td>
                <td>
                    {{ $peminjaman->tgl_diajukan_kembali?->format('d/m/Y') ?? '-' }}
                </td>
                <td>
                    {{ $peminjaman->tgl_harus_kembali->format('d/m/Y') }}
                    @if ($peminjaman->lewatTenggat())
                        <span class="badge bg-danger">
                            Terlambat {{ $peminjaman->tgl_harus_kembali->diffInDays(now()) }} hari
                        </span>
                    @endif
                </td>
                <td class="text-center">{{ $peminjaman->detail->count() }}</td>
                <td class="text-center">
                    <a href="{{ route('pengembalian.verifikasi', $peminjaman) }}"
                        class="btn btn-sm btn-primary">Verifikasi</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted">
                    Tidak ada pengajuan pengembalian yang menunggu verifikasi.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
