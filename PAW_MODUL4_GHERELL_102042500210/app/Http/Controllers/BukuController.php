<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
{
    // Menampilkan daftar buku di halaman Admin (dengan pencarian & paginasi 5 data)
    public function index(Request $request)
    {
        $search = $request->input('search');

        $buku = Buku::when($search, function ($query, $search) {
            return $query->where('judul', 'like', "%{$search}%")
                         ->orWhere('penulis', 'like', "%{$search}%")
                         ->orWhere('penerbit', 'like', "%{$search}%");
        })->latest()->paginate(5)->withQueryString();

        // Sesuaikan nama view dengan struktur folder di repo ('buku.index' atau 'admin.buku.index')
        $view = view()->exists('admin.buku.index') ? 'admin.buku.index' : 'buku.index';
        return view($view, compact('buku', 'search'));
    }

    public function create()
    {
        $view = view()->exists('admin.buku.create') ? 'admin.buku.create' : 'buku.create';
        return view($view);
    }

    // TODO 4: Validasi berkas sampul & simpan ke folder 'buku' di storage publik
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'        => 'required|string|max:255',
            'penulis'      => 'required|string|max:255',
            'penerbit'     => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|min:1900|max:' . date('Y'),
            'stok'         => 'required|integer|min:0',
            'cover'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('cover')) {
            $validated['cover'] = $request->file('cover')->store('buku', 'public');
        }

        Buku::create($validated);

        return redirect()->route('buku.index')->with('success', 'Data buku berhasil ditambahkan!');
    }

    public function show(Buku $buku)
    {
        $view = view()->exists('admin.buku.show') ? 'admin.buku.show' : 'buku.show';
        return view($view, compact('buku'));
    }

    public function edit(Buku $buku)
    {
        $view = view()->exists('admin.buku.edit') ? 'admin.buku.edit' : 'buku.edit';
        return view($view, compact('buku'));
    }

    // TODO 5: Hapus berkas fisik sampul lama jika ada sampul baru yang diunggah
    public function update(Request $request, Buku $buku)
    {
        $validated = $request->validate([
            'judul'        => 'required|string|max:255',
            'penulis'      => 'required|string|max:255',
            'penerbit'     => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|min:1900|max:' . date('Y'),
            'stok'         => 'required|integer|min:0',
            'cover'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('cover')) {
            // Hapus sampul lama jika ada di storage
            if ($buku->cover && Storage::disk('public')->exists($buku->cover)) {
                Storage::disk('public')->delete($buku->cover);
            }
            // Simpan sampul baru
            $validated['cover'] = $request->file('cover')->store('buku', 'public');
        }

        $buku->update($validated);

        return redirect()->route('buku.index')->with('success', 'Data buku berhasil diperbarui!');
    }

    // TODO 6: Hapus fisik berkas sampul dari storage sebelum data buku dihapus dari database
    public function destroy(Buku $buku)
    {
        if ($buku->cover && Storage::disk('public')->exists($buku->cover)) {
            Storage::disk('public')->delete($buku->cover);
        }

        $buku->delete();

        return redirect()->route('buku.index')->with('success', 'Data buku beserta sampulnya berhasil dihapus!');
    }
}