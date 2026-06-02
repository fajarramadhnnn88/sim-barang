<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model {
    protected $fillable = ['nama_supplier','kode_supplier','kontak_person','telepon','email','alamat','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    public function barangMasuks() { return $this->hasMany(BarangMasuk::class); }
    public function scopeActive($q) { return $q->where('is_active',true); }
}
