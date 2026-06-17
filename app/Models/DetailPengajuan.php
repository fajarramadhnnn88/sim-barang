<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetailPengajuan extends Model {
    protected $table = 'detail_pengajuans';
    protected $fillable = [
        'pengajuan_id','barang_id','nama_barang_custom','spesifikasi_custom',
        'is_custom','jumlah_diminta','jumlah_disetujui',
        'harga_estimasi','subtotal','keterangan',
    ];
    protected $casts = [
        'harga_estimasi' => 'decimal:2',
        'subtotal'       => 'decimal:2',
        'is_custom'      => 'boolean',
    ];

    public function pengajuan() { return $this->belongsTo(Pengajuan::class); }
    public function barang()    { return $this->belongsTo(Barang::class); }

    // Nama barang untuk ditampilkan
    public function getNamaBarangDisplayAttribute(): string {
        if ($this->is_custom) return $this->nama_barang_custom ?? '-';
        return $this->barang?->nama_barang ?? $this->nama_barang_custom ?? '-';
    }

    public function getSubtotalFormatAttribute(): string {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
    public function getHargaEstimasiFormatAttribute(): string {
        return 'Rp ' . number_format($this->harga_estimasi, 0, ',', '.');
    }
}