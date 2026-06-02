<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 50)->unique();
            $table->string('nama_barang', 150);
            $table->foreignId('kategori_id')->constrained('kategoris')->restrictOnDelete();
            $table->string('satuan', 30);
            $table->string('merk', 100)->nullable();
            $table->string('spesifikasi')->nullable();
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->integer('stok_minimum')->default(0);
            $table->string('foto')->nullable();
            $table->string('lokasi_penyimpanan', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('barangs'); }
};
