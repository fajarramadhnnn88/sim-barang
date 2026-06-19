<?php
namespace App\Notifications;
use App\Models\Pengajuan;
use Illuminate\Notifications\Notification;

class BarangSedangDibeli extends Notification {
    public function __construct(public Pengajuan $pengajuan) {}
    public function via(object $n): array { return ['database']; }
    public function toArray(object $n): array {
        return [
            'pengajuan_id' => $this->pengajuan->id,
            'no_pengajuan' => $this->pengajuan->no_pengajuan,
            'judul'        => '🛒 Barang Sedang Dibeli',
            'pesan'        => "Purchasing sedang melakukan pembelian untuk pengajuan {$this->pengajuan->no_pengajuan} — {$this->pengajuan->keperluan}.",
            'url'          => route('pengajuan.show', $this->pengajuan),
        ];
    }
}