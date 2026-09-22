@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <div class="card dashboard-card border-0 p-4">
                {{-- Header Profil --}}
                <div class="text-center mb-4">
                    <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center text-white mb-3 shadow" 
                         style="width: 90px; height: 90px; background: linear-gradient(135deg, #e83e8c, #ffd7e5); font-size: 40px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: #4b3b43;">Pengaturan Profil</h4>
                    <p class="text-muted small">Kelola informasi akun Anda di sini</p>
                </div>

                {{-- Field Nama Lengkap --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold small" style="color: #5c424c;">Nama Lengkap</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-0" value="{{ $user->name }}" readonly disabled>
                    </div>
                </div>

                {{-- Field Alamat Email --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold small" style="color: #5c424c;">Alamat Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" class="form-control bg-light border-0" value="{{ $user->email }}" readonly disabled>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection