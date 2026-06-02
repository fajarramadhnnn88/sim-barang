<?php
namespace App\Http\Controllers;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangMasukController extends Controller {
    public function index(Request $request){
        $items=BarangMasuk::with(['barang.kategori','supplier','user'])
            ->when($request->search,fn($q)=>$q->where('no_transaksi','like',"%{$request->search}%")->orWhereHas('barang',fn($q2)=>$q2->where('nama_barang','like',"%{$request->search}%")))
            ->when($request->dari&&$request->sampai,fn($q)=>$q->periode($request->dari,$request->sampai))
            ->latest()->paginate(15)->withQueryString();
        return view('barang-masuk.index',compact('items'));
    }
    public function create(){
        $barangs=Barang::active()->orderBy('nama_barang')->get();
        $suppliers=Supplier::active()->orderBy('nama_supplier')->get();
        return view('barang-masuk.create',compact('barangs','suppliers'));
    }
    public function store(Request $request){
        $v=$request->validate([
            'barang_id'=>'required|exists:barangs,id',
            'supplier_id'=>'nullable|exists:suppliers,id',
            'jumlah'=>'required|integer|min:1',
            'harga_satuan'=>'required|numeric|min:0',
            'tanggal_masuk'=>'required|date',
            'no_surat_jalan'=>'nullable|string|max:100',
            'no_po'=>'nullable|string|max:100',
            'keterangan'=>'nullable|string',
        ]);
        $v['user_id']=Auth::id();
        BarangMasuk::create($v);
        return redirect()->route('barang-masuk.index')->with('success','Barang masuk berhasil dicatat.');
    }
    public function show(BarangMasuk $barangMasuk){
        $barangMasuk->load(['barang','supplier','user']);
        return view('barang-masuk.show',compact('barangMasuk'));
    }
    public function edit(BarangMasuk $barangMasuk){
        $barangs=Barang::active()->orderBy('nama_barang')->get();
        $suppliers=Supplier::active()->orderBy('nama_supplier')->get();
        return view('barang-masuk.edit',compact('barangMasuk','barangs','suppliers'));
    }
    public function update(Request $request, BarangMasuk $barangMasuk){
        $v=$request->validate([
            'barang_id'=>'required|exists:barangs,id',
            'supplier_id'=>'nullable|exists:suppliers,id',
            'jumlah'=>'required|integer|min:1',
            'harga_satuan'=>'required|numeric|min:0',
            'tanggal_masuk'=>'required|date',
            'no_surat_jalan'=>'nullable|string|max:100',
            'no_po'=>'nullable|string|max:100',
            'keterangan'=>'nullable|string',
        ]);
        $barangMasuk->delete();
        $v['user_id']=Auth::id();
        BarangMasuk::create($v);
        return redirect()->route('barang-masuk.index')->with('success','Data barang masuk berhasil diperbarui.');
    }
    public function destroy(BarangMasuk $barangMasuk){
        $barangMasuk->delete();
        return redirect()->route('barang-masuk.index')->with('success','Data barang masuk berhasil dihapus.');
    }
}
