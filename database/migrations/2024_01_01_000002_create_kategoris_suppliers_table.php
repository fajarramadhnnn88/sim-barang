<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori', 100);
            $table->string('kode_kategori', 20)->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('nama_supplier', 150);
            $table->string('kode_supplier', 20)->unique();
            $table->string('kontak_person', 100)->nullable();
            $table->string('telepon', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('kategoris');
    }
};
