<table class="table table-striped align-middle">
    <thead>
        <tr>
            <th>Kode Pinjam</th>
            <th>Peminjam</th>
            <th>Tanggal Pinjam</th>
            <th>Harus Kembali</th>
            <th class="text-center">Jumlah Alat</th>
            <th style="width: 100px">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($daftarPengajuan as $peminjaman)
            <tr>
                <td>{{ $peminjaman->kode_pinjam }}</td>
                <td>{{ $peminjaman->peminjam->nama }}</td>
                <td>{{ $peminjaman->tgl_pinjam->format('d/m/Y') }}</td>
                <td>{{ $peminjaman->tgl_harus_kembali->format('d/m/Y') }}</td>
                <td class="text-center">{{ $peminjaman->detail->count() }}</td>
                <td>
                    <a href="{{ route('persetujuan.rincian', $peminjaman) }}" class="btn btn-sm btn-primary">
                        Proses
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted">
                    Tidak ada pengajuan yang menunggu diproses.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
