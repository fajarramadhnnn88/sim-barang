<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {

        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            $table->string('no_pengajuan', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('divisi_id')->constrained('divisis')->restrictOnDelete();
            $table->string('keperluan', 200);
            $table->text('keterangan')->nullable();
            $table->decimal('total_nilai', 15, 2)->default(0);
            $table->enum('status', [
                'draft',
                'diajukan',
                'review_admin',
                'diteruskan',
                'proses_purchasing',
                'menunggu_approval',
                'disetujui',
                'proses_pembelian',  // Purchasing mulai beli → notif admin divisi
                'barang_masuk',      // Purchasing catat barang masuk → notif admin divisi
                'diterima',          // Admin divisi konfirmasi terima → selesai
                'ditolak',
                'selesai',
            ])->default('draft');
            $table->enum('jalur_approval', ['wakil_direktur', 'direktur'])->nullable();
            $table->date('tanggal_pengajuan')->nullable();
            $table->date('tanggal_dibutuhkan')->nullable();
            $table->string('file_pendukung')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('detail_pengajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuans')->cascadeOnDelete();
            $table->foreignId('barang_id')->nullable()->constrained('barangs')->nullOnDelete();
            $table->string('nama_barang_custom')->nullable();
            $table->string('spesifikasi_custom')->nullable();
            $table->boolean('is_custom')->default(false);
            $table->integer('jumlah_diminta');
            $table->integer('jumlah_disetujui')->nullable();
            $table->decimal('harga_estimasi', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('detail_pengajuans');
        Schema::dropIfExists('pengajuans');
    }
};