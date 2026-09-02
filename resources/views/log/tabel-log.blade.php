<table class="table table-striped table-sm align-middle mb-0">
    <thead>
        <tr>
            <th style="width: 150px">Waktu</th>
            <th>Pengguna</th>
            <th>Aksi</th>
            <th>Tabel</th>
            <th>Deskripsi</th>
            <th>IP</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($daftarLog as $log)
            <tr>
                <td class="text-nowrap small">
                    {{ $log->created_at?->format('d/m/Y H:i:s') }}
                </td>
                <td class="small">
                    {{ $log->pengguna->nama ?? 'Tidak dikenal' }}
                </td>
                <td>
                    <span class="badge bg-secondary">{{ $log->aksi }}</span>
                </td>
                <td class="small text-muted">{{ $log->tabel_tujuan }}</td>
                <td class="small">{{ $log->deskripsi }}</td>
                <td class="small text-muted">{{ $log->ip_address ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-3">
                    Belum ada log aktivitas.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
