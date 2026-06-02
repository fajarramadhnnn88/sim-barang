<?php
namespace App\Http\Controllers;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\StockBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller {
    public function index(Request $request){
        $barangs=Barang::with(['kategori','stockBalance'])
            ->when($request->search,fn($q)=>$q->search($request->search))
            ->when($request->kategori_id,fn($q)=>$q->where('kategori_id',$request->kategori_id))
            ->when($request->status,fn($q)=>$request->status==='aktif'?$q->where('is_active',true):$q->where('is_active',false))
            ->latest()->paginate(15)->withQueryString();
        $kategoris=Kategori::orderBy('nama_kategori')->get();
        return view('barang.index',compact('barangs','kategoris'));
    }
    public function create(){
        $kategoris=Kategori::orderBy('nama_kategori')->get();
        return view('barang.create',compact('kategoris'));
    }
    public function store(Request $request){
        $v=$request->validate([
            'kode_barang'=>'required|string|max:50|unique:barangs',
            'nama_barang'=>'required|string|max:150',
            'kategori_id'=>'required|exists:kategoris,id',
            'satuan'=>'required|string|max:30',
            'merk'=>'nullable|string|max:100',
            'spesifikasi'=>'nullable|string|max:255',
            'deskripsi'=>'nullable|string',
            'harga_satuan'=>'required|numeric|min:0',
            'stok_minimum'=>'required|integer|min:0',
            'lokasi_penyimpanan'=>'nullable|string|max:100',
            'foto'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        if($request->hasFile('foto')) $v['foto']=$request->file('foto')->store('barang','public');
        $barang=Barang::create($v);
        StockBalance::create(['barang_id'=>$barang->id,'stok_masuk'=>0,'stok_keluar'=>0,'stok_tersedia'=>0]);
        return redirect()->route('barang.index')->with('success','Barang berhasil ditambahkan.');
    }
    public function show(Barang $barang){
        $barang->load(['kategori','stockBalance','barangMasuks'=>fn($q)=>$q->latest()->take(8),'barangKeluars'=>fn($q)=>$q->latest()->take(8)]);
        return view('barang.show',compact('barang'));
    }
    public function edit(Barang $barang){
        $kategoris=Kategori::orderBy('nama_kategori')->get();
        return view('barang.edit',compact('barang','kategoris'));
    }
    public function update(Request $request, Barang $barang){
        $v=$request->validate([
            'kode_barang'=>'required|string|max:50|unique:barangs,kode_barang,'.$barang->id,
            'nama_barang'=>'required|string|max:150',
            'kategori_id'=>'required|exists:kategoris,id',
            'satuan'=>'required|string|max:30',
            'merk'=>'nullable|string|max:100',
            'harga_satuan'=>'required|numeric|min:0',
            'stok_minimum'=>'required|integer|min:0',
            'lokasi_penyimpanan'=>'nullable|string|max:100',
            'is_active'=>'boolean',
            'foto'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        if($request->hasFile('foto')){if($barang->foto)Storage::disk('public')->delete($barang->foto);$v['foto']=$request->file('foto')->store('barang','public');}
        $v['is_active']=$request->boolean('is_active',true);
        $barang->update($v);
        return redirect()->route('barang.show',$barang)->with('success','Data barang berhasil diperbarui.');
    }
    public function destroy(Barang $barang){
        if($barang->barangMasuks()->exists()||$barang->barangKeluars()->exists()) $barang->delete();
        else{if($barang->foto)Storage::disk('public')->delete($barang->foto);$barang->forceDelete();}
        return redirect()->route('barang.index')->with('success','Barang berhasil dihapus.');
    }
}
