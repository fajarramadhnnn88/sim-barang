<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model {
    protected $table = 'barang_keluars';
    protected $fillable = ['no_transaksi','barang_id','divisi_id','user_id','pengajuan_id','jumlah','tanggal_keluar','penerima','keterangan'];
    protected $casts = ['tanggal_keluar'=>'date'];

    protected static function booted(): void {
        static::creating(function(self $m) {
            if (!$m->no_transaksi) {
                $y=now()->format('Y');$mo=now()->format('m');
                $last=self::whereYear('created_at',$y)->whereMonth('created_at',$mo)->max('no_transaksi');
                $seq=$last?(int)substr($last,-4)+1:1;
                $m->no_transaksi=sprintf('BK-%s%s-%04d',$y,$mo,$seq);
            }
        });
        static::created(function(self $m) {
            $s=StockBalance::where('barang_id',$m->barang_id)->first();
            if($s){$s->stok_keluar+=$m->jumlah;$s->stok_tersedia=$s->stok_masuk-$s->stok_keluar;$s->last_updated=now();$s->save();}
        });
        static::deleted(function(self $m) {
            $s=StockBalance::where('barang_id',$m->barang_id)->first();
            if($s){$s->stok_keluar-=$m->jumlah;$s->stok_tersedia=$s->stok_masuk-$s->stok_keluar;$s->save();}
        });
    }

    public function barang() { return $this->belongsTo(Barang::class); }
    public function divisi() { return $this->belongsTo(Divisi::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function pengajuan() { return $this->belongsTo(Pengajuan::class); }
    public function scopePeriode($q,$dari,$sampai) { return $q->whereBetween('tanggal_keluar',[$dari,$sampai]); }
}
