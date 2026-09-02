<div class="card">
    <div class="card-header">Alat yang Diajukan</div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Alat</th>
                    <th class="text-center">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($isiKeranjang as $baris)
                    <tr>
                        <td>{{ $baris->alat->kode_alat }}</td>
                        <td>{{ $baris->alat->nama }}</td>
                        <td class="text-center">{{ $baris->jumlah }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
