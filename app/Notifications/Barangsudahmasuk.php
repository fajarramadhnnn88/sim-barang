<?php
namespace App\Notifications;
use App\Models\Pengajuan;
use Illuminate\Notifications\Notification;

class BarangSudahMasuk extends Notification {
    public function __construct(public Pengajuan $pengajuan) {}
    public function via(object $n): array { return ['database']; }
    public function toArray(object $n): array {
        return [
            'pengajuan_id' => $this->pengajuan->id,
            'no_pengajuan' => $this->pengajuan->no_pengajuan,
            'judul'        => '📦 Barang Sudah Masuk — Mohon Dikonfirmasi',
            'pesan'        => "Barang untuk pengajuan {$this->pengajuan->no_pengajuan} telah dicatat masuk oleh Purchasing. Silakan cek dan konfirmasi penerimaan barang.",
            'url'          => route('pengajuan.show', $this->pengajuan),
        ];
    }
}