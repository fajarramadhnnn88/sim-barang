<?php
namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Kategori;
use App\Models\StockBalance;
use Illuminate\Http\Request;

class StockBalanceController extends Controller
{
    public function index(Request $request)
    {
        $stockBalances = StockBalance::with(['barang.kategori'])
            // PENTING: whereHas('barang') memastikan hanya baris stock_balance
            // yang barangnya MASIH ADA (belum dihapus) yang ditampilkan.
            // Ini mencegah error "Attempt to read property on null" kalau
            // ada data stock_balance yatim (barang induknya sudah terhapus).
            ->whereHas('barang')
            ->when($request->search, fn($q) => $q->whereHas('barang', fn($q2) =>
                $q2->where('nama_barang', 'like', "%{$request->search}%")
                   ->orWhere('kode_barang', 'like', "%{$request->search}%")
            ))
            ->when($request->kategori_id, fn($q) => $q->whereHas('barang', fn($q2) =>
                $q2->where('kategori_id', $request->kategori_id)
            ))
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        $kategoris = Kategori::orderBy('nama_kategori')->get();

        // Alert barang yang stoknya menipis/habis — juga dijaga whereHas('stockBalance')
        $barangMenipis = Barang::whereHas('stockBalance', function ($q) {
                $q->whereColumn('stok_tersedia', '<=', 'barangs.stok_minimum');
            })
            ->where('is_active', true)
            ->with('stockBalance')
            ->get();

        return view('stock.index', compact('stockBalances', 'kategoris', 'barangMenipis'));
    }

    /**
     * Hitung ulang stock_balance satu barang berdasarkan data riil
     * dari barang_masuks dan barang_keluars (memperbaiki data yang
     * mungkin tidak sinkron).
     */
    public function rekalkuasi(Barang $barang)
    {
        $totalMasuk  = BarangMasuk::where('barang_id', $barang->id)->sum('jumlah');
        $totalKeluar = BarangKeluar::where('barang_id', $barang->id)->sum('jumlah');

        StockBalance::updateOrCreate(
            ['barang_id' => $barang->id],
            [
                'stok_masuk'    => $totalMasuk,
                'stok_keluar'   => $totalKeluar,
                'stok_tersedia' => $totalMasuk - $totalKeluar,
                'last_updated'  => now(),
            ]
        );

        return back()->with('success', "Stok '{$barang->nama_barang}' berhasil dihitung ulang.");
    }

    /**
     * Hitung ulang SEMUA stock_balance, sekaligus otomatis membuat
     * baris baru untuk barang yang belum punya stock_balance, dan
     * MENGHAPUS baris stock_balance yang barangnya sudah tidak ada
     * (membersihkan data yatim seperti penyebab error sebelumnya).
     */
    public function rekalkuasiSemua()
    {
        // Bersihkan data yatim — stock_balance yang barang induknya sudah terhapus
        StockBalance::whereDoesntHave('barang')->delete();

        $barangs = Barang::all();
        foreach ($barangs as $barang) {
            $totalMasuk  = BarangMasuk::where('barang_id', $barang->id)->sum('jumlah');
            $totalKeluar = BarangKeluar::where('barang_id', $barang->id)->sum('jumlah');

            StockBalance::updateOrCreate(
                ['barang_id' => $barang->id],
                [
                    'stok_masuk'    => $totalMasuk,
                    'stok_keluar'   => $totalKeluar,
                    'stok_tersedia' => $totalMasuk - $totalKeluar,
                    'last_updated'  => now(),
                ]
            );
        }

        return back()->with('success', 'Semua stok berhasil dihitung ulang dan data yatim dibersihkan.');
    }
}