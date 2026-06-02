<?php
namespace App\Http\Controllers;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\Divisi;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangKeluarController extends Controller {
    public function __construct(private StockService $stockService){}
    public function index(Request $request){
        $items=BarangKeluar::with(['barang.kategori','divisi','user'])
            ->when($request->search,fn($q)=>$q->where('no_transaksi','like',"%{$request->search}%"))
            ->when($request->dari&&$request->sampai,fn($q)=>$q->periode($request->dari,$request->sampai))
            ->latest()->paginate(15)->withQueryString();
        return view('barang-keluar.index',compact('items'));
    }
    public function create(){
        $barangs=Barang::with('stockBalance')->active()->orderBy('nama_barang')->get();
        $divisis=Divisi::active()->orderBy('nama_divisi')->get();
        return view('barang-keluar.create',compact('barangs','divisis'));
    }
    public function store(Request $request){
        $v=$request->validate([
            'barang_id'=>'required|exists:barangs,id',
            'divisi_id'=>'nullable|exists:divisis,id',
            'jumlah'=>'required|integer|min:1',
            'tanggal_keluar'=>'required|date',
            'penerima'=>'nullable|string|max:100',
            'keterangan'=>'nullable|string',
        ]);
        $barang=Barang::findOrFail($v['barang_id']);
        if(!$this->stockService->cukupUntuk($barang,$v['jumlah']))
            return back()->withErrors(['jumlah'=>"Stok tidak mencukupi. Tersedia: {$barang->stok_tersedia} {$barang->satuan}."])->withInput();
        $v['user_id']=Auth::id();
        BarangKeluar::create($v);
        return redirect()->route('barang-keluar.index')->with('success','Barang keluar berhasil dicatat.');
    }
    public function show(BarangKeluar $barangKeluar){
        $barangKeluar->load(['barang','divisi','user','pengajuan']);
        return view('barang-keluar.show',compact('barangKeluar'));
    }
    public function edit(BarangKeluar $barangKeluar){
        $barangs=Barang::with('stockBalance')->active()->orderBy('nama_barang')->get();
        $divisis=Divisi::active()->orderBy('nama_divisi')->get();
        return view('barang-keluar.edit',compact('barangKeluar','barangs','divisis'));
    }
    public function update(Request $request, BarangKeluar $barangKeluar){
        $v=$request->validate(['barang_id'=>'required|exists:barangs,id','divisi_id'=>'nullable|exists:divisis,id','jumlah'=>'required|integer|min:1','tanggal_keluar'=>'required|date','penerima'=>'nullable|string|max:100','keterangan'=>'nullable|string']);
        $barangKeluar->delete();
        $barang=Barang::findOrFail($v['barang_id']);
        if(!$this->stockService->cukupUntuk($barang,$v['jumlah']))
            return back()->withErrors(['jumlah'=>'Stok tidak mencukupi.'])->withInput();
        $v['user_id']=Auth::id();
        BarangKeluar::create($v);
        return redirect()->route('barang-keluar.index')->with('success','Data barang keluar diperbarui.');
    }
    public function destroy(BarangKeluar $barangKeluar){
        $barangKeluar->delete();
        return redirect()->route('barang-keluar.index')->with('success','Data barang keluar dihapus.');
    }
}
