@extends('layouts.app')

@section('title', $buku->judul)

@section('content')
<div class="container py-4">
    <div class="mb-3">
        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">&larr; Kembali ke Katalog</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="row g-4 align-items-center">
                <div class="col-md-4 text-center">
                    @if($buku->cover)
                        <img src="{{ asset('storage/' . $buku->cover) }}" alt="{{ $buku->judul }}" class="img-fluid rounded shadow-sm" style="max-height: 380px; object-fit: cover;">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded" style="height: 320px;">
                            <span>Tidak Ada Sampul</span>
                        </div>
                    @endif
                </div>
                <div class="col-md-8">
                    <h3 class="fw-bold mb-1">{{ $buku->judul }}</h3>
                    <p class="text-muted fs-5 mb-4">{{ $buku->penulis }}</p>

                    <table class="table table-borderless w-auto mb-4">
                        <tr>
                            <td class="text-muted pe-4 py-1">Penerbit</td>
                            <td class="fw-semibold py-1">{{ $buku->penerbit }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted pe-4 py-1">Tahun Terbit</td>
                            <td class="fw-semibold py-1">{{ $buku->tahun_terbit }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted pe-4 py-1">Status Ketersediaan</td>
                            <td class="py-1">
                                <span class="badge {{ $buku->stok > 0 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $buku->stok > 0 ? 'Tersedia (' . $buku->stok . ' eksemplar)' : 'Stok Habis' }}
                                </span>
                            </td>
                        </tr>
                    </table>

                    <div class="alert alert-info mb-0">
                        Ingin meminjam buku ini? Silakan Login terlebih dahulu atau temui petugas perpustakaan.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection