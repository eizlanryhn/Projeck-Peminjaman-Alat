<div class="card">
    <div class="card-header">Kondisi Alat yang Dikembalikan</div>
    <div class="card-body p-0">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Alat</th>
                    <th class="text-center">Jumlah</th>
                    <th style="width: 200px">Kondisi Kembali</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($peminjaman->detail as $baris)
                    <tr>
                        <td>{{ $baris->alat->kode_alat }}</td>
                        <td>{{ $baris->alat->nama }}</td>
                        <td class="text-center">{{ $baris->jumlah }}</td>
                        <td>
                            <select name="kondisi[{{ $baris->id }}]" class="form-select form-select-sm" required>
                                <option value="baik">Baik</option>
                                <option value="rusak_ringan">Rusak Ringan</option>
                                <option value="rusak_berat">Rusak Berat</option>
                                <option value="hilang">Hilang</option>
                            </select>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
