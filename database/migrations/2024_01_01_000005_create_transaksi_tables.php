<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {

        Schema::create('barang_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi', 50)->unique();
            $table->foreignId('barang_id')->constrained('barangs')->restrictOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->unsignedBigInteger('pengajuan_id')->nullable(); // link ke pengajuan asal
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->date('tanggal_masuk');
            $table->string('no_surat_jalan', 100)->nullable();
            $table->string('no_po', 100)->nullable();
            $table->string('pic_name', 100)->nullable();        // Nama PIC yang menerima/melakukan pembelian
            $table->string('foto_dokumentasi')->nullable();     // Foto barang sebagai bukti dokumentasi
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('barang_keluars', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi', 50)->unique();
            $table->foreignId('barang_id')->constrained('barangs')->restrictOnDelete();
            $table->foreignId('divisi_id')->nullable()->constrained('divisis')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->unsignedBigInteger('pengajuan_id')->nullable();
            $table->integer('jumlah');
            $table->date('tanggal_keluar');
            $table->string('penerima', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->unique()->constrained('barangs')->cascadeOnDelete();
            $table->integer('stok_masuk')->default(0);
            $table->integer('stok_keluar')->default(0);
            $table->integer('stok_tersedia')->default(0);
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();
        });

        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('status_sebelum', 50)->nullable();
            $table->string('status_sesudah', 50);
            $table->enum('aksi', [
                'submit','review','teruskan','proses','setujui',
                'mulai_beli','catat_masuk','konfirmasi_terima',
                'tolak','revisi','selesai',
            ]);
            $table->text('catatan')->nullable();
            $table->decimal('nilai_saat_ini', 15, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('approval_logs');
        Schema::dropIfExists('stock_balances');
        Schema::dropIfExists('barang_keluars');
        Schema::dropIfExists('barang_masuks');
    }
};