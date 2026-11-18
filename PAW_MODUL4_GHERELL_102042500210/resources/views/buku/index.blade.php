@extends('layouts.app')

@section('title', 'Kelola Data Buku')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-0">Daftar Koleksi Buku</h4>
            <p class="text-muted small mb-0">Kelola data buku dan sampul perpustakaan.</p>
        </div>
        <a href="{{ route('buku.create') }}" class="btn btn-primary">+ Tambah Buku</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form Pencarian Buku Admin -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <form action="{{ route('buku.index') }}" method="GET" class="row g-2">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan judul, penulis, atau penerbit..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-secondary">Cari</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Data Buku -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">No</th>
                            <th>Sampul</th>
                            <th>Judul Buku</th>
                            <th>Penulis</th>
                            <th>Penerbit</th>
                            <th>Tahun</th>
                            <th>Stok</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($buku as $index => $item)
                            <tr>
                                <td class="ps-3">{{ $buku->firstItem() + $index }}</td>
                                <!-- TODO 9: Kolom Thumbnail Sampul Buku -->
                                <td>
                                    @if($item->cover)
                                        <img src="{{ asset('storage/' . $item->cover) }}" alt="{{ $item->judul }}" class="rounded border" style="width: 50px; height: 70px; object-fit: cover;">
                                    @else
                                        <span class="badge bg-secondary">No Cover</span>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $item->judul }}</td>
                                <td>{{ $item->penulis }}</td>
                                <td>{{ $item->penerbit }}</td>
                                <td>{{ $item->tahun_terbit }}</td>
                                <td>
                                    <span class="badge {{ $item->stok > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $item->stok }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('buku.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('buku.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Data buku tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $buku->links() }}
        </div>
    </div>
</div>
@endsection