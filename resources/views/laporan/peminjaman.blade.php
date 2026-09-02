@extends('laporan.layout')
@section('isi')
    <h3 class="judul">Laporan Peminjaman Alat (RPT-01)</h3>
    <p class="sub">Periode: {{ $periode }}</p>

    @if ($daftarPeminjaman->isEmpty())
        <p style="text-align:center; color:#888;">Tidak ada data pada periode ini.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Peminjam</th>
                    <th>Tgl Pinjam</th>
                    <th>Harus Kembali</th>
                    <th>Alat &amp; Jumlah</th>
                    <th>Status</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($daftarPeminjaman as $i => $p)
                    <tr>
                        <td class="ctr">{{ $i + 1 }}</td>
                        <td>{{ $p->kode_pinjam }}</td>
                        <td>{{ $p->peminjam->nama }}</td>
                        <td class="ctr">{{ $p->tgl_pinjam->format('d/m/Y') }}</td>
                        <td class="ctr">{{ $p->tgl_harus_kembali->format('d/m/Y') }}</td>
                        <td>
                            @foreach ($p->detail as $d)
                                {{ $d->alat->nama }} ({{ $d->jumlah }})<br>
                            @endforeach
                        </td>
                        <td class="ctr">{{ $p->status->label() }}</td>
                        <td>{{ $p->petugas?->nama ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7">Total Peminjaman</td>
                    <td class="ctr">{{ $daftarPeminjaman->count() }}</td>
                </tr>
            </tfoot>
        </table>
    @endif
@endsection
