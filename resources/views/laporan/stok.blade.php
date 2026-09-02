@extends('laporan.layout')
@section('isi')
    <h3 class="judul">Laporan Rekapitulasi Stok Alat (RPT-03)</h3>
    <p class="sub">
        @if ($kategoriDipilih)
            Kategori: {{ $kategoriDipilih->nama }}
        @else
            Semua Kategori
        @endif
    </p>

    @if ($daftarAlat->isEmpty())
        <p style="text-align:center; color:#888;">Tidak ada data alat.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Alat</th>
                    <th>Kategori</th>
                    <th class="ctr">Stok Total</th>
                    <th class="ctr">Tersedia</th>
                    <th class="ctr">Dipinjam</th>
                    <th>Satuan</th>
                    <th>Kondisi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($daftarAlat as $i => $alat)
                    <tr>
                        <td class="ctr">{{ $i + 1 }}</td>
                        <td>{{ $alat->kode_alat }}</td>
                        <td>{{ $alat->nama }}</td>
                        <td>{{ $alat->kategori->nama }}</td>
                        <td class="ctr">{{ $alat->stok }}</td>
                        <td class="ctr">{{ $alat->stok_tersedia }}</td>
                        <td class="ctr">{{ $alat->stok - $alat->stok_tersedia }}</td>
                        <td>{{ $alat->satuan ?? '-' }}</td>
                        <td>{{ $alat->kondisi ?? 'Baik' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right">Total</td>
                    <td class="ctr">{{ $daftarAlat->sum('stok') }}</td>
                    <td class="ctr">{{ $daftarAlat->sum('stok_tersedia') }}</td>
                    <td class="ctr">{{ $daftarAlat->sum(fn($a) => $a->stok - $a->stok_tersedia) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    @endif
@endsection
