<?php
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Jadwal otomatis: tolak pengajuan yang sudah lebih dari 14 hari (2 minggu)
 * belum di-ACC. Dijalankan setiap hari jam 01:00 dini hari.
 *
 * PENTING: supaya jadwal ini benar-benar berjalan, scheduler Laravel harus
 * aktif. Untuk pengembangan lokal, jalankan manual atau pakai:
 *   php artisan schedule:work
 * Untuk production (server sungguhan), tambahkan satu baris cron job ini
 * di crontab server (jalan tiap menit, Laravel sendiri yang menentukan
 * kapan tugas terjadwal benar-benar dieksekusi):
 *   * * * * * cd /path-ke-project && php artisan schedule:run >> /dev/null 2>&1
 */
Schedule::command('pengajuan:tolak-kedaluwarsa')
    ->dailyAt('01:00')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Auto-tolak pengajuan kedaluwarsa berhasil dijalankan.');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Auto-tolak pengajuan kedaluwarsa GAGAL dijalankan.');
    });