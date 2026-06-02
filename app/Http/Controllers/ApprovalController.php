<?php
namespace App\Http\Controllers;
use App\Models\Pengajuan;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller {
    public function __construct(private ApprovalService $approvalService){}
    public function showApproval(Pengajuan $pengajuan){
        $pengajuan->load(['user','divisi','details.barang','approvalLogs.user']);
        return view('pengajuan.approval',compact('pengajuan'));
    }
    public function review(Request $request, Pengajuan $pengajuan){
        try{$this->approvalService->review($pengajuan,Auth::user(),$request->catatan??'');}
        catch(\Exception $e){return back()->with('error',$e->getMessage());}
        return back()->with('success','Pengajuan berhasil direview.');
    }
    public function teruskan(Request $request, Pengajuan $pengajuan){
        try{$this->approvalService->teruskanKePurchasing($pengajuan,Auth::user(),$request->catatan??'');}
        catch(\Exception $e){return back()->with('error',$e->getMessage());}
        return back()->with('success','Pengajuan diteruskan ke Purchasing.');
    }
    public function proses(Request $request, Pengajuan $pengajuan){
        try{$this->approvalService->prosesPurchasing($pengajuan,Auth::user(),$request->catatan??'');}
        catch(\Exception $e){return back()->with('error',$e->getMessage());}
        return back()->with('success','Pengajuan sedang diproses.');
    }
    public function ajukanApproval(Request $request, Pengajuan $pengajuan){
        try{$this->approvalService->ajukanApprovalAkhir($pengajuan,Auth::user(),$request->catatan??'');}
        catch(\Exception $e){return back()->with('error',$e->getMessage());}
        return back()->with('success','Pengajuan diteruskan untuk approval akhir.');
    }
    public function setujui(Request $request, Pengajuan $pengajuan){
        try{$this->approvalService->setujui($pengajuan,Auth::user(),$request->catatan??'');}
        catch(\Exception $e){return back()->with('error',$e->getMessage());}
        return redirect()->route('pengajuan.show',$pengajuan)->with('success','Pengajuan disetujui.');
    }
    public function tolak(Request $request, Pengajuan $pengajuan){
        $request->validate(['alasan'=>'required|string|min:5']);
        try{$this->approvalService->tolak($pengajuan,Auth::user(),$request->alasan);}
        catch(\Exception $e){return back()->with('error',$e->getMessage());}
        return redirect()->route('pengajuan.show',$pengajuan)->with('success','Pengajuan ditolak.');
    }
    public function selesai(Request $request, Pengajuan $pengajuan){
        try{$this->approvalService->selesai($pengajuan,Auth::user(),$request->catatan??'');}
        catch(\Exception $e){return back()->with('error',$e->getMessage());}
        return back()->with('success','Pengajuan ditandai selesai.');
    }
}
