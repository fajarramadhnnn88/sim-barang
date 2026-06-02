<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model {
    protected $table = 'barang_masuks';
    protected $fillable = ['no_transaksi','barang_id','supplier_id','user_id','jumlah','harga_satuan','total_harga','tanggal_masuk','no_surat_jalan','no_po','keterangan'];
    protected $casts = ['tanggal_masuk'=>'date','harga_satuan'=>'decimal:2','total_harga'=>'decimal:2'];

    protected static function booted(): void {
        static::creating(function(self $m) {
            if (!$m->no_transaksi) {
                $y=now()->format('Y'); $mo=now()->format('m');
                $last=self::whereYear('created_at',$y)->whereMonth('created_at',$mo)->max('no_transaksi');
                $seq=$last?(int)substr($last,-4)+1:1;
                $m->no_transaksi=sprintf('BM-%s%s-%04d',$y,$mo,$seq);
            }
            $m->total_harga = $m->jumlah * $m->harga_satuan;
        });
        static::created(function(self $m) {
            $s=StockBalance::firstOrCreate(['barang_id'=>$m->barang_id],['stok_masuk'=>0,'stok_keluar'=>0,'stok_tersedia'=>0]);
            $s->stok_masuk += $m->jumlah;
            $s->stok_tersedia = $s->stok_masuk - $s->stok_keluar;
            $s->last_updated = now(); $s->save();
        });
        static::deleted(function(self $m) {
            $s=StockBalance::where('barang_id',$m->barang_id)->first();
            if($s){$s->stok_masuk-=$m->jumlah;$s->stok_tersedia=$s->stok_masuk-$s->stok_keluar;$s->save();}
        });
    }

    public function barang() { return $this->belongsTo(Barang::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function getTotalHargaFormatAttribute(): string { return 'Rp '.number_format($this->total_harga,0,',','.'); }
    public function scopePeriode($q,$dari,$sampai) { return $q->whereBetween('tanggal_masuk',[$dari,$sampai]); }
}
