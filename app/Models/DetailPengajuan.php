<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetailPengajuan extends Model {
    protected $table='detail_pengajuans';
    protected $fillable=['pengajuan_id','barang_id','jumlah_diminta','jumlah_disetujui','harga_estimasi','subtotal','keterangan'];
    protected $casts=['harga_estimasi'=>'decimal:2','subtotal'=>'decimal:2'];
    public function pengajuan(){return $this->belongsTo(Pengajuan::class);}
    public function barang(){return $this->belongsTo(Barang::class);}
    public function getSubtotalFormatAttribute():string{return'Rp '.number_format($this->subtotal,0,',','.');}
    public function getHargaEstimasiFormatAttribute():string{return'Rp '.number_format($this->harga_estimasi,0,',','.');}
}
