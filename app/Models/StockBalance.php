<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StockBalance extends Model {
    protected $table='stock_balances';
    protected $fillable=['barang_id','stok_masuk','stok_keluar','stok_tersedia','last_updated'];
    protected $casts=['last_updated'=>'datetime'];
    public function barang(){return $this->belongsTo(Barang::class);}
    public function getStatusStokAttribute():string{
        if($this->stok_tersedia<=0)return'Habis';
        if($this->stok_tersedia<=$this->barang?->stok_minimum)return'Menipis';
        return'Tersedia';
    }
    public function getStatusBadgeAttribute():string{
        return match($this->status_stok){'Habis'=>'badge-danger','Menipis'=>'badge-warning',default=>'badge-success'};
    }
    public function scopeStokHabis($q){return $q->where('stok_tersedia','<=',0);}
    public function scopeStokMenipis($q){
        return $q->whereHas('barang',fn($b)=>$b->whereRaw('stock_balances.stok_tersedia <= barangs.stok_minimum')->whereRaw('stock_balances.stok_tersedia > 0'));
    }
}
