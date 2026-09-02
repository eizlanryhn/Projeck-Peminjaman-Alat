<div class="card mb-3">
    <div class="card-header">Setujui</div>
    <div class="card-body">
        <form method="POST" action="{{ route('persetujuan.setujui', $peminjaman) }}">
            @csrf
            <x-input label="Tanggal Harus Kembali" name="tgl_harus_kembali" type="date"
                :value="$peminjaman->tgl_harus_kembali->toDateString()" required />
            <button type="submit" class="btn btn-success w-100">
                Setujui Peminjaman
            </button>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header">Tolak</div>
    <div class="card-body">
        <form method="POST" action="{{ route('persetujuan.tolak', $peminjaman) }}">
            @csrf
            <x-textarea label="Alasan Penolakan" name="alasan_tolak" rows="3" required />
            <button type="submit" class="btn btn-danger w-100">
                Tolak Peminjaman
            </button>
        </form>
    </div>
</div>
