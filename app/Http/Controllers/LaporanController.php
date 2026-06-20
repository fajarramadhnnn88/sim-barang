<?php
namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Pengajuan;
use App\Models\StockBalance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    // ── BARANG MASUK ─────────────────────────────────────────────────────────
    public function barangMasuk(Request $request)
    {
        [$data, $total] = $this->queryBarangMasuk($request);
        return view('laporan.print.barang-masuk', compact('data', 'total'));
    }

    public function barangMasukPdf(Request $request)
    {
        [$data, $total] = $this->queryBarangMasuk($request);
        $pdf = Pdf::loadView('laporan.print.barang-masuk', compact('data', 'total'))
                  ->setPaper('a4', 'portrait');
        return $pdf->download('laporan-barang-masuk-' . now()->format('Ymd-His') . '.pdf');
    }

    // ── BARANG KELUAR ─────────────────────────────────────────────────────────
    public function barangKeluar(Request $request)
    {
        $data = $this->queryBarangKeluar($request);
        return view('laporan.print.barang-keluar', compact('data'));
    }

    public function barangKeluarPdf(Request $request)
    {
        $data = $this->queryBarangKeluar($request);
        $pdf = Pdf::loadView('laporan.print.barang-keluar', compact('data'))
                  ->setPaper('a4', 'portrait');
        return $pdf->download('laporan-barang-keluar-' . now()->format('Ymd-His') . '.pdf');
    }

    // ── PENGAJUAN ─────────────────────────────────────────────────────────────
    public function pengajuan(Request $request)
    {
        [$data, $totalNilai] = $this->queryPengajuan($request);
        return view('laporan.print.pengajuan', compact('data', 'totalNilai'));
    }

    public function pengajuanPdf(Request $request)
    {
        [$data, $totalNilai] = $this->queryPengajuan($request);
        $pdf = Pdf::loadView('laporan.print.pengajuan', compact('data', 'totalNilai'))
                  ->setPaper('a4', 'landscape');
        return $pdf->download('laporan-pengajuan-' . now()->format('Ymd-His') . '.pdf');
    }

    // ── STOCK ─────────────────────────────────────────────────────────────────
    public function stock(Request $request)
    {
        $data = $this->queryStock($request);
        return view('laporan.print.stock', compact('data'));
    }

    public function stockPdf(Request $request)
    {
        $data = $this->queryStock($request);
        $pdf = Pdf::loadView('laporan.print.stock', compact('data'))
                  ->setPaper('a4', 'portrait');
        return $pdf->download('laporan-stock-' . now()->format('Ymd-His') . '.pdf');
    }

    // ── Query Helpers (dipakai bersama oleh view & PDF) ───────────────────────

    private function queryBarangMasuk(Request $request): array
    {
        $data = BarangMasuk::with(['barang', 'supplier', 'user'])
            ->when($request->dari && $request->sampai, fn($q) => $q->periode($request->dari, $request->sampai))
            ->latest('tanggal_masuk')
            ->get();
        $total = $data->sum('total_harga');
        return [$data, $total];
    }

    private function queryBarangKeluar(Request $request)
    {
        return BarangKeluar::with(['barang', 'divisi', 'user'])
            ->when($request->dari && $request->sampai, fn($q) => $q->whereBetween('tanggal_keluar', [$request->dari, $request->sampai]))
            ->latest('tanggal_keluar')
            ->get();
    }

    private function queryPengajuan(Request $request): array
    {
        $data = Pengajuan::with(['user', 'divisi'])
            ->when($request->dari && $request->sampai, fn($q) => $q->periode($request->dari, $request->sampai))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest('tanggal_pengajuan')
            ->get();
        $totalNilai = $data->sum('total_nilai');
        return [$data, $totalNilai];
    }

    private function queryStock(Request $request)
    {
        return StockBalance::with('barang.kategori')
            ->when($request->kategori_id, fn($q) => $q->whereHas('barang', fn($q2) => $q2->where('kategori_id', $request->kategori_id)))
            ->get()
            ->sortBy(fn($s) => $s->barang->nama_barang ?? '');
    }
}