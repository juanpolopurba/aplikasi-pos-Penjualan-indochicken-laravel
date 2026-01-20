<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Cabang;
use App\Models\KasKecil;
use App\Models\LaporanPenjualan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Tampilan Index Laporan Penjualan
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $cabangs = Cabang::all();

        $selectedCabangId = $request->cabang_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $laporansQuery = LaporanPenjualan::with(['cabang', 'user'])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        // Filter Hak Akses
        if (! in_array($user->role, ['admin', 'manager'])) {
            $laporansQuery->where('cabang_id', $user->cabang_id);
        } elseif ($selectedCabangId) {
            $laporansQuery->where('cabang_id', $selectedCabangId);
        }

        // Filter Tanggal
        if ($startDate) {
            $laporansQuery->whereDate('tanggal', '>=', $startDate);
        }
        if ($endDate) {
            $laporansQuery->whereDate('tanggal', '<=', $endDate);
        }

        $laporans = $laporansQuery->paginate(15)->appends($request->except('page'));
        $rekapTotal = $laporansQuery->sum('total_penjualan');

        return view('laporan.laporan_penjualan', compact(
            'laporans', 'cabangs', 'rekapTotal', 'selectedCabangId', 'startDate', 'endDate'
        ));
    }

    /**
     * Tampilan Laba Rugi
     */
  public function labarugi(Request $request)
{
    $user = Auth::user();
    $hasFullAccess = in_array($user->role, ['admin', 'manager', 'owner']);
    $selectedCabang = $request->get('cabang_id');

    // 1. DEFINISIKAN $applyFilter TERLEBIH DAHULU (PENTING!)
    $applyFilter = function ($query) use ($hasFullAccess, $user, $selectedCabang) {
        if ($hasFullAccess) {
            return $selectedCabang ? $query->where('cabang_id', $selectedCabang) : $query;
        }
        return $query->where('cabang_id', $user->cabang_id);
    };

    // 2. Ambil input bulan & tahun (Type Casting ke Integer agar tidak error Carbon)
    $bulan = (int) $request->get('bulan', date('m'));
    $tahun = (int) $request->get('tahun', date('Y'));

    $labelsLabaRugi = [];
    $dataOmzet = [];
    $dataBiaya = [];

    // 3. Looping Tren 6 Bulan
    for ($i = 5; $i >= 0; $i--) {
        $date = now()->setYear($tahun)->setMonth($bulan)->subMonths($i);
        $labelsLabaRugi[] = $date->format('M Y');

        $dataOmzet[] = \App\Models\LaporanPenjualan::whereMonth('tanggal', $date->month)
            ->whereYear('tanggal', $date->year)
            ->tap($applyFilter)
            ->sum('total_penjualan');

        $dataBiaya[] = \App\Models\Pengeluaran::whereMonth('tanggal', $date->month)
            ->whereYear('tanggal', $date->year)
            ->tap($applyFilter)
            ->sum('jumlah');
    }

    // 4. Query Detail untuk Tabel (Bulan yang dipilih saja)
    $totalPendapatan = \App\Models\LaporanPenjualan::whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', $tahun)
        ->tap($applyFilter)
        ->sum('total_penjualan');

    $totalPengeluaran = \App\Models\Pengeluaran::whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', $tahun)
        ->tap($applyFilter)
        ->sum('jumlah');

    $labaBersih = $totalPendapatan - $totalPengeluaran;

    $detailPendapatan = \App\Models\LaporanPenjualan::whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', $tahun)
        ->tap($applyFilter)
        ->orderBy('tanggal', 'desc')
        ->get();

    $detailPengeluaran = \App\Models\Pengeluaran::whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', $tahun)
        ->tap($applyFilter)
        ->orderBy('tanggal', 'desc')
        ->get();

    $cabangs = \App\Models\Cabang::all();

    // Ambil data pengeluaran berdasarkan kategori untuk Pie Chart
$pengeluaranPerKategori = \App\Models\Pengeluaran::whereMonth('tanggal', $bulan)
    ->whereYear('tanggal', $tahun)
    ->tap($applyFilter)
    ->select('kategori_id', DB::raw('SUM(jumlah) as total'))
    ->groupBy('kategori_id')
    ->with('kategori') // Pastikan relasi 'kategori' ada di Model Pengeluaran
    ->get();

$labelsKategori = $pengeluaranPerKategori->map(fn($item) => $item->kategori->nama_kategori ?? 'Lain-lain');
$dataKategori = $pengeluaranPerKategori->map(fn($item) => $item->total);

    return view('laporan.laba_rugi', compact(
        'bulan', 'tahun', 'totalPendapatan', 'totalPengeluaran', 'labaBersih',
        'detailPendapatan', 'detailPengeluaran', 'cabangs', 
        'labelsLabaRugi', 'dataOmzet', 'dataBiaya', 'dataKategori','labelsKategori'
    ));
}

    // --- FITUR EXPORT PDF ---

    public function exportPengeluaranPDF(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $cabangId = $request->query('cabang_id');

        // 1. Ambil data user yang login
        $user = auth()->user();

        $query = \App\Models\Pengeluaran::with(['kategori', 'user', 'cabang']);

        // 2. PROTEKSI ROLE: Jika kasir, kunci ke cabangnya sendiri
        if ($user->role === 'kasir') {
            $query->where('cabang_id', $user->cabang_id);
            $cabangId = $user->cabang_id; // Timpa variabel agar label judul PDF sesuai
        } else {
            // Jika Admin/Manager, gunakan filter cabang dari dropdown (jika ada)
            if ($cabangId) {
                $query->where('cabang_id', $cabangId);
            }
        }

        // 3. Filter Range Tanggal
        if ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal', '<=', $endDate);
        }

        // Mengurutkan berdasarkan tanggal terbaru (desc), lalu jam terbaru (desc)
        $pengeluaran = $query->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        $totalPengeluaran = $pengeluaran->sum('jumlah');

        // 4. Penentuan Label Periode
        if ($startDate && $endDate) {
            $namaBulan = \Carbon\Carbon::parse($startDate)->format('d/m/Y').' - '.\Carbon\Carbon::parse($endDate)->format('d/m/Y');
        } else {
            $namaBulan = \Carbon\Carbon::now()->translatedFormat('F');
        }

        $tahun = $startDate ? \Carbon\Carbon::parse($startDate)->format('Y') : date('Y');

        // 5. Penentuan Nama Cabang untuk Header PDF
        $cabang = \App\Models\Cabang::find($cabangId);
        $namaCabang = $cabang ? $cabang->nama_cabang : 'Semua Cabang';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pengeluaran_pdf', compact(
            'pengeluaran', 'totalPengeluaran', 'namaBulan', 'tahun', 'namaCabang'
        ));

        return $pdf->stream('Laporan-Pengeluaran.pdf');
    }

    /**
     * PDF 2: Laba Rugi
     */
    public function exportLabaRugiPDF(Request $request)
    {
        $bulan = (int) $request->get('bulan', date('m'));
        $tahun = (int) $request->get('tahun', date('Y'));
        $cabangId = $request->get('cabang_id');

        $totalPenjualan = LaporanPenjualan::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->when($cabangId, fn ($q) => $q->where('cabang_id', $cabangId))
            ->sum('total_penjualan');

        $totalPengeluaran = DB::table('pengeluaran')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->when($cabangId, fn ($q) => $q->where('cabang_id', $cabangId))
            ->sum('jumlah');

        $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');
        $data = [
            'tanggal' => $namaBulan.' '.$tahun,
            'cabang' => $cabangId ? (Cabang::find($cabangId)->nama_cabang) : 'Semua Cabang',
            'pemasukan' => $totalPenjualan,
            'pengeluaran' => $totalPengeluaran,
            'laba_bersih' => $totalPenjualan - $totalPengeluaran,
        ];

        $pdf = Pdf::loadView('laporan.pdf_laba_rugi', $data);

        return $pdf->stream("Laporan-Laba-Rugi-{$namaBulan}.pdf");
    }

    /**
     * PDF 3: Pembelian Bahan Baku
     */
    public function exportBahanBakuPDF(Request $request)
    {
        $cabang_id = $request->cabang_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        try {
            $query = DB::table('pembelian_bahan_baku')
                ->join('cabang', 'pembelian_bahan_baku.cabang_id', '=', 'cabang.id')
                ->join('users', 'pembelian_bahan_baku.user_id', '=', 'users.id')
                ->leftJoin('detail_pembelian_baku', 'pembelian_bahan_baku.id', '=', 'detail_pembelian_baku.pembelian_id')
                ->leftJoin('bahan_baku', 'detail_pembelian_baku.bahan_id', '=', 'bahan_baku.id')
                ->select(
                    'pembelian_bahan_baku.*',
                    'cabang.nama_cabang',
                    'users.username as nama_penginput',
                    DB::raw("GROUP_CONCAT(bahan_baku.nama SEPARATOR ', ') as daftar_bahan")
                )
                ->groupBy(
                    'pembelian_bahan_baku.id',
                    'pembelian_bahan_baku.tanggal',
                    'pembelian_bahan_baku.supplier',
                    'pembelian_bahan_baku.total_pembelian',
                    'pembelian_bahan_baku.cabang_id',
                    'pembelian_bahan_baku.user_id',
                    'pembelian_bahan_baku.created_at',
                    'cabang.nama_cabang',
                    'users.username'
                );

            // Filter Hak Akses
            if (! in_array(auth()->user()->role, ['admin', 'manager'])) {
                $query->where('pembelian_bahan_baku.cabang_id', auth()->user()->cabang_id);
            } elseif ($cabang_id) {
                $query->where('pembelian_bahan_baku.cabang_id', $cabang_id);
            }

            if ($start_date) {
                $query->whereDate('pembelian_bahan_baku.tanggal', '>=', $start_date);
            }
            if ($end_date) {
                $query->whereDate('pembelian_bahan_baku.tanggal', '<=', $end_date);
            }

            // 1. AMBIL DATA DULU
            $pembelian = $query->orderBy('pembelian_bahan_baku.created_at', 'desc')->get();

            // 2. BARU CEK APAKAH KOSONG
            if ($pembelian->isEmpty()) {
                return 'Data kosong untuk periode ini, tidak ada yang bisa dicetak.';
            }

            $total = $pembelian->sum('total_pembelian');
            $namaCabang = $cabang_id ? DB::table('cabang')->where('id', $cabang_id)->value('nama_cabang') : 'Semua Cabang';

            // 3. GENERATE PDF
            $pdf = Pdf::loadView('laporan.pdf_pembelian_bahan', [
                'pembelian' => $pembelian,
                'total' => $total,
                'namaCabang' => $namaCabang,
                'startDate' => $start_date,
                'endDate' => $end_date,
            ]);

            return $pdf->stream('Laporan-Pembelian-Bahan.pdf');

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat generate PDF',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
        }
    }

    public function exportRekapPDF(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $cabangId = $request->query('cabang_id');

        // 1. Ambil data user yang sedang login
        $user = auth()->user();

        $query = \App\Models\LaporanPenjualan::with(['cabang', 'user']);

        // --- LOGIKA FILTER CABANG BERDASARKAN ROLE ---
        if ($user->role === 'kasir') {
            // Jika Kasir, paksa hanya melihat cabangnya sendiri
            $query->where('cabang_id', $user->cabang_id);
            $cabangId = $user->cabang_id; // Set agar nama_cabang di judul PDF benar
        } else {
            // Jika Admin/Manager, gunakan filter dari dropdown jika ada
            if ($cabangId) {
                $query->where('cabang_id', $cabangId);
            }
        }

        // 2. Filter Tanggal
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $laporans = $query->orderBy('created_at', 'desc')->get();
        $totalSemua = $laporans->sum('total_penjualan');

        // --- LOGIKA NAMA CABANG & PERIODE UNTUK JUDUL ---
        $nama_cabang = $cabangId
            ? (\App\Models\Cabang::find($cabangId)->nama_cabang ?? 'Cabang Tidak Ditemukan')
            : 'Semua Cabang';

        $periode = ($startDate && $endDate)
            ? \Carbon\Carbon::parse($startDate)->format('d/m/Y').' s/d '.\Carbon\Carbon::parse($endDate)->format('d/m/Y')
            : 'Semua Periode';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.penjualan_rekap_pdf', compact(
            'laporans', 'totalSemua', 'nama_cabang', 'periode'
        ));

        return $pdf->stream('rekap-penjualan.pdf');
    }

    /**
     * PDF 5: Kas Kecil
     */
    public function exportKasKecilPDF(Request $request)
    {
        $tanggal = $request->query('tanggal', date('Y-m-d'));
        $cabangId = $request->query('cabang_id');

        $query = KasKecil::query();
        if ($cabangId) {
            $query->where('cabang_id', $cabangId);
        }

        $riwayatKas = $query->whereDate('created_at', $tanggal)->get();

        $pdf = Pdf::loadView('laporan.kas_kecil_pdf', compact('riwayatKas', 'tanggal'));

        return $pdf->stream('Log-Kas-'.$tanggal.'.pdf');
    }

    /**
     * PDF 6: Download Nota Tunggal
     */
    public function downloadPDF($id)
    {
        $laporan = LaporanPenjualan::with(['cabang', 'details.menu'])->findOrFail($id);
        $data = [
            'laporan' => $laporan,
            'title' => 'Nota Transaksi #'.$laporan->id,
            'tanggal' => $laporan->created_at->format('d F Y'),
            'jam' => $laporan->created_at->format('H:i'),
        ];

        return Pdf::loadView('laporan.pdf_nota', $data)->download('nota-indochicken-'.$id.'.pdf');
    }

    // --- FUNGSI CRUD & HELPER ---

    public function showDetail($id)
    {
        try {
            $laporan = LaporanPenjualan::with(['cabang', 'details.menu', 'user'])->find($id);
            if (! $laporan) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            return response()->json([
                'status' => 'success',
                'id' => $laporan->id,
                'tanggal' => $laporan->tanggal,
                'cabang' => $laporan->cabang,
                'details' => $laporan->details,
                'total_penjualan' => $laporan->total_penjualan,
                'petugas' => $laporan->user->name ?? 'System',
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        if (! in_array(auth()->user()->role, ['admin', 'manager'])) {
            return back()->with('error', 'Akses ditolak!');
        }

        DB::transaction(function () use ($id) {
            $laporan = LaporanPenjualan::findOrFail($id);
            ActivityLog::record('HAPUS', "Menghapus Laporan Penjualan #{$id}");
            $laporan->delete();
        });

        return back()->with('success', 'Laporan berhasil dihapus.');
    }

    public function trash()
    {
        if (auth()->user()->role !== 'manager') {
            return redirect()->route('laporan.penjualan.index');
        }
        $laporans = LaporanPenjualan::onlyTrashed()->with('cabang')->paginate(15);

        return view('laporan.trash', compact('laporans'));
    }

    public function restore($id)
    {
        LaporanPenjualan::withTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Data dikembalikan.');
    }

    public function forceDelete($id)
    {
        // Cari data yang sudah di-trash
        $laporan = LaporanPenjualan::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($laporan) {
            // 1. Hapus semua detail itemnya dulu (Agar tidak ada data sampah di tabel detail)
            $laporan->details()->delete();

            // 2. Hapus laporan induk secara permanen
            $laporan->forceDelete();
        });

        return back()->with('success', 'Data laporan dihapus permanen dari sistem.');
    }
}
