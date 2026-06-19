<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ApprovalLog extends Model {
    protected $table = 'approval_logs';
    protected $fillable = [
        'pengajuan_id','user_id','status_sebelum',
        'status_sesudah','aksi','catatan','nilai_saat_ini',
    ];
    protected $casts = ['nilai_saat_ini' => 'decimal:2'];

    public function pengajuan() { return $this->belongsTo(Pengajuan::class); }
    public function user()      { return $this->belongsTo(User::class); }

    public function getAksiLabelAttribute(): string {
        return match($this->aksi) {
            'submit'            => 'Mengajukan',
            'review'            => 'Mereview',
            'teruskan'          => 'Meneruskan ke Purchasing',
            'proses'            => 'Memproses',
            'setujui'           => 'Menyetujui',
            'mulai_beli'        => 'Memulai Proses Pembelian',
            'catat_masuk'       => 'Mencatat Barang Masuk',
            'konfirmasi_terima' => 'Mengkonfirmasi Penerimaan Barang',
            'tolak'             => 'Menolak',
            'revisi'            => 'Meminta Revisi',
            'selesai'           => 'Menyelesaikan',
            default             => ucfirst($this->aksi),
        };
    }

    public function getAksiColorAttribute(): string {
        return match($this->aksi) {
            'setujui','konfirmasi_terima' => 'text-success',
            'tolak'                       => 'text-danger',
            'mulai_beli'                  => 'text-primary',
            'catat_masuk'                 => 'text-info',
            'selesai'                     => 'text-dark',
            default                       => 'text-warning',
        };
    }
}