@csrf

{{-- Preview Foto Saat Ini & Foto Baru --}}
<div class="row mb-3">
    @if (!empty($produk->foto))
        <div class="col-md-6 mb-2">
            <label class="form-label fw-semibold">Foto Saat Ini</label><br>
            <img src="{{ asset('storage/' . $produk->foto) }}"
                 alt="Foto Produk"
                 class="img-thumbnail rounded-3 shadow-sm"
                 style="max-height: 120px; object-fit: cover;">
        </div>
    @endif

    <div class="col-md-6 mb-2 d-none" id="previewContainer">
        <label class="form-label fw-semibold">Preview Foto Baru</label><br>
        <img id="imgPreview"
             src="#"
             alt="Preview Foto Baru"
             class="img-thumbnail rounded-3 shadow-sm"
             style="max-height: 120px; object-fit: cover;">
    </div>
</div>

{{-- Upload Gambar --}}
<div class="mb-3">
    <label for="foto" class="form-label fw-semibold">Gambar Produk</label>
    <input type="file"
           id="foto"
           name="foto"
           accept="image/*"
           onchange="previewImage(event)"
           class="form-control @error('foto') is-invalid @enderror">

    @error('foto')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Nama Produk --}}
<div class="mb-3">
    <label for="nama" class="form-label fw-semibold">Nama Produk</label>
    <input type="text"
           id="nama"
           name="nama"
           class="form-control @error('nama') is-invalid @enderror"
           value="{{ old('nama', $produk->nama ?? '') }}"
           placeholder="Masukkan nama produk"
           required>

    @error('nama')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Jenis --}}
<div class="mb-3">
    <label for="jenis_id" class="form-label fw-semibold">Jenis</label>
    <select id="jenis_id"
            name="jenis_id"
            class="form-select @error('jenis_id') is-invalid @enderror"
            required>
        <option value="">-- Pilih Jenis --</option>
        @foreach($jenisList as $item)
            <option value="{{ $item->id }}"
                {{ old('jenis_id', $produk->jenis_id ?? '') == $item->id ? 'selected' : '' }}>
                {{ $item->nama_jenis ?? $item->nama }}
            </option>
        @endforeach
    </select>

    @error('jenis_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Harga Pokok --}}
<div class="mb-3">
    <label for="harga_beli" class="form-label fw-semibold">Harga Pokok (Rp)</label>
    <input type="number"
           id="harga_beli"
           name="harga_beli"
           class="form-control @error('harga_beli') is-invalid @enderror"
           value="{{ old('harga_beli', $produk->harga_beli ?? '') }}"
           placeholder="Contoh: 15000"
           required>

    @error('harga_beli')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Harga Jual --}}
<div class="mb-3">
    <label for="harga_jual" class="form-label fw-semibold">Harga Jual (Rp)</label>
    <input type="number"
           id="harga_jual"
           name="harga_jual"
           class="form-control @error('harga_jual') is-invalid @enderror"
           value="{{ old('harga_jual', $produk->harga_jual ?? '') }}"
           placeholder="Contoh: 20000"
           required>

    @error('harga_jual')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Stok --}}
<div class="mb-4">
    <label for="stok" class="form-label fw-semibold">Stok</label>
    <input type="number"
           id="stok"
           name="stok"
           class="form-control @error('stok') is-invalid @enderror"
           value="{{ old('stok', $produk->stok ?? 0) }}"
           placeholder="Masukkan jumlah stok"
           required>

    @error('stok')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Tombol Aksi --}}
<div class="d-flex align-items-center gap-2">
    <button type="submit" class="btn text-white px-4 py-2 rounded-3 fw-semibold shadow-sm" style="background-color: #E87A5D; border: none;">
        Simpan
    </button>
    <a href="{{ route('produk.index') }}" class="btn px-4 py-2 rounded-3 fw-semibold" style="background-color: #F3E8E8; color: #6C5F67; border: none;">
        Kembali
    </a>
</div>

{{-- Script JS Preview Gambar --}}
<script>
    function previewImage(event) {
        const input = event.target;
        const container = document.getElementById('previewContainer');
        const imgPreview = document.getElementById('imgPreview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imgPreview.src = e.target.result;
                container.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            container.classList.add('d-none');
        }
    }
</script>