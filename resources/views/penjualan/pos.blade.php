@extends('layouts.app')

@section('title', 'POS - Tambah Penjualan')

@section('content')
<div class="container py-4">

    {{-- Pesan Status / Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('errors'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3" role="alert">
            {{ session('errors') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h2 fw-bold mb-0" style="color: #4b3b43;">Tambah Penjualan</h1>
                <a href="{{ route('penjualan.index') }}" class="btn btn-light border fw-semibold">Kembali</a>
            </div>

            <div class="row g-4">
                
                {{-- KIRI: Cari & Daftar Produk --}}
                <div class="col-md-7">
                    <form action="{{ route('penjualan.edit', $sale->id) }}" method="GET" class="mb-3">
                        <div class="input-group">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   class="form-control" 
                                   placeholder="Cari produk...">
                            <button class="btn btn-outline-secondary" type="submit">Cari</button>
                        </div>
                    </form>

                    <div class="d-flex flex-column gap-2" style="max-height: 500px; overflow-y: auto;">
                        @forelse($products as $product)
                        <div class="card border rounded-3 p-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    @if($product->foto)
                                        <img src="{{ asset('storage/'.$product->foto) }}" class="rounded" width="45" height="45" style="object-fit: cover;">
                                    @else
                                        <div class="rounded bg-light border d-flex align-items-center justify-content-center text-muted small" style="width: 45px; height: 45px;">No Img</div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark">{{ $product->nama }}</div>
                                        <small class="text-muted">Rp {{ number_format($product->harga_jual, 0, ',', '.') }} | Stok: {{ $product->stok }}</small>
                                    </div>
                                </div>

                                <form action="{{ route('penjualan.addItem', $sale->id) }}" method="POST" class="d-flex align-items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="number" name="qty" value="1" min="1" max="{{ $product->stok }}" class="form-control form-control-sm text-center" style="width: 60px;" required>
                                    <button type="submit" class="btn btn-sm text-white px-3 fw-bold" style="background-color: #eb7a61;">+</button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">Produk tidak ditemukan</div>
                        @endforelse
                    </div>
                </div>

                {{-- KANAN: Detail Keranjang & Pembayaran --}}
                <div class="col-md-5">
                    <div class="card border rounded-4 p-3 bg-light">
                        
                        <div class="table-responsive mb-3">
                            <table class="table table-borderless align-middle mb-0" style="font-size: 13px;">
                                <thead>
                                    <tr class="text-muted border-bottom">
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sale->itemPenjualan ?? [] as $item)
                                    <tr>
                                        <td class="fw-bold">{{ Str::limit($item->produk->nama ?? '-', 12) }}</td>
                                        <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="text-center">{{ $item->kuantitas }}</td>
                                        <td class="text-end fw-bold text-danger">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('penjualan.removeItem', $item->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger py-0 px-2 rounded-pill" onclick="return confirm('Hapus item ini?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Keranjang masih kosong</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        {{-- Form Checkout --}}
                        <form id="checkoutForm" action="{{ route('penjualan.update', $sale->id) }}" method="POST" class="mb-2">
                            @csrf
                            @method('PUT')

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="h5 fw-bold mb-0">Total</span>
                                <span class="h5 fw-bold mb-0 text-danger" id="total_belanja" data-total="{{ $sale->total_pembayaran ?? 0 }}">
                                    Rp {{ number_format($sale->total_pembayaran ?? 0, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted fw-semibold">Metode Pembayaran</label>
                                <select name="payment_method" id="payment_method" class="form-select" required>
                                    <option value="CASH">CASH</option>
                                    <option value="QRIS">QRIS</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="bayar" class="form-label small text-muted fw-semibold">Uang Diterima (Rp)</label>
                                <input type="number" id="bayar" name="bayar" class="form-control" placeholder="Masukkan nominal..." min="0" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted fw-semibold">Kembalian (Rp)</label>
                                <input type="text" id="kembalian" class="form-control bg-white fw-bold text-muted" value="Rp 0" readonly>
                            </div>

                            <button type="submit" id="btnSubmitCheckout" class="btn btn-danger w-100 py-2 fw-semibold" style="background-color: #eb7a61; border: none;">
                                Checkout
                            </button>
                        </form>

                        <form action="{{ route('penjualan.destroy', $sale->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-light border w-100 py-2 fw-semibold text-muted" onclick="return confirm('Batalkan dan hapus draft transaksi ini?')">
                                Batalkan Transaksi
                            </button>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- MODAL POPUP QRIS AUTOMATIS --}}
<div class="modal fade" id="qrisModal" tabindex="-1" aria-labelledby="qrisModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center rounded-4 border-0 p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-center w-100" id="qrisModalLabel" style="color: #4b3b43;">Pembayaran QRIS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-2">Scan kode QR berikut menggunakan aplikasi e-Wallet atau M-Banking</p>
                <div class="p-3 bg-light rounded-4 d-inline-block border mb-3">
                    <img id="qrisImage" src="" alt="QRIS Code" class="img-fluid" style="max-width: 220px; height: auto;">
                </div>
                <div class="h5 fw-bold text-danger mb-0" id="modalTotalAmount">Rp 0</div>
            </div>
            <div class="modal-footer border-0 pt-0 d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnConfirmQris" class="btn text-white px-4 fw-bold" style="background-color: #eb7a61;">Konfirmasi Pembayaran</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkoutForm = document.getElementById('checkoutForm');
    const bayarInput = document.getElementById('bayar');
    const kembalianInput = document.getElementById('kembalian');
    const totalElement = document.getElementById('total_belanja');
    const paymentMethodSelect = document.getElementById('payment_method');
    
    // Modal Elements
    const qrisModal = new bootstrap.Modal(document.getElementById('qrisModal'));
    const qrisImage = document.getElementById('qrisImage');
    const modalTotalAmount = document.getElementById('modalTotalAmount');
    const btnConfirmQris = document.getElementById('btnConfirmQris');

    let isQrisConfirmed = false;

    function hitungKembalian() {
        const total = parseFloat(totalElement.getAttribute('data-total')) || 0;
        const bayar = parseFloat(bayarInput.value) || 0;
        const kembalian = bayar - total;

        if (bayarInput.value === '' || bayar === 0) {
            kembalianInput.value = "Rp 0";
            kembalianInput.className = "form-control bg-white fw-bold text-muted";
        } else if (kembalian < 0) {
            const kurang = Math.abs(kembalian);
            kembalianInput.value = "Kurang Rp " + kurang.toLocaleString('id-ID');
            kembalianInput.className = "form-control bg-white fw-bold text-danger";
        } else {
            kembalianInput.value = "Rp " + kembalian.toLocaleString('id-ID');
            kembalianInput.className = "form-control bg-white fw-bold text-success";
        }
    }

    // Auto fill nominal bayar pas ketika memilih QRIS
    paymentMethodSelect.addEventListener('change', function() {
        const total = parseFloat(totalElement.getAttribute('data-total')) || 0;
        if (this.value === 'QRIS') {
            bayarInput.value = total;
            hitungKembalian();
        } else {
            bayarInput.value = '';
            hitungKembalian();
        }
    });

    bayarInput.addEventListener('input', hitungKembalian);

    // Intercept form submit saat metode QRIS dipilih
    checkoutForm.addEventListener('submit', function (e) {
        const selectedMethod = paymentMethodSelect.value;
        const total = parseFloat(totalElement.getAttribute('data-total')) || 0;
        const saleId = "{{ $sale->id }}";

        if (selectedMethod === 'QRIS' && !isQrisConfirmed) {
            e.preventDefault();

            // Sediakan URL payload transaksi otomatis via QR Server API
            const qrPayload = `TRX-${saleId}-TOTAL-${total}`;
            const qrApiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(qrPayload)}`;

            qrisImage.src = qrApiUrl;
            modalTotalAmount.innerText = "Rp " + total.toLocaleString('id-ID');
            
            // Tampilkan Modal QRIS
            qrisModal.show();
        }
    });

    // Submit form setelah kasir menekan tombol konfirmasi pembayaran di modal
    btnConfirmQris.addEventListener('click', function () {
        isQrisConfirmed = true;
        qrisModal.hide();
        checkoutForm.submit();
    });
});
</script>
@endsection