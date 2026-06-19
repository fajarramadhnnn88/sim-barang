<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BarangMasuk extends Model {
    protected $table = 'barang_masuks';
    protected $fillable = [
        'no_transaksi','barang_id','supplier_id','user_id','pengajuan_id',
        'jumlah','harga_satuan','total_harga',
        'tanggal_masuk','no_surat_jalan','no_po',
        'pic_name','foto_dokumentasi','keterangan',
    ];
    protected $casts = [
        'tanggal_masuk' => 'date',
        'harga_satuan'  => 'decimal:2',
        'total_harga'   => 'decimal:2',
    ];

    protected static function booted(): void {
        static::creating(function (self $m) {
            if (!$m->no_transaksi) {
                $y  = now()->format('Y');
                $mo = now()->format('m');
                $last = self::whereYear('created_at',$y)->whereMonth('created_at',$mo)->max('no_transaksi');
                $seq  = $last ? (int)substr($last,-4)+1 : 1;
                $m->no_transaksi = sprintf('BM-%s%s-%04d',$y,$mo,$seq);
            }
            $m->total_harga = $m->jumlah * $m->harga_satuan;
        });

        static::created(function (self $m) {
            // Update stock
            $s = StockBalance::firstOrCreate(
                ['barang_id' => $m->barang_id],
                ['stok_masuk'=>0,'stok_keluar'=>0,'stok_tersedia'=>0]
            );
            $s->stok_masuk   += $m->jumlah;
            $s->stok_tersedia = $s->stok_masuk - $s->stok_keluar;
            $s->last_updated  = now();
            $s->save();

            // Kalau terhubung ke pengajuan yang sedang proses_pembelian
            // → update status ke barang_masuk & notif admin divisi
            if ($m->pengajuan_id) {
                $p = Pengajuan::find($m->pengajuan_id);
                if ($p && $p->status->value === 'proses_pembelian') {
                    $p->update(['status' => 'barang_masuk']);

                    ApprovalLog::create([
                        'pengajuan_id'   => $p->id,
                        'user_id'        => $m->user_id,
                        'status_sebelum' => 'proses_pembelian',
                        'status_sesudah' => 'barang_masuk',
                        'aksi'           => 'catat_masuk',
                        'catatan'        => "Barang dicatat masuk oleh Purchasing. No. Transaksi: {$m->no_transaksi}. PIC: {$m->pic_name}",
                        'nilai_saat_ini' => $p->total_nilai,
                    ]);

                    // Notif semua admin divisi terkait
                    $admins = User::where('role','admin_divisi')
                                  ->where('divisi_id', $p->divisi_id)->get();
                    foreach ($admins as $admin) {
                        $admin->notify(new \App\Notifications\BarangSudahMasuk($p));
                    }
                }
            }
        });

        static::deleted(function (self $m) {
            $s = StockBalance::where('barang_id',$m->barang_id)->first();
            if ($s) {
                $s->stok_masuk   -= $m->jumlah;
                $s->stok_tersedia = $s->stok_masuk - $s->stok_keluar;
                $s->save();
            }
        });
    }

    public function barang()    { return $this->belongsTo(Barang::class); }
    public function supplier()  { return $this->belongsTo(Supplier::class); }
    public function user()      { return $this->belongsTo(User::class); }
    public function pengajuan() { return $this->belongsTo(Pengajuan::class); }

    public function getFotoDokumentasiUrlAttribute(): ?string {
        return $this->foto_dokumentasi ? asset('storage/'.$this->foto_dokumentasi) : null;
    }
    public function getTotalHargaFormatAttribute(): string {
        return 'Rp '.number_format($this->total_harga,0,',','.');
    }
    public function scopePeriode($q, $dari, $sampai) {
        return $q->whereBetween('tanggal_masuk',[$dari,$sampai]);
    }
}