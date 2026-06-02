<?php
namespace App\Models;
use App\Enums\StatusPengajuan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengajuan extends Model {
    use HasFactory, SoftDeletes;
    protected $fillable=['no_pengajuan','user_id','divisi_id','keperluan','keterangan','total_nilai','status','jalur_approval','tanggal_pengajuan','tanggal_dibutuhkan','file_pendukung'];
    protected $casts=['status'=>StatusPengajuan::class,'total_nilai'=>'decimal:2','tanggal_pengajuan'=>'date','tanggal_dibutuhkan'=>'date'];
    const BATAS_WADIR = 10_000_000;

    protected static function booted():void {
        static::creating(function(self $m){
            if(!$m->no_pengajuan){
                $y=now()->format('Y');$mo=now()->format('m');
                $last=self::withTrashed()->whereYear('created_at',$y)->whereMonth('created_at',$mo)->max('no_pengajuan');
                $seq=$last?(int)substr($last,-4)+1:1;
                $m->no_pengajuan=sprintf('PJ-%s%s-%04d',$y,$mo,$seq);
            }
        });
    }

    public function user(){return $this->belongsTo(User::class);}
    public function divisi(){return $this->belongsTo(Divisi::class);}
    public function details(){return $this->hasMany(DetailPengajuan::class);}
    public function approvalLogs(){return $this->hasMany(ApprovalLog::class)->latest();}
    public function barangKeluars(){return $this->hasMany(BarangKeluar::class);}

    public function hitungTotalNilai():void{$this->update(['total_nilai'=>$this->details()->sum('subtotal')]);}
    public function tentukanJalurApproval():string{return $this->total_nilai<=self::BATAS_WADIR?'wakil_direktur':'direktur';}
    public function isDiatas10Juta():bool{return $this->total_nilai>self::BATAS_WADIR;}
    public function canBeEditedBy(User $u):bool{return $this->user_id===$u->id&&$this->status===StatusPengajuan::Draft;}
    public function canBeSubmittedBy(User $u):bool{return $this->canBeEditedBy($u)&&$this->details()->count()>0;}

    public function getTotalNilaiFormatAttribute():string{return 'Rp '.number_format($this->total_nilai,0,',','.');}
    public function getStatusLabelAttribute():string{return $this->status->label();}
    public function getStatusBadgeAttribute():string{return $this->status->badgeClass();}

    public function scopePeriode($q,$dari,$sampai){return $q->whereBetween('tanggal_pengajuan',[$dari,$sampai]);}
}
