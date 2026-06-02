<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model {
    protected $table = 'kategoris';
    protected $fillable = ['nama_kategori','kode_kategori','deskripsi'];
    public function barangs() { return $this->hasMany(Barang::class); }
}
