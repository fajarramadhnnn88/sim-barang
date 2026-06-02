<?php
namespace Database\Seeders;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder {
    public function run(): void {
        $data = [
            ['nama_kategori'=>'Alat Tulis Kantor','kode_kategori'=>'ATK'],
            ['nama_kategori'=>'Peralatan Komputer','kode_kategori'=>'PKP'],
            ['nama_kategori'=>'Peralatan Kantor','kode_kategori'=>'PKN'],
            ['nama_kategori'=>'Bahan Kebersihan','kode_kategori'=>'BKB'],
            ['nama_kategori'=>'Perlengkapan Listrik','kode_kategori'=>'PLT'],
        ];
        foreach ($data as $d) Kategori::create($d);
    }
}
