<?php
namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller {

    public function index(Request $request) {
        $kategoris = Kategori::withCount('barangs')
            ->when($request->search, fn($q) => $q->where('nama_kategori','like',"%{$request->search}%")
                ->orWhere('kode_kategori','like',"%{$request->search}%"))
            ->latest()->paginate(15)->withQueryString();
        return view('kategori.index', compact('kategoris'));
    }

    public function create() {
        return view('kategori.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100',
            'kode_kategori' => 'required|string|max:20|unique:kategoris',
            'deskripsi'     => 'nullable|string',
        ]);

        $kategori = Kategori::create($validated);

        // Response JSON untuk AJAX (modal inline di form barang)
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'kategori' => [
                    'id'            => $kategori->id,
                    'nama_kategori' => $kategori->nama_kategori,
                    'kode_kategori' => $kategori->kode_kategori,
                ],
            ]);
        }

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori) {
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori) {
        $request->validate([
            'nama_kategori' => 'required|string|max:100',
            'kode_kategori' => 'required|string|max:20|unique:kategoris,kode_kategori,'.$kategori->id,
            'deskripsi'     => 'nullable|string',
        ]);
        $kategori->update($request->only(['nama_kategori','kode_kategori','deskripsi']));
        return redirect()->route('kategori.index')->with('success','Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori) {
        if ($kategori->barangs()->count() > 0)
            return back()->with('error',"Tidak bisa menghapus '{$kategori->nama_kategori}' karena masih digunakan {$kategori->barangs()->count()} barang.");
        $kategori->delete();
        return redirect()->route('kategori.index')->with('success','Kategori berhasil dihapus.');
    }
}