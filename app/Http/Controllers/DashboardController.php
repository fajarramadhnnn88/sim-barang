<?php
namespace App\Http\Controllers;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Pengajuan;
use App\Services\PengajuanService;
use App\Services\StockService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller {
    public function __construct(private StockService $stockService, private PengajuanService $pengajuanService){}
    public function index(){
        $user=$auth=Auth::user();
        $stockSummary=$this->stockService->getSummary();
        $pengajuanSummary=$this->pengajuanService->getSummary($user);
        $alertStok=$this->stockService->getAlertStok()->take(5);
        $pengajuanTerbaru=Pengajuan::with(['user','divisi'])
            ->when($user->isStaff(),fn($q)=>$q->where('user_id',$user->id))
            ->when($user->isAdminDivisi(),fn($q)=>$q->where('divisi_id',$user->divisi_id))
            ->latest()->take(6)->get();
        $bulanLabels=collect(range(5,0))->map(fn($i)=>now()->subMonths($i)->translatedFormat('M Y'));
        $bulanMasuk=collect(range(5,0))->map(fn($i)=>BarangMasuk::whereYear('tanggal_masuk',now()->subMonths($i)->year)->whereMonth('tanggal_masuk',now()->subMonths($i)->month)->sum('jumlah'));
        $bulanKeluar=collect(range(5,0))->map(fn($i)=>BarangKeluar::whereYear('tanggal_keluar',now()->subMonths($i)->year)->whereMonth('tanggal_keluar',now()->subMonths($i)->month)->sum('jumlah'));
        return view('dashboard.index',compact('stockSummary','pengajuanSummary','alertStok','pengajuanTerbaru','bulanLabels','bulanMasuk','bulanKeluar'));
    }
}
