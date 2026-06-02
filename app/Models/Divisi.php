<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Divisi extends Model {
    protected $table = 'divisis';
    protected $fillable = ['nama_divisi','kode_divisi','deskripsi','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    public function users(): HasMany { return $this->hasMany(User::class); }
    public function pengajuans(): HasMany { return $this->hasMany(Pengajuan::class); }
    public function barangKeluars(): HasMany { return $this->hasMany(BarangKeluar::class); }
    public function scopeActive($q) { return $q->where('is_active',true); }
}
