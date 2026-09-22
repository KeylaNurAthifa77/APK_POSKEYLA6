<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemPenjualanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\JenisController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
});

Route::post('/auth', [AuthController::class, 'auth'])->name('auth');

Route::middleware('auth')->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route Profil User (Kanan Atas Navbar)
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Route Profil Aplikasi / Maison Fashion Boutique (Klik Logo POS)
    Route::get('/maison-profile', function () {
        return view('maison_profile');
    })->name('boutique.profile');

    // Route Tentang
    Route::get('/tentang', function () {
        return view('tentang');
    })->name('tentang.index');

    // KHUSUS ADMIN
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/edit/{user}', [UserController::class, 'edit'])->name('users.edit');
        Route::match(['PUT', 'POST'], '/users/update/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/destroy/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // BISA DIAKSES ADMIN & KASIR
    Route::middleware('role:admin,kasir')->group(function () {
        Route::resource('/jenis', JenisController::class);
        Route::resource('/produk', ProdukController::class);
        
        Route::post('/penjualan/{penjualan}/add-item', [PenjualanController::class, 'addItem'])->name('penjualan.addItem');
        Route::delete('/penjualan/item/{itemPenjualan}', [PenjualanController::class, 'removeItem'])->name('penjualan.removeItem');
        Route::get('/penjualan/{penjualan}/cetak-struk', [PenjualanController::class, 'cetakStruk'])->name('penjualan.cetak-struk');

        Route::resource('/penjualan', PenjualanController::class);
        Route::resource('/itempenjualan', ItemPenjualanController::class);
    });

});