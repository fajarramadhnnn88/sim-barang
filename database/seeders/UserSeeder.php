<?php
namespace Database\Seeders;
use App\Models\Divisi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run(): void {
        $ti  = Divisi::where('kode_divisi','TI')->first();
        $keu = Divisi::where('kode_divisi','KEU')->first();
        $sdm = Divisi::where('kode_divisi','SDM')->first();
        $prc = Divisi::where('kode_divisi','PRC')->first();

        $users = [
            ['name'=>'Super Administrator','nip'=>'SA001','email'=>'superadmin@simbarang.id','password'=>Hash::make('password'),'role'=>'superadmin','divisi_id'=>null],
            ['name'=>'Budi Santoso','nip'=>'DIR001','email'=>'direktur@simbarang.id','password'=>Hash::make('password'),'role'=>'direktur','divisi_id'=>null],
            ['name'=>'Siti Rahayu','nip'=>'WD001','email'=>'wadir@simbarang.id','password'=>Hash::make('password'),'role'=>'wakil_direktur','divisi_id'=>null],
            ['name'=>'Ahmad Purchasing','nip'=>'PRC001','email'=>'purchasing@simbarang.id','password'=>Hash::make('password'),'role'=>'purchasing','divisi_id'=>$prc?->id],
            ['name'=>'Rina Admin TI','nip'=>'TI001','email'=>'admin.ti@simbarang.id','password'=>Hash::make('password'),'role'=>'admin_divisi','divisi_id'=>$ti?->id],
            ['name'=>'Deni Admin KEU','nip'=>'KEU001','email'=>'admin.keu@simbarang.id','password'=>Hash::make('password'),'role'=>'admin_divisi','divisi_id'=>$keu?->id],
            ['name'=>'Andi Staff TI','nip'=>'TI002','email'=>'staff.ti@simbarang.id','password'=>Hash::make('password'),'role'=>'staff','divisi_id'=>$ti?->id],
            ['name'=>'Dewi Staff KEU','nip'=>'KEU002','email'=>'staff.keu@simbarang.id','password'=>Hash::make('password'),'role'=>'staff','divisi_id'=>$keu?->id],
            ['name'=>'Hendra Staff SDM','nip'=>'SDM001','email'=>'staff.sdm@simbarang.id','password'=>Hash::make('password'),'role'=>'staff','divisi_id'=>$sdm?->id],
        ];
        foreach ($users as $u) User::create($u);
    }
}
