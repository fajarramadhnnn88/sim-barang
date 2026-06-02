<?php
namespace App\Services;
use App\Enums\StatusPengajuan;
use App\Models\ApprovalLog;
use App\Models\Pengajuan;
use App\Models\User;
use App\Notifications\PengajuanDisetujui;
use App\Notifications\PengajuanDitolak;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApprovalService {
    public function submit(Pengajuan $p, User $u):Pengajuan {
        $this->ensure($p,StatusPengajuan::Draft);
        if($p->details()->count()===0) throw ValidationException::withMessages(['detail'=>'Minimal satu barang harus ditambahkan.']);
        return DB::transaction(function()use($p,$u){
            $p->hitungTotalNilai();
            $this->log($p,$u,'submit',StatusPengajuan::Diajukan,'',['tanggal_pengajuan'=>now()->toDateString()]);
            return $p;
        });
    }
    public function review(Pengajuan $p,User $u,string $c=''):Pengajuan {
        $this->ensure($p,StatusPengajuan::Diajukan);
        $this->ensureAdminDivisi($p,$u);
        return DB::transaction(fn()=>tap($p,fn()=>$this->log($p,$u,'review',StatusPengajuan::ReviewAdmin,$c)));
    }
    public function teruskanKePurchasing(Pengajuan $p,User $u,string $c=''):Pengajuan {
        $this->ensure($p,StatusPengajuan::ReviewAdmin);
        $this->ensureAdminDivisi($p,$u);
        return DB::transaction(function()use($p,$u,$c){
            $this->log($p,$u,'teruskan',StatusPengajuan::Diteruskan,$c,['jalur_approval'=>$p->tentukanJalurApproval()]);
            return $p;
        });
    }
    public function prosesPurchasing(Pengajuan $p,User $u,string $c=''):Pengajuan {
        $this->ensure($p,StatusPengajuan::Diteruskan);
        return DB::transaction(fn()=>tap($p,fn()=>$this->log($p,$u,'proses',StatusPengajuan::ProsesPurchasing,$c)));
    }
    public function ajukanApprovalAkhir(Pengajuan $p,User $u,string $c=''):Pengajuan {
        $this->ensure($p,StatusPengajuan::ProsesPurchasing);
        return DB::transaction(fn()=>tap($p,fn()=>$this->log($p,$u,'proses',StatusPengajuan::MenungguApproval,$c)));
    }
    public function setujui(Pengajuan $p,User $u,string $c=''):Pengajuan {
        $this->ensure($p,StatusPengajuan::MenungguApproval);
        $this->ensureApprover($p,$u);
        return DB::transaction(function()use($p,$u,$c){
            $this->log($p,$u,'setujui',StatusPengajuan::Disetujui,$c);
            $p->user->notify(new PengajuanDisetujui($p));
            return $p;
        });
    }
    public function tolak(Pengajuan $p,User $u,string $alasan):Pengajuan {
        if(empty($alasan)) throw ValidationException::withMessages(['alasan'=>'Alasan penolakan wajib diisi.']);
        return DB::transaction(function()use($p,$u,$alasan){
            $this->log($p,$u,'tolak',StatusPengajuan::Ditolak,$alasan);
            $p->user->notify(new PengajuanDitolak($p,$alasan));
            return $p;
        });
    }
    public function selesai(Pengajuan $p,User $u,string $c=''):Pengajuan {
        $this->ensure($p,StatusPengajuan::Disetujui);
        return DB::transaction(fn()=>tap($p,fn()=>$this->log($p,$u,'selesai',StatusPengajuan::Selesai,$c)));
    }

    private function log(Pengajuan $p,User $u,string $aksi,StatusPengajuan $baru,string $c='',array $extra=[]):void {
        $lama=$p->status->value;
        $p->update(array_merge(['status'=>$baru],$extra));
        ApprovalLog::create(['pengajuan_id'=>$p->id,'user_id'=>$u->id,'status_sebelum'=>$lama,'status_sesudah'=>$baru->value,'aksi'=>$aksi,'catatan'=>$c,'nilai_saat_ini'=>$p->total_nilai]);
    }
    private function ensure(Pengajuan $p,StatusPengajuan $exp):void {
        if($p->status!==$exp) throw ValidationException::withMessages(['status'=>"Status tidak valid. Saat ini: {$p->status->label()}"]);
    }
    private function ensureAdminDivisi(Pengajuan $p,User $u):void {
        if($u->divisi_id!==$p->divisi_id) throw ValidationException::withMessages(['divisi'=>'Anda tidak berwenang atas pengajuan dari divisi lain.']);
    }
    private function ensureApprover(Pengajuan $p,User $u):void {
        $valid=match($p->jalur_approval){
            'wakil_direktur'=>$u->isWakilDirektur()||$u->isDirektur()||$u->isSuperadmin(),
            'direktur'=>$u->isDirektur()||$u->isSuperadmin(),
            default=>false,
        };
        if(!$valid) throw ValidationException::withMessages(['approver'=>'Anda tidak memiliki wewenang untuk menyetujui pengajuan ini.']);
    }
}
