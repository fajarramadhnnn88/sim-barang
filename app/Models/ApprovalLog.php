<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ApprovalLog extends Model {
    protected $table='approval_logs';
    protected $fillable=['pengajuan_id','user_id','status_sebelum','status_sesudah','aksi','catatan','nilai_saat_ini'];
    protected $casts=['nilai_saat_ini'=>'decimal:2'];
    public function pengajuan(){return $this->belongsTo(Pengajuan::class);}
    public function user(){return $this->belongsTo(User::class);}
    public function getAksiLabelAttribute():string{
        return match($this->aksi){'submit'=>'Mengajukan','review'=>'Mereview','teruskan'=>'Meneruskan ke Purchasing','proses'=>'Memproses','setujui'=>'Menyetujui','tolak'=>'Menolak','revisi'=>'Meminta Revisi','selesai'=>'Menyelesaikan',default=>ucfirst($this->aksi)};
    }
    public function getAksiIconAttribute():string{
        return match($this->aksi){'submit'=>'bi-send','review'=>'bi-eye','teruskan'=>'bi-arrow-right-circle','proses'=>'bi-gear','setujui'=>'bi-check-circle-fill','tolak'=>'bi-x-circle-fill','selesai'=>'bi-flag-fill',default=>'bi-circle'};
    }
    public function getAksiColorAttribute():string{
        return match($this->aksi){'setujui'=>'text-success','tolak'=>'text-danger','selesai'=>'text-dark',default=>'text-primary'};
    }
}
