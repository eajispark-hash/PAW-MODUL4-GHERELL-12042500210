@extends('layouts.app')

@section('title', 'Katalog Koleksi Buku')

@section('content')
<div class="container py-4">
    <!-- Notifikasi Error (Contoh: Saat Mahasiswa ditolak RoleMiddleware) -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="text-center mb-4">
        <h2 class="fw-bold">Katalog Koleksi Buku</h2>
        <p class="text-muted">Temukan dan pinjam buku referensi perkuliahan terbaikmu</p>
    </div>

    <!-- TODO 10: Formulir Pencarian Terpadu, Filter Stok, & Pengurutan -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('home') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari judul, penulis..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="penerbit" class="form-select">
                        <option value="">Semua Penerbit</option>
                        @if(isset($listPenerbit))
                            @foreach($listPenerbit as $p)
                                <option value="{{ $p }}" {{ request('penerbit') == $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="habis" {{ request('status') == 'habis' ? 'selected' : '' }}>Stok Habis</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select">
                        <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Urutkan: Terbaru</option>
                        <option value="judul" {{ request('sort') == 'judul' ? 'selected' : '' }}>Judul (A-Z)</option>
                        <option value="tahun" {{ request('sort') == 'tahun' ? 'selected' : '' }}>Tahun Terbit</option>
                        <option value="stok" {{ request('sort') == 'stok' ? 'selected' : '' }}>Stok Terbanyak</option>
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Grid Katalog Buku -->
    <div class="row">
        @forelse($buku as $item)
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    @if($item->cover)
                        <img src="{{ asset('storage/' . $item->cover) }}" class="card-img-top" style="height: 240px; object-fit: cover;" alt="{{ $item->judul }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="height: 240px;">
                            <span>Tidak Ada Sampul</span>
                        </div>
                    @endif
                    <div class="card-body">
                        <h6 class="fw-bold mb-1">{{ $item->judul }}</h6>
                        <p class="text-muted small mb-2">{{ $item->penulis }}</p>
                        <span class="badge {{ $item->stok > 0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $item->stok > 0 ? 'Tersedia (' . $item->stok . ')' : 'Stok Habis' }}
                        </span>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="{{ route('buku.publik.show', $item->id) }}" class="btn btn-outline-primary btn-sm w-100">Lihat Detail</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center text-muted py-5">Buku tidak ditemukan.</p>
            </div>
        @endforelse
    </div>

    <!-- Navigasi Paginasi -->
    <div class="d-flex justify-content-center mt-3">
        {{ $buku->links() }}
    </div>
</div>
@endsection