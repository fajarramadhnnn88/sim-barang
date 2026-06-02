<?php
namespace Database\Seeders;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder {
    public function run(): void {
        $data = [
            ['nama_supplier'=>'PT. Sumber Makmur','kode_supplier'=>'SUP001','kontak_person'=>'Pak Joko','telepon'=>'021-1234567','email'=>'info@sumbermakmur.id','alamat'=>'Jl. Sudirman No.1, Jakarta'],
            ['nama_supplier'=>'CV. Berkah Jaya','kode_supplier'=>'SUP002','kontak_person'=>'Bu Ani','telepon'=>'021-7654321','email'=>'order@berkahjaya.id','alamat'=>'Jl. Thamrin No.5, Jakarta'],
            ['nama_supplier'=>'UD. Maju Bersama','kode_supplier'=>'SUP003','kontak_person'=>'Pak Bejo','telepon'=>'022-1122334','email'=>'sales@majubersama.id','alamat'=>'Jl. Asia Afrika, Bandung'],
        ];
        foreach ($data as $d) Supplier::create($d);
    }
}
