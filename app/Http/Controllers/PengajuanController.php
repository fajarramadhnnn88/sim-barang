<?php
namespace App\Http\Controllers;

use App\Enums\StatusPengajuan;
use App\Models\Barang;
use App\Models\DetailPengajuan;
use App\Models\Kategori;
use App\Models\Pengajuan;
use App\Models\StockBalance;
use App\Services\ApprovalService;
use App\Services\PengajuanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller {
    public function __construct(
        private PengajuanService $pengajuanService,
        private ApprovalService $approvalService
    ) {}

    public function index(Request $request) {
        $user = Auth::user();
        $pengajuans = Pengajuan::with(['user','divisi'])
            ->when($user->isStaff(), fn($q) => $q->where('user_id', $user->id))
            ->when($user->isAdminDivisi(), fn($q) => $q->where('divisi_id', $user->divisi_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where(fn($q2) =>
                $q2->where('no_pengajuan','like',"%{$request->search}%")
                   ->orWhere('keperluan','like',"%{$request->search}%")
            ))
            ->latest()->paginate(15)->withQueryString();
        $statusList = StatusPengajuan::cases();
        return view('pengajuan.index', compact('pengajuans','statusList'));
    }

    public function create() {
        $barangs = Barang::with('stockBalance')->active()->orderBy('nama_barang')->get();
        return view('pengajuan.create', compact('barangs'));
    }

    public function store(Request $request) {
        $request->validate([
            'keperluan'           => 'required|string|max:200',
            'keterangan'          => 'nullable|string',
            'tanggal_dibutuhkan'  => 'nullable|date',
            'details'             => 'required|array|min:1',
            'details.*.tipe'      => 'required|in:existing,custom',
            'details.*.barang_id' => 'nullable|exists:barangs,id',
            'details.*.nama_barang_custom' => 'nullable|string|max:200',
            'details.*.jumlah'    => 'required|integer|min:1',
            'details.*.harga_estimasi' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        if (!$user->divisi_id)
            return back()->with('error','Anda belum terdaftar di divisi. Hubungi admin.')->withInput();

        $pengajuan = DB::transaction(function () use ($request, $user) {
            $p = Pengajuan::create([
                'user_id'            => $user->id,
                'divisi_id'          => $user->divisi_id,
                'keperluan'          => $request->keperluan,
                'keterangan'         => $request->keterangan,
                'tanggal_dibutuhkan' => $request->tanggal_dibutuhkan,
                'status'             => 'draft',
            ]);

            $total = 0;
            foreach ($request->details as $item) {
                $isCustom = $item['tipe'] === 'custom';
                if (!$isCustom && empty($item['barang_id'])) continue;
                if ($isCustom && empty($item['nama_barang_custom'])) continue;

                $h   = (float)($item['harga_estimasi'] ?? 0);
                $j   = (int)$item['jumlah'];
                $sub = $j * $h;
                $total += $sub;

                DetailPengajuan::create([
                    'pengajuan_id'       => $p->id,
                    'barang_id'          => $isCustom ? null : $item['barang_id'],
                    'nama_barang_custom' => $isCustom ? $item['nama_barang_custom'] : null,
                    'spesifikasi_custom' => $item['spesifikasi_custom'] ?? null,
                    'is_custom'          => $isCustom,
                    'jumlah_diminta'     => $j,
                    'harga_estimasi'     => $h,
                    'subtotal'           => $sub,
                    'keterangan'         => $item['keterangan'] ?? null,
                ]);
            }
            $p->update(['total_nilai' => $total]);
            return $p;
        });

        if ($request->action === 'submit') {
            try {
                $this->approvalService->submit($pengajuan, $user);
                return redirect()->route('pengajuan.show', $pengajuan)
                    ->with('success', "Pengajuan {$pengajuan->no_pengajuan} berhasil diajukan.");
            } catch (\Exception $e) {
                return redirect()->route('pengajuan.show', $pengajuan)->with('error', $e->getMessage());
            }
        }
        return redirect()->route('pengajuan.show', $pengajuan)
            ->with('success', "Draft {$pengajuan->no_pengajuan} berhasil disimpan.");
    }

    public function show(Pengajuan $pengajuan) {
        $this->authorizeView($pengajuan);
        $pengajuan->load(['user','divisi','details.barang.kategori','approvalLogs.user']);
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        return view('pengajuan.show', compact('pengajuan','kategoris'));
    }

    public function edit(Pengajuan $pengajuan) {
        abort_unless($pengajuan->canBeEditedBy(Auth::user()), 403);
        $barangs = Barang::with('stockBalance')->active()->orderBy('nama_barang')->get();
        $pengajuan->load('details.barang');
        return view('pengajuan.edit', compact('pengajuan','barangs'));
    }

    public function update(Request $request, Pengajuan $pengajuan) {
        abort_unless($pengajuan->canBeEditedBy(Auth::user()), 403);
        $request->validate([
            'keperluan'    => 'required|string|max:200',
            'details'      => 'required|array|min:1',
            'details.*.tipe'   => 'required|in:existing,custom',
            'details.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $pengajuan) {
            $pengajuan->update($request->only(['keperluan','keterangan','tanggal_dibutuhkan']));
            $pengajuan->details()->delete();
            $total = 0;
            foreach ($request->details as $item) {
                $isCustom = $item['tipe'] === 'custom';
                if (!$isCustom && empty($item['barang_id'])) continue;
                if ($isCustom && empty($item['nama_barang_custom'])) continue;
                $h = (float)($item['harga_estimasi'] ?? 0);
                $j = (int)$item['jumlah'];
                $sub = $j * $h;
                $total += $sub;
                DetailPengajuan::create([
                    'pengajuan_id'       => $pengajuan->id,
                    'barang_id'          => $isCustom ? null : $item['barang_id'],
                    'nama_barang_custom' => $isCustom ? $item['nama_barang_custom'] : null,
                    'spesifikasi_custom' => $item['spesifikasi_custom'] ?? null,
                    'is_custom'          => $isCustom,
                    'jumlah_diminta'     => $j,
                    'harga_estimasi'     => $h,
                    'subtotal'           => $sub,
                ]);
            }
            $pengajuan->update(['total_nilai' => $total]);
        });

        return redirect()->route('pengajuan.show', $pengajuan)->with('success','Pengajuan berhasil diperbarui.');
    }

    public function destroy(Pengajuan $pengajuan) {
        abort_unless($pengajuan->canBeEditedBy(Auth::user()), 403);
        $pengajuan->delete();
        return redirect()->route('pengajuan.index')->with('success','Pengajuan berhasil dihapus.');
    }

    public function submit(Pengajuan $pengajuan) {
        abort_unless($pengajuan->canBeSubmittedBy(Auth::user()), 403);
        try {
            $this->approvalService->submit($pengajuan, Auth::user());
            return redirect()->route('pengajuan.show', $pengajuan)
                ->with('success','Pengajuan berhasil diajukan ke Admin Divisi.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Admin Divisi: tambah barang custom ke sistem
    public function tambahBarangDariPengajuan(Request $request, DetailPengajuan $detail) {
        abort_unless(auth()->user()->isAdminDivisi() || auth()->user()->isSuperadmin(), 403);

        $request->validate([
            'kode_barang'  => 'required|string|max:50|unique:barangs',
            'kategori_id'  => 'required|exists:kategoris,id',
            'satuan'       => 'required|string|max:30',
            'harga_satuan' => 'nullable|numeric|min:0',
        ]);

        $barang = Barang::create([
            'kode_barang'  => $request->kode_barang,
            'nama_barang'  => $detail->nama_barang_custom,
            'kategori_id'  => $request->kategori_id,
            'satuan'       => $request->satuan,
            'harga_satuan' => $request->harga_satuan ?? 0,
            'stok_minimum' => 0,
            'is_active'    => true,
        ]);

        StockBalance::create([
            'barang_id'    => $barang->id,
            'stok_masuk'   => 0,
            'stok_keluar'  => 0,
            'stok_tersedia'=> 0,
        ]);

        // Hubungkan detail pengajuan ke barang baru
        $detail->update([
            'barang_id' => $barang->id,
            'is_custom' => false,
        ]);

        return back()->with('success', "Barang '{$barang->nama_barang}' berhasil ditambahkan ke sistem!");
    }

    private function authorizeView(Pengajuan $p): void {
        $u = Auth::user();
        $ok = $u->isSuperadmin() || $u->isPurchasing() || $u->isWakilDirektur() || $u->isDirektur()
            || $p->user_id === $u->id
            || ($u->isAdminDivisi() && $p->divisi_id === $u->divisi_id);
        abort_unless($ok, 403);
    }
}