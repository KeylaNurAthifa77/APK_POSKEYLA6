@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="container py-4">

    {{-- Card Container Pembungkus --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-body p-4">

            {{-- Alert Success --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Alert Error --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Judul Halaman Users --}}
            <h1 class="h2 fw-bold mb-3" style="color: #4b3b43;">Halaman Users</h1>

            {{-- Tombol Tambah User --}}
            <div class="mb-3">
                <a href="{{ route('admin.users.create') }}" class="btn btn-cream fw-semibold">
                    <i class="bi bi-plus-lg me-1"></i> Tambah User
                </a>
            </div>

            {{-- Form Search --}}
            <form action="{{ route('admin.users') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="form-control"
                           placeholder="Cari nama user...">

                    <button class="btn btn-outline-secondary" type="submit">
                        Cari
                    </button>
                </div>
            </form>

            {{-- Tabel Data Users --}}
            <div class="table-responsive">
                <table class="table align-middle border-0 mb-0 w-100" style="font-size: 14px;">
                    <thead>
                        <tr>
                            <th scope="col" style="color: #4b3b43; width: 5%;">#</th>
                            <th scope="col" style="color: #4b3b43; width: 25%;">Name</th>
                            <th scope="col" style="color: #4b3b43; width: 30%;">Email</th>
                            <th scope="col" style="color: #4b3b43; width: 20%;">Role</th>
                            <th scope="col" style="color: #4b3b43; width: 20%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-0">
                        @forelse($users as $user)
                        <tr class="border-0">
                            <td>{{ $users->firstItem() + $loop->index }}</td>
                            <td class="fw-medium">{{ $user->name }}</td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">
                                    {{ $user->role->name ?? $user->role }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning fw-bold text-white px-2 py-1" style="font-size: 12px;">
                                        Edit Akun
                                    </a>

                                    <span class="text-muted mx-1">|</span>

                                    {{-- Form & Tombol Hapus --}}
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-danger fw-bold px-2 py-1" style="font-size: 12px;">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="border-0">
                            <td colspan="5" class="text-center py-4 text-muted">
                                Belum ada data user
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination dengan Teks Bahasa Indonesia di Dekat Angka Halaman --}}
            <div class="mt-3 d-flex flex-column flex-sm-row justify-content-end align-items-center gap-3 border-0 pt-0">
                <div class="text-muted small mb-0">
                    Menampilkan <strong>{{ $users->firstItem() ?? 0 }}</strong> - <strong>{{ $users->lastItem() ?? 0 }}</strong> dari <strong>{{ $users->total() }}</strong> hasil
                </div>
                <div class="pagination-clean">
                    {{ $users->links() }}
                </div>
            </div>

        </div>
    </div>

</div>

{{-- Custom Style --}}
<style>
    .btn-cream {
        background-color: #f5e6d3;
        color: #4b3b43;
        border: 1px solid #ebd4b9;
        transition: all 0.2s ease-in-out;
    }
    .btn-cream:hover {
        background-color: #ebd4b9;
        color: #35282e;
    }

    .table > :not(caption) > * > * {
        border-bottom-width: 0 !important;
        padding: 0.6rem 0.4rem !important;
    }

    /* Menyembunyikan teks keterangan Bahasa Inggris bawaan dari Laravel $users->links() */
    .pagination-clean nav div:first-child p.text-sm,
    .pagination-clean nav .small.text-muted {
        display: none !important;
    }
</style>
@endsection