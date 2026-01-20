<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\LaporanPenjualan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Data User & Status Akses
        $user = Auth::user();
        $hasFullAccess = in_array($user->role, ['admin', 'manager', 'owner']);
        $selectedCabang = $request->get('cabang_id');

        // 2. Definisi Filter (Dibuat di awal agar bisa dipakai di bawahnya)
        $applyFilter = function ($query) use ($hasFullAccess, $user, $selectedCabang) {
            if ($hasFullAccess) {
                return $selectedCabang ? $query->where('cabang_id', $selectedCabang) : $query;
            }
            return $query->where('cabang_id', $user->cabang_id);
        };

        // 3. Data Penjualan
        $penjualanHariIni = LaporanPenjualan::whereDate('tanggal', today())
            ->tap($applyFilter)->sum('total_penjualan');

        $transaksiHariIni = LaporanPenjualan::whereDate('tanggal', today())
            ->tap($applyFilter)->count();

        $penjualanBulanIni = LaporanPenjualan::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->tap($applyFilter)->sum('total_penjualan');

        // Kinerja vs Kemarin
        $penjualanKemarin = LaporanPenjualan::whereDate('tanggal', today()->subDay())
            ->tap($applyFilter)->sum('total_penjualan');

        $persentaseKinerja = ($penjualanKemarin > 0)
            ? round((($penjualanHariIni - $penjualanKemarin) / $penjualanKemarin) * 100, 1)
            : ($penjualanHariIni > 0 ? 100 : 0);

        // 4. Data Pengeluaran (Berdasarkan Tabel Pengeluaran Anda)
        $pengeluaranHariIni = Pengeluaran::whereDate('tanggal', today())
            ->tap($applyFilter)->sum('jumlah');

        $pengeluaranBulanIni = Pengeluaran::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->tap($applyFilter)->sum('jumlah');

        // Komposisi Pengeluaran (Relasi ke kategori_id)
        $komposisiPengeluaran = Pengeluaran::with('kategori')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->tap($applyFilter)
            ->selectRaw('kategori_id, SUM(jumlah) as total')
            ->groupBy('kategori_id')
            ->orderBy('total', 'desc')
            ->get();

        // 5. Data Grafik 7 Hari Terakhir
        $dataPenjualan = [];
        $dataPengeluaran = [];
        $labelHari = [];

        for ($i = 6; $i >= 0; $i--) {
            $tgl = today()->subDays($i);
            $labelHari[] = $tgl->format('d M');

            $dataPenjualan[] = LaporanPenjualan::whereDate('tanggal', $tgl)
                ->tap($applyFilter)->sum('total_penjualan');

            $dataPengeluaran[] = Pengeluaran::whereDate('tanggal', $tgl)
                ->tap($applyFilter)->sum('jumlah');
        }

        // 6. Data Pendukung
        $cabangs = Cabang::all();
        $totalCabang = $cabangs->count();

        // 7. Return View (Pastikan 'user' dikirim di sini)
        return view('dashboard', compact(
            'user', 
            'hasFullAccess', 
            'cabangs', 
            'totalCabang',
            'penjualanHariIni', 
            'transaksiHariIni', 
            'penjualanBulanIni', 
            'persentaseKinerja',
            'pengeluaranHariIni', 
            'pengeluaranBulanIni', 
            'komposisiPengeluaran',
            'dataPenjualan', 
            'dataPengeluaran', 
            'labelHari'
        ));
    }
}