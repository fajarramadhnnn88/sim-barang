<?php
namespace App\Http\Controllers;
use App\Enums\StatusPengajuan;
use App\Models\Barang;
use App\Models\DetailPengajuan;
use App\Models\Pengajuan;
use App\Services\ApprovalService;
use App\Services\PengajuanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller {
    public function __construct(private PengajuanService $pengajuanService, private ApprovalService $approvalService){}

    public function index(Request $request){
        $user=Auth::user();
        $pengajuans=Pengajuan::with(['user','divisi'])
            ->when($user->isStaff(),fn($q)=>$q->where('user_id',$user->id))
            ->when($user->isAdminDivisi(),fn($q)=>$q->where('divisi_id',$user->divisi_id))
            ->when($request->status,fn($q)=>$q->where('status',$request->status))
            ->when($request->search,fn($q)=>$q->where(fn($q2)=>$q2->where('no_pengajuan','like',"%{$request->search}%")->orWhere('keperluan','like',"%{$request->search}%")))
            ->latest()->paginate(15)->withQueryString();
        $statusList=StatusPengajuan::cases();
        return view('pengajuan.index',compact('pengajuans','statusList'));
    }

    public function create(){
        $barangs=Barang::with('stockBalance')->active()->orderBy('nama_barang')->get();
        return view('pengajuan.create',compact('barangs'));
    }

    public function store(Request $request){
        $request->validate([
            'keperluan'=>'required|string|max:200',
            'keterangan'=>'nullable|string',
            'tanggal_dibutuhkan'=>'nullable|date',
            'details'=>'required|array|min:1',
            'details.*.barang_id'=>'required|exists:barangs,id',
            'details.*.jumlah'=>'required|integer|min:1',
            'details.*.harga_estimasi'=>'nullable|numeric|min:0',
        ]);
        $user=Auth::user();
        if(!$user->divisi_id) return back()->with('error','Anda belum terdaftar di divisi. Hubungi admin.')->withInput();

        $pengajuan=DB::transaction(function()use($request,$user){
            $p=Pengajuan::create(['user_id'=>$user->id,'divisi_id'=>$user->divisi_id,'keperluan'=>$request->keperluan,'keterangan'=>$request->keterangan,'tanggal_dibutuhkan'=>$request->tanggal_dibutuhkan,'status'=>'draft']);
            $total=0;
            foreach($request->details as $item){
                if(empty($item['barang_id']))continue;
                $h=(float)($item['harga_estimasi']??0);$j=(int)$item['jumlah'];$sub=$j*$h;$total+=$sub;
                DetailPengajuan::create(['pengajuan_id'=>$p->id,'barang_id'=>$item['barang_id'],'jumlah_diminta'=>$j,'harga_estimasi'=>$h,'subtotal'=>$sub,'keterangan'=>$item['keterangan']??null]);
            }
            $p->update(['total_nilai'=>$total]);
            return $p;
        });

        if($request->action==='submit'){
            try{
                $this->approvalService->submit($pengajuan,$user);
                return redirect()->route('pengajuan.show',$pengajuan)->with('success',"Pengajuan {$pengajuan->no_pengajuan} berhasil diajukan.");
            }catch(\Exception $e){
                return redirect()->route('pengajuan.show',$pengajuan)->with('error',$e->getMessage());
            }
        }
        return redirect()->route('pengajuan.show',$pengajuan)->with('success',"Draft {$pengajuan->no_pengajuan} berhasil disimpan.");
    }

    public function show(Pengajuan $pengajuan){
        $this->authorizeView($pengajuan);
        $pengajuan->load(['user','divisi','details.barang.kategori','approvalLogs.user']);
        return view('pengajuan.show',compact('pengajuan'));
    }

    public function edit(Pengajuan $pengajuan){
        abort_unless($pengajuan->canBeEditedBy(Auth::user()),403);
        $barangs=Barang::with('stockBalance')->active()->orderBy('nama_barang')->get();
        $pengajuan->load('details.barang');
        return view('pengajuan.edit',compact('pengajuan','barangs'));
    }

    public function update(Request $request, Pengajuan $pengajuan){
        abort_unless($pengajuan->canBeEditedBy(Auth::user()),403);
        $request->validate(['keperluan'=>'required|string|max:200','keterangan'=>'nullable|string','tanggal_dibutuhkan'=>'nullable|date','details'=>'required|array|min:1','details.*.barang_id'=>'required|exists:barangs,id','details.*.jumlah'=>'required|integer|min:1','details.*.harga_estimasi'=>'nullable|numeric|min:0']);
        DB::transaction(function()use($request,$pengajuan){
            $pengajuan->update($request->only(['keperluan','keterangan','tanggal_dibutuhkan']));
            $pengajuan->details()->delete();
            $total=0;
            foreach($request->details as $item){
                if(empty($item['barang_id']))continue;
                $h=(float)($item['harga_estimasi']??0);$j=(int)$item['jumlah'];$sub=$j*$h;$total+=$sub;
                DetailPengajuan::create(['pengajuan_id'=>$pengajuan->id,'barang_id'=>$item['barang_id'],'jumlah_diminta'=>$j,'harga_estimasi'=>$h,'subtotal'=>$sub]);
            }
            $pengajuan->update(['total_nilai'=>$total]);
        });
        return redirect()->route('pengajuan.show',$pengajuan)->with('success','Pengajuan berhasil diperbarui.');
    }

    public function destroy(Pengajuan $pengajuan){
        abort_unless($pengajuan->canBeEditedBy(Auth::user()),403);
        $pengajuan->delete();
        return redirect()->route('pengajuan.index')->with('success','Pengajuan berhasil dihapus.');
    }

    public function submit(Pengajuan $pengajuan){
        abort_unless($pengajuan->canBeSubmittedBy(Auth::user()),403);
        try{
            $this->approvalService->submit($pengajuan,Auth::user());
            return redirect()->route('pengajuan.show',$pengajuan)->with('success','Pengajuan berhasil diajukan ke Admin Divisi.');
        }catch(\Exception $e){return back()->with('error',$e->getMessage());}
    }

    private function authorizeView(Pengajuan $p):void{
        $u=Auth::user();
        $ok=$u->isSuperadmin()||$u->isPurchasing()||$u->isWakilDirektur()||$u->isDirektur()||$p->user_id===$u->id||($u->isAdminDivisi()&&$p->divisi_id===$u->divisi_id);
        abort_unless($ok,403);
    }
}
