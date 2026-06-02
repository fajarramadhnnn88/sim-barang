<?php
namespace App\Http\Controllers;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Pengajuan;
use App\Models\StockBalance;
use Illuminate\Http\Request;

class LaporanController extends Controller {
    public function index(){return view('laporan.index');}
    public function barangMasuk(Request $request){
        $data=BarangMasuk::with(['barang.kategori','supplier','user'])
            ->when($request->dari&&$request->sampai,fn($q)=>$q->periode($request->dari,$request->sampai))
            ->when($request->barang_id,fn($q)=>$q->where('barang_id',$request->barang_id))
            ->latest('tanggal_masuk')->get();
        $total=$data->sum('total_harga');
        if($request->format==='print')return view('laporan.print.barang-masuk',compact('data','total','request'));
        return view('laporan.barang-masuk',compact('data','total'));
    }
    public function barangKeluar(Request $request){
        $data=BarangKeluar::with(['barang.kategori','divisi','user'])
            ->when($request->dari&&$request->sampai,fn($q)=>$q->periode($request->dari,$request->sampai))
            ->when($request->divisi_id,fn($q)=>$q->where('divisi_id',$request->divisi_id))
            ->latest('tanggal_keluar')->get();
        if($request->format==='print')return view('laporan.print.barang-keluar',compact('data','request'));
        return view('laporan.barang-keluar',compact('data'));
    }
    public function pengajuan(Request $request){
        $data=Pengajuan::with(['user','divisi'])
            ->when($request->dari&&$request->sampai,fn($q)=>$q->periode($request->dari,$request->sampai))
            ->when($request->status,fn($q)=>$q->where('status',$request->status))
            ->when($request->divisi_id,fn($q)=>$q->where('divisi_id',$request->divisi_id))
            ->latest()->get();
        $totalNilai=$data->sum('total_nilai');
        if($request->format==='print')return view('laporan.print.pengajuan',compact('data','totalNilai','request'));
        return view('laporan.pengajuan',compact('data','totalNilai'));
    }
    public function stock(Request $request){
        $data=StockBalance::with(['barang.kategori'])
            ->when($request->filter==='menipis',fn($q)=>$q->stokMenipis())
            ->when($request->filter==='habis',fn($q)=>$q->stokHabis())
            ->get();
        if($request->format==='print')return view('laporan.print.stock',compact('data','request'));
        return view('laporan.stock',compact('data'));
    }
}
