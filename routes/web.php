<?php
use App\Http\Controllers\{
    ApprovalController, AuthController, BarangController,
    BarangKeluarController, BarangMasukController, DashboardController,
    DivisiController, KategoriController, LaporanController,
    PengajuanController, ProfileController, StockBalanceController,
    SupplierController, UserController
};
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/notifications/read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.read');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',         [ProfileController::class, 'edit'])->name('edit');
        Route::put('/',         [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    });

    // ════════════════════════════════════════════════════════════════════════
    // DATA BARANG (master)
    // PENTING: route literal '/barang/create' WAJIB didaftarkan SEBELUM
    // route wildcard '/barang/{barang}', kalau tidak Laravel akan mengira
    // "create" adalah ID barang dan mengembalikan 404.
    // ════════════════════════════════════════════════════════════════════════

    // 1) Literal routes dulu (create, store) — admin_divisi & superadmin saja
    Route::middleware('role:admin_divisi,superadmin')->group(function () {
        Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
        Route::post('/barang',       [BarangController::class, 'store'])->name('barang.store');
    });

    // 2) Baru index & show (wildcard) — admin_divisi + purchasing + superadmin (purchasing read-only)
    Route::middleware('role:admin_divisi,purchasing,superadmin')->group(function () {
        Route::get('/barang',          [BarangController::class, 'index'])->name('barang.index');
        Route::get('/barang/{barang}', [BarangController::class, 'show'])->name('barang.show');
    });

    // 3) edit/update/destroy — punya segmen tambahan ({barang}/edit) atau method
    //    berbeda (PUT/DELETE), jadi aman didaftarkan kapan saja
    Route::middleware('role:admin_divisi,superadmin')->group(function () {
        Route::get('/barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('/barang/{barang}',      [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/{barang}',   [BarangController::class, 'destroy'])->name('barang.destroy');
    });

    // ════════════════════════════════════════════════════════════════════════
    // BARANG MASUK — sama persis pola urutannya, literal sebelum wildcard
    // ════════════════════════════════════════════════════════════════════════

    // 1) Literal routes dulu (create, store) — purchasing & superadmin
    Route::middleware('role:purchasing,superadmin')->group(function () {
        Route::get('/barang-masuk/create', [BarangMasukController::class, 'create'])->name('barang-masuk.create');
        Route::post('/barang-masuk',       [BarangMasukController::class, 'store'])->name('barang-masuk.store');
    });

    // 2) index & show (wildcard) — admin_divisi (read-only) + purchasing + superadmin
    Route::middleware('role:admin_divisi,purchasing,superadmin')->group(function () {
        Route::get('/barang-masuk',               [BarangMasukController::class, 'index'])->name('barang-masuk.index');
        Route::get('/barang-masuk/{barangMasuk}', [BarangMasukController::class, 'show'])->name('barang-masuk.show');
    });

    // 3) edit/update/destroy — purchasing & superadmin
    Route::middleware('role:purchasing,superadmin')->group(function () {
        Route::get('/barang-masuk/{barangMasuk}/edit', [BarangMasukController::class, 'edit'])->name('barang-masuk.edit');
        Route::put('/barang-masuk/{barangMasuk}',      [BarangMasukController::class, 'update'])->name('barang-masuk.update');
        Route::delete('/barang-masuk/{barangMasuk}',   [BarangMasukController::class, 'destroy'])->name('barang-masuk.destroy');
    });

    // ════════════════════════════════════════════════════════════════════════
    // BARANG KELUAR — pakai Route::resource() jadi urutannya OTOMATIS BENAR
    // (Laravel resource() selalu mendaftarkan 'create' sebelum '{id}')
    // ════════════════════════════════════════════════════════════════════════
    Route::middleware('role:admin_divisi,superadmin')
         ->resource('barang-keluar', BarangKeluarController::class);

    // ── KATEGORI ─────────────────────────────────────────────────────────────
    Route::post('/kategori', [KategoriController::class, 'store'])
         ->name('kategori.store')
         ->middleware('role:admin_divisi,superadmin');
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/kategori/create',          [KategoriController::class, 'create'])->name('kategori.create');
        Route::get('/kategori',                 [KategoriController::class, 'index'])->name('kategori.index');
        Route::get('/kategori/{kategori}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
        Route::put('/kategori/{kategori}',      [KategoriController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/{kategori}',   [KategoriController::class, 'destroy'])->name('kategori.destroy');
    });

    // ── SUPPLIER ─────────────────────────────────────────────────────────────
    Route::post('/supplier', [SupplierController::class, 'store'])
         ->name('supplier.store')
         ->middleware('role:purchasing,superadmin');

    // ── STOCK BALANCE ─────────────────────────────────────────────────────────
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/', [StockBalanceController::class, 'index'])->name('index');
        Route::post('/rekalkuasi/{barang}', [StockBalanceController::class, 'rekalkuasi'])
             ->middleware('role:admin_divisi,purchasing,superadmin')->name('rekalkuasi');
        Route::post('/rekalkuasi-semua', [StockBalanceController::class, 'rekalkuasiSemua'])
             ->middleware('role:superadmin')->name('rekalkuasi.semua');
    });

    // ── PENGAJUAN ─────────────────────────────────────────────────────────────
    Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
        // literal '/buat' dulu sebelum wildcard '/{pengajuan}'
        Route::get('/buat', [PengajuanController::class, 'create'])
             ->middleware('role:staff,admin_divisi,superadmin')->name('create');
        Route::post('/', [PengajuanController::class, 'store'])
             ->middleware('role:staff,admin_divisi,superadmin')->name('store');
        Route::get('/', [PengajuanController::class, 'index'])->name('index');

        Route::get('/{pengajuan}', [PengajuanController::class, 'show'])->name('show');
        Route::get('/{pengajuan}/edit', [PengajuanController::class, 'edit'])
             ->middleware('role:staff,admin_divisi,superadmin')->name('edit');
        Route::put('/{pengajuan}', [PengajuanController::class, 'update'])
             ->middleware('role:staff,admin_divisi,superadmin')->name('update');
        Route::delete('/{pengajuan}', [PengajuanController::class, 'destroy'])->name('destroy');
        Route::post('/{pengajuan}/submit', [PengajuanController::class, 'submit'])->name('submit');
        Route::post('/detail/{detail}/tambah-barang',
            [PengajuanController::class, 'tambahBarangDariPengajuan'])
            ->middleware('role:admin_divisi,superadmin')
            ->name('detail.tambah-barang');
    });

    // ── APPROVAL ──────────────────────────────────────────────────────────────
    Route::prefix('approval')->name('approval.')->group(function () {
        Route::get('/{pengajuan}', [ApprovalController::class, 'showApproval'])
             ->middleware('role:wakil_direktur,direktur,superadmin')->name('show');
        Route::post('/{pengajuan}/review', [ApprovalController::class, 'review'])
             ->middleware('role:admin_divisi,superadmin')->name('review');
        Route::post('/{pengajuan}/teruskan', [ApprovalController::class, 'teruskan'])
             ->middleware('role:admin_divisi,superadmin')->name('teruskan');
        Route::post('/{pengajuan}/proses', [ApprovalController::class, 'proses'])
             ->middleware('role:purchasing,superadmin')->name('proses');
        Route::post('/{pengajuan}/ajukan-approval', [ApprovalController::class, 'ajukanApproval'])
             ->middleware('role:purchasing,superadmin')->name('ajukan');
        Route::post('/{pengajuan}/setujui', [ApprovalController::class, 'setujui'])
             ->middleware('role:wakil_direktur,direktur,superadmin')->name('setujui');
        Route::post('/{pengajuan}/mulai-pembelian', [ApprovalController::class, 'mulaiPembelian'])
             ->middleware('role:purchasing,superadmin')->name('mulai-pembelian');
        Route::post('/{pengajuan}/konfirmasi-terima', [ApprovalController::class, 'konfirmasiTerima'])
             ->middleware('role:admin_divisi,superadmin')->name('konfirmasi-terima');
        Route::post('/{pengajuan}/selesai', [ApprovalController::class, 'selesai'])
             ->middleware('role:purchasing,admin_divisi,superadmin')->name('selesai');
        Route::post('/{pengajuan}/tolak', [ApprovalController::class, 'tolak'])
             ->middleware('role:admin_divisi,purchasing,wakil_direktur,direktur,superadmin')->name('tolak');
    });

    // ── LAPORAN ───────────────────────────────────────────────────────────────
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/',              [LaporanController::class, 'index'])->name('index');
        Route::get('/barang-masuk',  [LaporanController::class, 'barangMasuk'])->name('barang-masuk');
        Route::get('/barang-keluar', [LaporanController::class, 'barangKeluar'])->name('barang-keluar');
        Route::get('/pengajuan',     [LaporanController::class, 'pengajuan'])->name('pengajuan');
        Route::get('/stock',         [LaporanController::class, 'stock'])->name('stock');
        Route::get('/barang-masuk/pdf',  [LaporanController::class, 'barangMasukPdf'])->name('barang-masuk.pdf');
        Route::get('/barang-keluar/pdf', [LaporanController::class, 'barangKeluarPdf'])->name('barang-keluar.pdf');
        Route::get('/pengajuan/pdf',     [LaporanController::class, 'pengajuanPdf'])->name('pengajuan.pdf');
        Route::get('/stock/pdf',         [LaporanController::class, 'stockPdf'])->name('stock.pdf');
    });

    // ── MASTER DATA ───────────────────────────────────────────────────────────
    Route::middleware('role:superadmin')->group(function () {
        Route::resource('divisi', DivisiController::class);
        Route::resource('users',  UserController::class);
    });
});