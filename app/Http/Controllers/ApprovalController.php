<?php
namespace App\Http\Controllers;

use App\Models\DetailPengajuan;
use App\Models\Pengajuan;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function __construct(private ApprovalService $approvalService) {}

    public function showApproval(Pengajuan $pengajuan)
    {
        $pengajuan->load(['user','divisi','details.barang','approvalLogs.user']);
        return view('pengajuan.approval', compact('pengajuan'));
    }

    public function review(Request $request, Pengajuan $pengajuan)
    {
        try { $this->approvalService->review($pengajuan, Auth::user(), $request->catatan ?? ''); }
        catch (\Exception $e) { return back()->with('error', $e->getMessage()); }
        return back()->with('success', 'Pengajuan berhasil direview.');
    }

    public function teruskan(Request $request, Pengajuan $pengajuan)
    {
        try { $this->approvalService->teruskanKePurchasing($pengajuan, Auth::user(), $request->catatan ?? ''); }
        catch (\Exception $e) { return back()->with('error', $e->getMessage()); }
        return back()->with('success', 'Pengajuan diteruskan ke Purchasing.');
    }

    public function proses(Request $request, Pengajuan $pengajuan)
    {
        try { $this->approvalService->prosesPurchasing($pengajuan, Auth::user(), $request->catatan ?? ''); }
        catch (\Exception $e) { return back()->with('error', $e->getMessage()); }
        return back()->with('success', 'Pengajuan sedang diproses.');
    }

    public function ajukanApproval(Request $request, Pengajuan $pengajuan)
    {
        try { $this->approvalService->ajukanApprovalAkhir($pengajuan, Auth::user(), $request->catatan ?? ''); }
        catch (\Exception $e) { return back()->with('error', $e->getMessage()); }
        return back()->with('success', 'Pengajuan diteruskan untuk approval akhir.');
    }

    public function setujui(Request $request, Pengajuan $pengajuan)
    {
        // Simpan jumlah disetujui per item
        $request->validate([
            'jumlah_disetujui'   => 'nullable|array',
            'jumlah_disetujui.*' => 'nullable|integer|min:0',
        ]);

        if ($request->has('jumlah_disetujui')) {
            foreach ($request->jumlah_disetujui as $detailId => $jumlah) {
                $detail = DetailPengajuan::find($detailId);
                if ($detail && $detail->pengajuan_id === $pengajuan->id) {
                    $detail->update([
                        'jumlah_disetujui' => ($jumlah !== null && $jumlah !== '') ? (int)$jumlah : null,
                    ]);
                }
            }
            // Recalculate total
            $total = 0;
            foreach ($pengajuan->fresh()->details as $d) {
                $jml    = $d->jumlah_disetujui ?? $d->jumlah_diminta;
                $total += $jml * $d->harga_estimasi;
            }
            $pengajuan->update(['total_nilai' => $total]);
        }

        try { $this->approvalService->setujui($pengajuan, Auth::user(), $request->catatan ?? ''); }
        catch (\Exception $e) { return back()->with('error', $e->getMessage()); }

        return redirect()->route('pengajuan.show', $pengajuan)
            ->with('success', 'Pengajuan disetujui! Purchasing dapat memulai proses pembelian.');
    }

    /**
     * Purchasing: mulai proses pembelian (status → proses_pembelian)
     * Admin divisi otomatis dinotifikasi
     */
    public function mulaiPembelian(Request $request, Pengajuan $pengajuan)
    {
        try {
            $this->approvalService->mulaiPembelian($pengajuan, Auth::user(), $request->catatan ?? '');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('pengajuan.show', $pengajuan)
            ->with('success', 'Proses pembelian dimulai. Admin divisi telah dinotifikasi. Silakan input barang masuk setelah barang tiba.');
    }

    /**
     * Admin divisi: konfirmasi barang sudah diterima (status → diterima)
     */
    public function konfirmasiTerima(Request $request, Pengajuan $pengajuan)
    {
        $request->validate(['catatan' => 'nullable|string|max:500']);

        try {
            $this->approvalService->konfirmasiTerima($pengajuan, Auth::user(), $request->catatan ?? '');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('pengajuan.show', $pengajuan)
            ->with('success', '✅ Penerimaan barang dikonfirmasi. Pengajuan selesai!');
    }

    public function tolak(Request $request, Pengajuan $pengajuan)
    {
        $request->validate(['alasan' => 'required|string|min:5']);
        try { $this->approvalService->tolak($pengajuan, Auth::user(), $request->alasan); }
        catch (\Exception $e) { return back()->with('error', $e->getMessage()); }
        return redirect()->route('pengajuan.show', $pengajuan)->with('success', 'Pengajuan ditolak.');
    }

    public function selesai(Request $request, Pengajuan $pengajuan)
    {
        try { $this->approvalService->selesai($pengajuan, Auth::user(), $request->catatan ?? ''); }
        catch (\Exception $e) { return back()->with('error', $e->getMessage()); }
        return back()->with('success', 'Pengajuan diselesaikan.');
    }
}