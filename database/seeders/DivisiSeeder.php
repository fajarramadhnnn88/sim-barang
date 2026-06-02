<?php
namespace Database\Seeders;
use App\Models\Divisi;
use Illuminate\Database\Seeder;

class DivisiSeeder extends Seeder {
    public function run(): void {
        $data = [
            ['nama_divisi'=>'Teknologi Informasi','kode_divisi'=>'TI','deskripsi'=>'Divisi IT'],
            ['nama_divisi'=>'Keuangan','kode_divisi'=>'KEU','deskripsi'=>'Divisi Keuangan'],
            ['nama_divisi'=>'Sumber Daya Manusia','kode_divisi'=>'SDM','deskripsi'=>'Divisi HRD'],
            ['nama_divisi'=>'Operasional','kode_divisi'=>'OPS','deskripsi'=>'Divisi Operasional'],
            ['nama_divisi'=>'Marketing','kode_divisi'=>'MKT','deskripsi'=>'Divisi Marketing'],
            ['nama_divisi'=>'Purchasing','kode_divisi'=>'PRC','deskripsi'=>'Divisi Pengadaan'],
        ];
        foreach ($data as $d) Divisi::create($d);
    }
}
