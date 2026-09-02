@extends('laporan.layout')
@section('isi')
    <h3 class="judul">Laporan Pengembalian &amp; Denda (RPT-02)</h3>
    <p class="sub">Periode: {{ $periode }}</p>

    @if ($daftarPengembalian->isEmpty())
        <p style="text-align:center; color:#888;">Tidak ada data pada periode ini.</p>
    @else
        @php $totalDenda = $daftarPengembalian->sum('total_denda'); @endphp
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Peminjam</th>
                    <th>Tgl Kembali</th>
                    <th>Hari Terlambat</th>
                    <th class="num">Denda Keterlambatan</th>
                    <th class="num">Denda Kerusakan</th>
                    <th class="num">Total Denda</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($daftarPengembalian as $i => $pg)
                    <tr>
                        <td class="ctr">{{ $i + 1 }}</td>
                        <td>{{ $pg->peminjaman->kode_pinjam }}</td>
                        <td>{{ $pg->peminjaman->peminjam->nama }}</td>
                        <td class="ctr">{{ $pg->tgl_kembali->format('d/m/Y') }}</td>
                        <td class="ctr">{{ $pg->hari_terlambat }}</td>
                        <td class="num">Rp {{ number_format($pg->denda, 0, ',', '.') }}</td>
                        <td class="num">Rp {{ number_format($pg->denda_kerusakan, 0, ',', '.') }}</td>
                        <td class="num">Rp {{ number_format($pg->total_denda, 0, ',', '.') }}</td>
                        <td>{{ $pg->petugas?->nama ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" style="text-align:right">Total Denda Terkumpul</td>
                    <td class="num">Rp {{ number_format($totalDenda, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    @endif
@endsection
