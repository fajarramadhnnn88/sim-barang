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
        $pengajuan->load(['user', 'divisi', 'details.barang', 'approvalLogs.user']);
        return view('pengajuan.approval', compact('pengajuan'));
    }

    public function review(Request $request, Pengajuan $pengajuan)
    {
        try {
            $this->approvalService->review($pengajuan, Auth::user(), $request->catatan ?? '');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', 'Pengajuan berhasil direview.');
    }

    public function teruskan(Request $request, Pengajuan $pengajuan)
    {
        try {
            $this->approvalService->teruskanKePurchasing($pengajuan, Auth::user(), $request->catatan ?? '');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', 'Pengajuan diteruskan ke Purchasing.');
    }

    public function proses(Request $request, Pengajuan $pengajuan)
    {
        try {
            $this->approvalService->prosesPurchasing($pengajuan, Auth::user(), $request->catatan ?? '');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', 'Pengajuan sedang diproses.');
    }

    public function ajukanApproval(Request $request, Pengajuan $pengajuan)
    {
        try {
            $this->approvalService->ajukanApprovalAkhir($pengajuan, Auth::user(), $request->catatan ?? '');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', 'Pengajuan diteruskan untuk approval akhir.');
    }

    /**
     * Setujui pengajuan — Wadir/Direktur bisa atur jumlah disetujui per item
     */
    public function setujui(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'jumlah_disetujui'   => 'nullable|array',
            'jumlah_disetujui.*' => 'nullable|integer|min:0',
        ]);

        // Simpan jumlah_disetujui per detail item
        if ($request->has('jumlah_disetujui')) {
            foreach ($request->jumlah_disetujui as $detailId => $jumlah) {
                $detail = DetailPengajuan::find($detailId);
                if ($detail && $detail->pengajuan_id === $pengajuan->id) {
                    $detail->update([
                        'jumlah_disetujui' => $jumlah !== null && $jumlah !== '' ? (int)$jumlah : null,
                    ]);
                }
            }

            // Hitung ulang total nilai berdasarkan jumlah yang disetujui
            $totalBaru = 0;
            foreach ($pengajuan->fresh()->details as $d) {
                $jml = $d->jumlah_disetujui ?? $d->jumlah_diminta;
                $totalBaru += $jml * $d->harga_estimasi;
            }
            $pengajuan->update(['total_nilai' => $totalBaru]);
        }

        try {
            $this->approvalService->setujui($pengajuan, Auth::user(), $request->catatan ?? '');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('pengajuan.show', $pengajuan)
            ->with('success', 'Pengajuan disetujui.');
    }

    public function tolak(Request $request, Pengajuan $pengajuan)
    {
        $request->validate(['alasan' => 'required|string|min:5']);
        try {
            $this->approvalService->tolak($pengajuan, Auth::user(), $request->alasan);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('pengajuan.show', $pengajuan)
            ->with('success', 'Pengajuan ditolak.');
    }

    public function selesai(Request $request, Pengajuan $pengajuan)
    {
        try {
            $this->approvalService->selesai($pengajuan, Auth::user(), $request->catatan ?? '');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', 'Pengajuan ditandai selesai.');
    }
}