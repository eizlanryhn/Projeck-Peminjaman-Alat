<div class="card">
    <div class="card-header">Data Peminjaman</div>
    <div class="card-body">
        @error('keranjang')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        <form method="POST" action="{{ route('peminjaman.simpan') }}">
            @csrf
            <x-input label="Tanggal Pinjam" type="date" name="tgl_pinjam"
                :value="now()->toDateString()" readonly class="bg-light" required />
            <div class="form-text mb-3 text-muted">
                Tanggal pinjam otomatis terisi hari ini (real-time) dan tidak dapat diubah manual.
            </div>

            <x-input label="Tanggal Harus Kembali" type="date" name="tgl_harus_kembali"
                :value="old('tgl_harus_kembali', now()->addDays($defaultHari)->toDateString())"
                min="{{ now()->toDateString() }}"
                max="{{ now()->addDays($maksHari)->toDateString() }}"
                required />
            <div class="form-text mb-3">
                Durasi bawaan {{ $defaultHari }} hari, maksimal {{ $maksHari }} hari.
            </div>

            <x-textarea label="Keperluan" name="keperluan" :value="old('keperluan')"
                minlength="5" maxlength="500"
                placeholder="Jelaskan tujuan peminjaman (minimal 5 karakter)" />
            <button type="submit" class="btn btn-primary w-100" {{ $daftarTunggakan->isNotEmpty() ? 'disabled' : '' }}>
                Kirim Pengajuan
            </button>
            <a href="{{ route('katalog.keranjang') }}" class="btn btn-secondary w-100 mt-2">
                Kembali ke Keranjang
            </a>
        </form>
    </div>
</div>
