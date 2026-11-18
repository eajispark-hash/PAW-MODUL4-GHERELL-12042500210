<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // TODO 3: Menampilkan halaman depan katalog buku dengan Search, Filter, Sort, & Pagination
    public function index(Request $request)
    {
        $search = $request->input('search');
        $penerbit = $request->input('penerbit');
        $status = $request->input('status');
        $sort = $request->input('sort', 'terbaru');

        // Daftar penerbit unik untuk dropdown filter (jika digunakan di view)
        $listPenerbit = Buku::select('penerbit')->distinct()->pluck('penerbit');

        $query = Buku::query();

        // 1. Pencarian berdasarkan judul atau penulis
        $query->when($search, function ($q, $search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%");
            });
        });

        // 2. Filter berdasarkan penerbit (opsional sesuai tampilan UI modul)
        $query->when($penerbit, function ($q, $penerbit) {
            return $q->where('penerbit', $penerbit);
        });

        // 3. Filter ketersediaan stok (tersedia / habis)
        $query->when($status, function ($q, $status) {
            if ($status === 'tersedia') {
                return $q->where('stok', '>', 0);
            } elseif ($status === 'habis') {
                return $q->where('stok', '<=', 0);
            }
        });

        // 4. Opsi pengurutan data (sort: judul, tahun, stok, terbaru)
        switch ($sort) {
            case 'judul':
                $query->orderBy('judul', 'asc');
                break;
            case 'tahun':
                $query->orderBy('tahun_terbit', 'desc');
                break;
            case 'stok':
                $query->orderBy('stok', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        // Paginasi dengan mempertahankan query string
        $buku = $query->paginate(8)->withQueryString();

        return view('home', compact('buku', 'search', 'penerbit', 'status', 'sort', 'listPenerbit'));
    }

    // Menampilkan rincian detail buku untuk publik/mahasiswa
    public function show(Buku $buku)
    {
        return view('home_show', compact('buku'));
    }
}