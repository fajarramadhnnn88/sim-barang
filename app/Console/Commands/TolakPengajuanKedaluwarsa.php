<?php
namespace App\Console\Commands;

use App\Models\Pengajuan;
use App\Services\ApprovalService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TolakPengajuanKedaluwarsa extends Command
{
    /**
     * Nama & signature command.
     * Bisa dijalankan manual: php artisan pengajuan:tolak-kedaluwarsa
     * Atau pakai --batas-hari=N untuk override batas waktu default (14 hari).
     */
    protected $signature = 'pengajuan:tolak-kedaluwarsa {--batas-hari=14}';

    protected $description = 'Tolak otomatis pengajuan yang belum di-ACC dalam batas waktu tertentu (default 14 hari / 2 minggu). Pengajuan yang sudah masuk tahap pembelian tidak akan ditolak.';

    public function handle(ApprovalService $approvalService): int
    {
        $batasHari = (int) $this->option('batas-hari');
        $batasWaktu = now()->subDays($batasHari);

        // Hanya ambil pengajuan dengan status yang MASIH dalam tahap review
        // (sebelum disetujui) — status proses_pembelian/barang_masuk/diterima
        // TIDAK pernah masuk ke query ini sama sekali, jadi aman dari awal.
        $statusBolehTolak = ApprovalService::statusBolehTolakValues();

        $pengajuans = Pengajuan::whereIn('status', $statusBolehTolak)
            ->where(function ($q) use ($batasWaktu) {
                $q->where('tanggal_pengajuan', '<=', $batasWaktu->toDateString())
                  ->orWhere(function ($q2) use ($batasWaktu) {
                      // fallback kalau tanggal_pengajuan kosong, pakai created_at
                      $q2->whereNull('tanggal_pengajuan')
                         ->where('created_at', '<=', $batasWaktu);
                  });
            })
            ->get();

        if ($pengajuans->isEmpty()) {
            $this->info('Tidak ada pengajuan yang kedaluwarsa. Tidak ada yang ditolak.');
            return self::SUCCESS;
        }

        $berhasil = 0;
        $gagal    = 0;

        foreach ($pengajuans as $p) {
            try {
                $approvalService->tolakOtomatis($p);
                $berhasil++;
                $this->line("✓ Ditolak otomatis: {$p->no_pengajuan} ({$p->keperluan}) — sudah {$batasHari} hari tanpa ACC.");
            } catch (\Exception $e) {
                $gagal++;
                Log::warning("Gagal auto-tolak pengajuan {$p->no_pengajuan}: " . $e->getMessage());
                $this->warn("✗ Dilewati: {$p->no_pengajuan} — {$e->getMessage()}");
            }
        }

        $this->info("Selesai. {$berhasil} pengajuan ditolak otomatis, {$gagal} dilewati.");
        return self::SUCCESS;
    }
}