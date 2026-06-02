<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model {
    use HasFactory, SoftDeletes;
    protected $fillable = ['kode_barang','nama_barang','kategori_id','satuan','merk','spesifikasi','deskripsi','harga_satuan','stok_minimum','foto','lokasi_penyimpanan','is_active'];
    protected $casts = ['harga_satuan'=>'decimal:2','is_active'=>'boolean'];

    public function kategori() { return $this->belongsTo(Kategori::class); }
    public function barangMasuks() { return $this->hasMany(BarangMasuk::class); }
    public function barangKeluars() { return $this->hasMany(BarangKeluar::class); }
    public function stockBalance() { return $this->hasOne(StockBalance::class); }
    public function detailPengajuans() { return $this->hasMany(DetailPengajuan::class); }

    public function getFotoUrlAttribute(): string {
        return $this->foto ? asset('storage/'.$this->foto) : 'https://via.placeholder.com/200x200/F1F5F9/94A3B8?text='.urlencode($this->nama_barang);
    }
    public function getStokTersediaAttribute(): int { return $this->stockBalance?->stok_tersedia ?? 0; }
    public function getIsStokMinimumAttribute(): bool { return $this->stok_tersedia <= $this->stok_minimum; }
    public function getHargaFormatAttribute(): string { return 'Rp '.number_format($this->harga_satuan,0,',','.'); }

    public function scopeActive($q) { return $q->where('is_active',true); }
    public function scopeSearch($q, $keyword) {
        return $q->where(fn($q2) => $q2->where('nama_barang','like',"%{$keyword}%")->orWhere('kode_barang','like',"%{$keyword}%")->orWhere('merk','like',"%{$keyword}%"));
    }
}
