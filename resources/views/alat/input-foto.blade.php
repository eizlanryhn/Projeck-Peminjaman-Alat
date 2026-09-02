<div class="mb-3">
    <label for="foto" class="form-label">Foto</label>
    <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto"
        accept="image/jpeg, image/png">
    @error('foto')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    @if ($alat->foto)
        <div class="mt-2">
            <img src="{{ asset('gambar/alat/' . $alat->foto) }}" class="rounded" width="100">
            <div class="form-text">
                Biarkan kosong bila tidak ingin mengganti foto.
            </div>
        </div>
    @endif
</div>
