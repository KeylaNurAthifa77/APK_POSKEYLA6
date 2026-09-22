@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mx-auto" style="max-width: 800px;">
        <div class="card-body p-4">
            <h1 class="h2 fw-bold mb-4" style="color: #4b3b43;">Tambah Produk</h1>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                    <div class="fw-bold mb-1">Terjadi kesalahan input:</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
                @include('produk._form')
            </form>
        </div>
    </div>
</div>
@endsection