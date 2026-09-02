<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th style="width: 60px">No</th>
                <th>Nama</th>
                <th>Nama Pengguna</th>
                <th>Peran</th>
                <th>Telepon</th>
                <th>Status</th>
                <th style="width: 160px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($daftarPengguna as $nomor => $pengguna)
                <tr>
                    <td>{{ $daftarPengguna->firstItem() + $nomor }}</td>
                    <td>{{ $pengguna->nama }}</td>
                    <td>{{ $pengguna->username }}</td>
                    <td>
                        @foreach ($pengguna->roles as $peranPengguna)
                            <span class="badge bg-info text-dark">
                                {{ ucfirst($peranPengguna->name) }}
                            </span>
                        @endforeach
                    </td>
                    <td>{{ $pengguna->no_telp ?: '-' }}</td>
                    <td>
                        <span class="badge bg-{{ $pengguna->is_aktif ? 'success' : 'secondary' }}">
                            {{ $pengguna->is_aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        @php
                            $hapus = $pengguna->id !== auth()->id() ? route('pengguna.destroy', $pengguna) : null;
                        @endphp
                        <x-tombol-aksi :ubah="route('pengguna.edit', $pengguna)" :hapus="$hapus"
                            pesanHapus="Yakin ingin menghapus pengguna {{ $pengguna->nama }}?" />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        Data pengguna tidak ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
