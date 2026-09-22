@extends('layouts.app')

@section('title', 'Profil Maison Fashion Boutique')

@section('content')
<div class="container py-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold" style="color: #4b3b43;">Profil Maison Fashion Boutique</h2>
        <p class="text-muted">Sistem Manajemen Point of Sale & Katalog Produk High-End Boutique</p>
    </div>

    <div class="row g-4 justify-content-center">
        {{-- Card Identitas Boutique --}}
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100">
                
                {{-- Gambar / Foto Profil Toko --}}
                <div class="mx-auto mb-3 overflow-hidden shadow-sm rounded-4" style="width: 110px; height: 110px; border: 3px solid #fae1dd;">
                    <img src="{{ asset('img/images.png') }}" alt="Foto Maison Fashion Boutique" class="w-100 h-100" style="object-fit: cover;">
                </div>
                
                <h3 class="fw-bold mb-1" style="color: #4b3b43;">Maison Fashion</h3>
                <span class="badge bg-danger px-3 py-2 rounded-pill mx-auto mb-3">Premium Clothing & Accessories</span>
                
                <p class="text-muted small">
                    Maison Fashion Boutique adalah brand busana modern yang menghadirkan koleksi pakaian berkualitas tinggi, trendi, dan elegan untuk memenuhi gaya hidup Anda.
                </p>

                <hr class="my-4" style="border-color: #fae1dd;">

                <div class="text-start">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-geo-alt-fill text-danger me-3 fs-5"></i>
                        <div>
                            <small class="text-muted d-block">Lokasi Boutique</small>
                            <strong>Tasikmalaya, Jawa Barat, Indonesia</strong>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-clock-fill text-danger me-3 fs-5"></i>
                        <div>
                            <small class="text-muted d-block">Jam Operasional</small>
                            <strong>Senin - Minggu (09.00 - 21.00 WIB)</strong>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-instagram text-danger me-3 fs-5"></i>
                        <div>
                            <small class="text-muted d-block">Instagram Official</small>
                            <strong>@maisonfashion.id</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Fokus Visi & Layanan --}}
        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-3" style="color: #4b3b43;"><i class="bi bi-gem text-danger me-2"></i> Konsep & Keunggulan</h5>
                
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 bg-light border-0">
                            <i class="bi bi-check2-circle text-danger fs-4 mb-2 d-block"></i>
                            <h6 class="fw-bold">Kualitas Terjamin</h6>
                            <small class="text-muted">Setiap produk pakaian dipilih dari bahan kain pilihan dan jahitan presisi tinggi.</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 bg-light border-0">
                            <i class="bi bi-check2-circle text-danger fs-4 mb-2 d-block"></i>
                            <h6 class="fw-bold">Sistem POS Terintegrasi</h6>
                            <small class="text-muted">Memudahkan kasir dan staf dalam memproses transaksi kasir secara cepat dan akurat.</small>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold mb-3" style="color: #4b3b43;"><i class="bi bi-shop me-2 text-danger"></i> Layanan Sistem Aplikasi</h5>
                <ul class="list-group list-group-flush rounded-3">
                    <li class="list-group-item bg-light border-0 mb-1"><i class="bi bi-caret-right-fill text-danger me-2"></i> Pencatatan Stok Produk & Kategori Jenis Pakaian</li>
                    <li class="list-group-item bg-light border-0 mb-1"><i class="bi bi-caret-right-fill text-danger me-2"></i> Transaksi Penjualan Instan & Cetak Struk Belanja</li>
                    <li class="list-group-item bg-light border-0"><i class="bi bi-caret-right-fill text-danger me-2"></i> Pengelolaan Pengguna (Role Admin & Kasir)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection