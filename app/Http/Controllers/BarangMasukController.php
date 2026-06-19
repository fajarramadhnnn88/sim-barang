<?php
namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Pengajuan;
use App\Models\Supplier;
use App\Enums\StatusPengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BarangMasukController extends Controller
{
    public function index(Request $request)
    {
        $items = BarangMasuk::with(['barang.kategori','supplier','user','pengajuan'])
            ->when($request->search, fn($q) => $q
                ->where('no_transaksi','like',"%{$request->search}%")
                ->orWhereHas('barang', fn($q2) => $q2->where('nama_barang','like',"%{$request->search}%"))
            )
            ->when($request->dari && $request->sampai, fn($q) => $q->periode($request->dari, $request->sampai))
            ->latest()->paginate(15)->withQueryString();

        return view('barang-masuk.index', compact('items'));
    }

    public function create(Request $request)
    {
        $barangs   = Barang::with('stockBalance')->active()->orderBy('nama_barang')->get();
        $suppliers = Supplier::active()->orderBy('nama_supplier')->get();

        // Kalau dari halaman pengajuan (tombol "Input Barang Masuk")
        $pengajuan = null;
        if ($request->pengajuan_id) {
            $pengajuan = Pengajuan::with('details.barang')->find($request->pengajuan_id);
        }

        // Semua pengajuan berstatus proses_pembelian untuk dropdown
        $pengajuanProses = Pengajuan::where('status', StatusPengajuan::ProsesPembelian)
            ->orderBy('no_pengajuan')->get();

        return view('barang-masuk.create', compact('barangs','suppliers','pengajuan','pengajuanProses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id'        => 'required|exists:barangs,id',
            'supplier_id'      => 'nullable|exists:suppliers,id',
            'pengajuan_id'     => 'nullable|exists:pengajuans,id',
            'jumlah'           => 'required|integer|min:1',
            'harga_satuan'     => 'required|numeric|min:0',
            'tanggal_masuk'    => 'required|date',
            'no_surat_jalan'   => 'nullable|string|max:100',
            'no_po'            => 'nullable|string|max:100',
            'pic_name'         => 'nullable|string|max:100',
            'foto_dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'keterangan'       => 'nullable|string',
        ]);

        $data = $request->except('foto_dokumentasi');
        $data['user_id'] = Auth::id();

        if ($request->hasFile('foto_dokumentasi')) {
            $data['foto_dokumentasi'] = $request->file('foto_dokumentasi')
                ->store('barang-masuk/dokumentasi', 'public');
        }

        BarangMasuk::create($data);
        // Status pengajuan diupdate otomatis di BarangMasuk::booted() created event

        return redirect()->route('barang-masuk.index')
            ->with('success', 'Barang masuk berhasil dicatat. Stok otomatis bertambah.');
    }

    public function show(BarangMasuk $barangMasuk)
    {
        $barangMasuk->load(['barang','supplier','user','pengajuan']);
        return view('barang-masuk.show', compact('barangMasuk'));
    }

    public function edit(BarangMasuk $barangMasuk)
    {
        $barangs         = Barang::with('stockBalance')->active()->orderBy('nama_barang')->get();
        $suppliers       = Supplier::active()->orderBy('nama_supplier')->get();
        $pengajuanProses = Pengajuan::whereIn('status',[
            StatusPengajuan::ProsesPembelian->value,
            StatusPengajuan::BarangMasuk->value,
        ])->orderBy('no_pengajuan')->get();

        return view('barang-masuk.edit', compact('barangMasuk','barangs','suppliers','pengajuanProses'));
    }

    public function update(Request $request, BarangMasuk $barangMasuk)
    {
        $request->validate([
            'barang_id'        => 'required|exists:barangs,id',
            'supplier_id'      => 'nullable|exists:suppliers,id',
            'pengajuan_id'     => 'nullable|exists:pengajuans,id',
            'jumlah'           => 'required|integer|min:1',
            'harga_satuan'     => 'required|numeric|min:0',
            'tanggal_masuk'    => 'required|date',
            'no_surat_jalan'   => 'nullable|string|max:100',
            'no_po'            => 'nullable|string|max:100',
            'pic_name'         => 'nullable|string|max:100',
            'foto_dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'keterangan'       => 'nullable|string',
        ]);

        // Hapus transaksi lama (stok otomatis dikurangi di deleted event)
        $barangMasuk->delete();

        $data = $request->except('foto_dokumentasi');
        $data['user_id'] = Auth::id();

        if ($request->hasFile('foto_dokumentasi')) {
            if ($barangMasuk->foto_dokumentasi) {
                Storage::disk('public')->delete($barangMasuk->foto_dokumentasi);
            }
            $data['foto_dokumentasi'] = $request->file('foto_dokumentasi')
                ->store('barang-masuk/dokumentasi', 'public');
        } else {
            $data['foto_dokumentasi'] = $barangMasuk->foto_dokumentasi;
        }

        BarangMasuk::create($data);

        return redirect()->route('barang-masuk.index')
            ->with('success', 'Data barang masuk berhasil diperbarui.');
    }

    public function destroy(BarangMasuk $barangMasuk)
    {
        if ($barangMasuk->foto_dokumentasi) {
            Storage::disk('public')->delete($barangMasuk->foto_dokumentasi);
        }
        $barangMasuk->delete();
        return redirect()->route('barang-masuk.index')
            ->with('success', 'Data barang masuk berhasil dihapus. Stok otomatis dikoreksi.');
    }
}