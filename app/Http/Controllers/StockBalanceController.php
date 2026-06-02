<?php
namespace App\Http\Controllers;
use App\Models\Barang;
use App\Models\StockBalance;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockBalanceController extends Controller {
    public function __construct(private StockService $stockService){}
    public function index(Request $request){
        $stocks=StockBalance::with(['barang.kategori'])
            ->when($request->search,fn($q)=>$q->whereHas('barang',fn($b)=>$b->where('nama_barang','like',"%{$request->search}%")->orWhere('kode_barang','like',"%{$request->search}%")))
            ->when($request->filter==='menipis',fn($q)=>$q->stokMenipis())
            ->when($request->filter==='habis',fn($q)=>$q->stokHabis())
            ->paginate(15)->withQueryString();
        $alertStok=$this->stockService->getAlertStok();
        return view('stock.index',compact('stocks','alertStok'));
    }
    public function rekalkuasi(Barang $barang){
        $this->stockService->rekalkuasi($barang);
        return back()->with('success',"Stok {$barang->nama_barang} berhasil direkalkuasi.");
    }
    public function rekalkuasiSemua(){
        $this->stockService->rekalkuasiSemua();
        return back()->with('success','Semua stok berhasil direkalkuasi.');
    }
}
