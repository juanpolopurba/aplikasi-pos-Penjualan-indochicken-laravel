<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\CashOnHandController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriPengeluaranController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PemakaianController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// --- Public Route ---
Route::get('/', function () {
    return view('auth.login');
});

// --- Authenticated Routes (Semua yang butuh login ada di sini) ---
Route::middleware(['auth', 'verified'])->group(function () {

    Route::post('/user/{id}/restore', [App\Http\Controllers\UserController::class, 'restore'])->name('user.restore');

   Route::get('/get-stok-bahan/{cabang_id}', [App\Http\Controllers\PemakaianController::class, 'getStok']);

    // 1. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. GRUP LAPORAN & PDF
    Route::prefix('laporan')->name('laporan.')->group(function () {
        // Pengeluaran
        Route::get('/pengeluaran/pdf', [LaporanController::class, 'exportPengeluaranPDF'])->name('pengeluaran_pdf');

        // Penjualan & Rekap
        Route::get('/penjualan', [LaporanController::class, 'index'])->name('penjualan.index');
        Route::get('/penjualan/rekap/pdf', [LaporanController::class, 'exportRekapPDF'])->name('penjualan.rekap_pdf');
        Route::get('/penjualan/{id}/pdf', [LaporanController::class, 'downloadPDF'])->name('penjualan.pdf');
        Route::get('/penjualan/trash', [LaporanController::class, 'trash'])->name('penjualan.trash');
        Route::get('/penjualan/{id}', [LaporanController::class, 'showDetail'])->name('penjualan.show');

        // Laba Rugi
        Route::get('/laba-rugi', [LaporanController::class, 'labaRugi'])->name('labarugi');
        Route::get('/laba-rugi/pdf', [LaporanController::class, 'exportLabaRugiPDF'])->name('labarugi_pdf');

        // Pembelian Bahan Baku (URL: /laporan/pembelian-bahan/pdf)
        Route::get('/pembelian-bahan/pdf', [LaporanController::class, 'exportBahanBakuPDF'])->name('pembelian_bahan_pdf');

        // Kas Kecil
        Route::get('/kas-kecil/pdf', [LaporanController::class, 'exportKasKecilPDF'])->name('kas_kecil_pdf');
    });

    // 3. PEMBELIAN BAHAN BAKU (Transaksi)
    Route::prefix('pembelian')->name('pembelian.')->group(function () {
        Route::get('/', [PembelianController::class, 'index'])->name('index');
        Route::get('/create', [PembelianController::class, 'create'])->name('create');
        Route::post('/store', [PembelianController::class, 'store'])->name('store');
        Route::get('/{id}', [PembelianController::class, 'show'])->name('show');
    });

    Route::get('/cetak-pembelian-pdf', [PembelianController::class, 'cetakPdf'])
        ->name('pembelian.cetak.pdf');

    // 4. Cash On Hand (COH)
    Route::get('/cash-on-hand', [CashOnHandController::class, 'index'])->name('cash_on_hand.index');
    Route::post('/cash-on-hand', [CashOnHandController::class, 'store'])->name('cash_on_hand.store');
    Route::post('/cash-on-hand/setor', [CashOnHandController::class, 'setor'])->name('cash_on_hand.setor');
    Route::get('/manager/cash-report', [CashOnHandController::class, 'laporanManager'])->name('cash_on_hand.manager');

    // 5. POS & Pemakaian Bahan Baku
    Route::get('/pos/create', [PosController::class, 'create'])->name('pos.create');
    Route::post('/pos/store', [PosController::class, 'store'])->name('pos.store');
    Route::get('/pemakaian', [PemakaianController::class, 'index'])->name('pemakaian.index');
    Route::get('/pemakaian/create', [PemakaianController::class, 'create'])->name('pemakaian.create');
    Route::post('/pemakaian', [PemakaianController::class, 'store'])->name('pemakaian.store');

 
    // 7. Pengeluaran Extra (Trash)
    Route::get('/pengeluaran/trash', [PengeluaranController::class, 'trash'])->name('pengeluaran.trash');
    Route::post('/pengeluaran/{id}/restore', [PengeluaranController::class, 'restore'])->name('pengeluaran.restore');
    Route::delete('/pengeluaran/{id}/force-delete', [PengeluaranController::class, 'forceDelete'])->name('pengeluaran.forceDelete');

    // 8. Profile & Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

       // 6. Master Data (Resources)
    Route::resource('user', UserController::class);
    Route::resource('cabang', CabangController::class);
    Route::resource('menu', MenuController::class);
    Route::resource('bahan_baku', BahanBakuController::class);
    Route::resource('kategori', KategoriPengeluaranController::class);
    Route::resource('pengeluaran', PengeluaranController::class)->except(['edit', 'update']);

});

require __DIR__.'/auth.php';
