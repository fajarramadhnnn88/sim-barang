<?php
use App\Http\Controllers\{
    ApprovalController, AuthController, BarangController,
    BarangKeluarController, BarangMasukController, DashboardController,
    DivisiController, KategoriController, LaporanController,
    PengajuanController, ProfileController, StockBalanceController,
    SupplierController, UserController
};
use Illuminate\Support\Facades\Route;

// ── Guest ────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ── Authenticated ─────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::post('/notifications/read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.read');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profil
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',         [ProfileController::class, 'edit'])->name('edit');
        Route::put('/',         [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    });

    // ── Barang — admin_divisi juga bisa kelola ────────────────────────────────
    Route::middleware('role:admin_divisi,purchasing,superadmin')
         ->resource('barang', BarangController::class);

    // ── Barang Masuk & Keluar — admin_divisi sekarang punya akses ─────────────
    Route::middleware('role:admin_divisi,purchasing,superadmin')
         ->resource('barang-masuk', BarangMasukController::class);
    Route::middleware('role:admin_divisi,purchasing,superadmin')
         ->resource('barang-keluar', BarangKeluarController::class);

    // ── Kategori AJAX store ───────────────────────────────────────────────────
    Route::post('/kategori', [KategoriController::class, 'store'])
         ->name('kategori.store')
         ->middleware('role:admin_divisi,purchasing,superadmin');
    // Kelola kategori lengkap (superadmin)
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/kategori',                  [KategoriController::class, 'index'])->name('kategori.index');
        Route::get('/kategori/create',           [KategoriController::class, 'create'])->name('kategori.create');
        Route::get('/kategori/{kategori}/edit',  [KategoriController::class, 'edit'])->name('kategori.edit');
        Route::put('/kategori/{kategori}',       [KategoriController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/{kategori}',    [KategoriController::class, 'destroy'])->name('kategori.destroy');
    });

    // ── Supplier AJAX store ───────────────────────────────────────────────────
    Route::post('/supplier', [SupplierController::class, 'store'])
         ->name('supplier.store')
         ->middleware('role:admin_divisi,purchasing,superadmin');

    // ── Stock ─────────────────────────────────────────────────────────────────
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/', [StockBalanceController::class, 'index'])->name('index');
        Route::post('/rekalkuasi/{barang}',  [StockBalanceController::class, 'rekalkuasi'])
             ->middleware('role:admin_divisi,purchasing,superadmin')->name('rekalkuasi');
        Route::post('/rekalkuasi-semua', [StockBalanceController::class, 'rekalkuasiSemua'])
             ->middleware('role:superadmin')->name('rekalkuasi.semua');
    });

    // ── Pengajuan ─────────────────────────────────────────────────────────────
    Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
        Route::get('/',     [PengajuanController::class, 'index'])->name('index');
        Route::get('/buat', [PengajuanController::class, 'create'])
             ->middleware('role:staff,admin_divisi,superadmin')->name('create');
        Route::post('/',    [PengajuanController::class, 'store'])
             ->middleware('role:staff,admin_divisi,superadmin')->name('store');
        Route::get('/{pengajuan}',       [PengajuanController::class, 'show'])->name('show');
        Route::get('/{pengajuan}/edit',  [PengajuanController::class, 'edit'])
             ->middleware('role:staff,admin_divisi,superadmin')->name('edit');
        Route::put('/{pengajuan}',       [PengajuanController::class, 'update'])
             ->middleware('role:staff,admin_divisi,superadmin')->name('update');
        Route::delete('/{pengajuan}',    [PengajuanController::class, 'destroy'])->name('destroy');
        Route::post('/{pengajuan}/submit', [PengajuanController::class, 'submit'])->name('submit');

        // Admin divisi: tambah barang custom ke sistem
        Route::post('/detail/{detail}/tambah-barang',
            [PengajuanController::class, 'tambahBarangDariPengajuan'])
            ->middleware('role:admin_divisi,superadmin')
            ->name('detail.tambah-barang');
    });

    // ── Approval ──────────────────────────────────────────────────────────────
    Route::prefix('approval')->name('approval.')->group(function () {
        Route::get('/{pengajuan}',  [ApprovalController::class, 'showApproval'])
             ->middleware('role:wakil_direktur,direktur,superadmin')->name('show');
        Route::post('/{pengajuan}/review',   [ApprovalController::class, 'review'])
             ->middleware('role:admin_divisi,superadmin')->name('review');
        Route::post('/{pengajuan}/teruskan', [ApprovalController::class, 'teruskan'])
             ->middleware('role:admin_divisi,superadmin')->name('teruskan');
        Route::post('/{pengajuan}/proses',   [ApprovalController::class, 'proses'])
             ->middleware('role:purchasing,superadmin')->name('proses');
        Route::post('/{pengajuan}/ajukan-approval', [ApprovalController::class, 'ajukanApproval'])
             ->middleware('role:purchasing,superadmin')->name('ajukan');
        Route::post('/{pengajuan}/setujui',  [ApprovalController::class, 'setujui'])
             ->middleware('role:wakil_direktur,direktur,superadmin')->name('setujui');
        Route::post('/{pengajuan}/selesai',  [ApprovalController::class, 'selesai'])
             ->middleware('role:purchasing,admin_divisi,superadmin')->name('selesai');
        Route::post('/{pengajuan}/tolak',    [ApprovalController::class, 'tolak'])
             ->middleware('role:admin_divisi,purchasing,wakil_direktur,direktur,superadmin')->name('tolak');
    });

    // ── Laporan ───────────────────────────────────────────────────────────────
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/',             [LaporanController::class, 'index'])->name('index');
        Route::get('/barang-masuk', [LaporanController::class, 'barangMasuk'])->name('barang-masuk');
        Route::get('/barang-keluar',[LaporanController::class, 'barangKeluar'])->name('barang-keluar');
        Route::get('/pengajuan',    [LaporanController::class, 'pengajuan'])->name('pengajuan');
        Route::get('/stock',        [LaporanController::class, 'stock'])->name('stock');
    });

    // ── Master Data ───────────────────────────────────────────────────────────
    Route::middleware('role:superadmin')->group(function () {
        Route::resource('divisi', DivisiController::class);
        Route::resource('users',  UserController::class);
    });
});