<?php
namespace Database\Seeders;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\StockBalance;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder {
    public function run(): void {
        $atk = Kategori::where('kode_kategori','ATK')->first();
        $pkp = Kategori::where('kode_kategori','PKP')->first();
        $pkn = Kategori::where('kode_kategori','PKN')->first();
        $bkb = Kategori::where('kode_kategori','BKB')->first();

        $barangs = [
            ['kode_barang'=>'BRG00001','nama_barang'=>'Kertas HVS A4 80gr','kategori_id'=>$atk->id,'satuan'=>'Rim','harga_satuan'=>55000,'stok_minimum'=>10],
            ['kode_barang'=>'BRG00002','nama_barang'=>'Pulpen Pilot G2','kategori_id'=>$atk->id,'satuan'=>'Box','harga_satuan'=>35000,'stok_minimum'=>5],
            ['kode_barang'=>'BRG00003','nama_barang'=>'Stabilo Boss','kategori_id'=>$atk->id,'satuan'=>'Pcs','harga_satuan'=>8000,'stok_minimum'=>10],
            ['kode_barang'=>'BRG00004','nama_barang'=>'Mouse Wireless','kategori_id'=>$pkp->id,'satuan'=>'Unit','harga_satuan'=>150000,'stok_minimum'=>3],
            ['kode_barang'=>'BRG00005','nama_barang'=>'Keyboard USB','kategori_id'=>$pkp->id,'satuan'=>'Unit','harga_satuan'=>175000,'stok_minimum'=>3],
            ['kode_barang'=>'BRG00006','nama_barang'=>'Flashdisk 32GB','kategori_id'=>$pkp->id,'satuan'=>'Pcs','harga_satuan'=>85000,'stok_minimum'=>5],
            ['kode_barang'=>'BRG00007','nama_barang'=>'Tinta Printer Canon','kategori_id'=>$pkp->id,'satuan'=>'Botol','harga_satuan'=>120000,'stok_minimum'=>5],
            ['kode_barang'=>'BRG00008','nama_barang'=>'Stapler Joyko','kategori_id'=>$pkn->id,'satuan'=>'Pcs','harga_satuan'=>45000,'stok_minimum'=>3],
            ['kode_barang'=>'BRG00009','nama_barang'=>'Sabun Cuci Tangan','kategori_id'=>$bkb->id,'satuan'=>'Botol','harga_satuan'=>25000,'stok_minimum'=>10],
            ['kode_barang'=>'BRG00010','nama_barang'=>'Tisu Kotak','kategori_id'=>$bkb->id,'satuan'=>'Box','harga_satuan'=>18000,'stok_minimum'=>10],
        ];

        foreach ($barangs as $b) {
            $barang = Barang::create($b);
            StockBalance::create(['barang_id'=>$barang->id,'stok_masuk'=>0,'stok_keluar'=>0,'stok_tersedia'=>0]);
        }
    }
}
