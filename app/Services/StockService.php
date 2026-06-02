<?php
namespace App\Services;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\StockBalance;
use Illuminate\Support\Collection;

class StockService {
    public function rekalkuasi(Barang $barang): StockBalance {
        $masuk  = BarangMasuk::where('barang_id',$barang->id)->sum('jumlah');
        $keluar = BarangKeluar::where('barang_id',$barang->id)->sum('jumlah');
        return StockBalance::updateOrCreate(['barang_id'=>$barang->id],[
            'stok_masuk'=>$masuk,'stok_keluar'=>$keluar,'stok_tersedia'=>$masuk-$keluar,'last_updated'=>now()
        ]);
    }
    public function rekalkuasiSemua():void { Barang::all()->each(fn($b)=>$this->rekalkuasi($b)); }
    public function getAlertStok():Collection {
        return Barang::with(['stockBalance','kategori'])->whereHas('stockBalance',fn($q)=>$q->whereRaw('stok_tersedia <= barangs.stok_minimum'))->active()->get();
    }
    public function cukupUntuk(Barang $barang, int $jumlah):bool {
        return ($barang->stockBalance?->stok_tersedia??0)>=$jumlah;
    }
    public function getSummary():array {
        return [
            'total_jenis_barang'=>Barang::active()->count(),
            'stok_menipis'=>StockBalance::stokMenipis()->count(),
            'stok_habis'=>StockBalance::stokHabis()->count(),
            'total_masuk_bulan'=>BarangMasuk::whereMonth('tanggal_masuk',now()->month)->whereYear('tanggal_masuk',now()->year)->sum('jumlah'),
            'total_keluar_bulan'=>BarangKeluar::whereMonth('tanggal_keluar',now()->month)->whereYear('tanggal_keluar',now()->year)->sum('jumlah'),
        ];
    }
}
