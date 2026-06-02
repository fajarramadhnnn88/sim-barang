<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $this->call([
            DivisiSeeder::class,
            UserSeeder::class,
            KategoriSeeder::class,
            SupplierSeeder::class,
            BarangSeeder::class,
        ]);
    }
}
