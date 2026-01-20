<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashOnHandController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $currentCabangId = (in_array($user->role, ['admin', 'manager']))
            ? $request->get('cabang_id')
            : $user->cabang_id;

        if (!$currentCabangId) {
            $listCabang = DB::table('cabang')->get();

            return view('cash_on_hand.index', [
                'listCabang' => $listCabang,
                'currentCabangId' => null,
                'cashOnHand' => 0,
                'riwayatKas' => [],
            ]);
        }

        $penjualanTunai = DB::table('laporan_penjualan')
            ->where('cabang_id', $currentCabangId)
            ->whereDate('created_at', $today)
            ->sum('total_penjualan');

        $kas = DB::table('kas_kecil')
            ->where('cabang_id', $currentCabangId)
            ->whereDate('created_at', $today)
            ->select(
                DB::raw("SUM(CASE WHEN jenis = 'masuk' THEN jumlah ELSE 0 END) as masuk"),
                DB::raw("SUM(CASE WHEN jenis = 'keluar' THEN jumlah ELSE 0 END) as keluar"),
                DB::raw("SUM(CASE WHEN jenis = 'setoran' THEN jumlah ELSE 0 END) as setoran")
            )->first();

        $cashOnHand = ($kas->masuk + $penjualanTunai) - ($kas->keluar + $kas->setoran);

        $riwayatKas = DB::table('kas_kecil')
            ->where('cabang_id', $currentCabangId)
            ->whereDate('created_at', $today)
            ->latest()
            ->get();

        $listCabang = DB::table('cabang')->get();

        return view('cash_on_hand.index', compact(
            'penjualanTunai', 'cashOnHand', 'riwayatKas', 'listCabang', 'currentCabangId'
        ) + [
            'kasMasuk' => $kas->masuk,
            'kasKeluar' => $kas->keluar,
            'totalSetoran' => $kas->setoran,
        ]);
    }

    public function setor(Request $request)
    {
        $request->validate([
            'jumlah_setoran' => 'required|numeric|min:1',
        ]);

        DB::table('kas_kecil')->insert([
            'cabang_id' => Auth::user()->cabang_id,
            'user_id' => Auth::id(),
            'jenis' => 'setoran',
            'jumlah' => $request->jumlah_setoran,
            'keterangan' => 'Setoran uang ke Owner/Manager',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Setoran berhasil dicatat.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:masuk,keluar,setoran',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'required|string|max:255',
            'cabang_id' => 'required_if:role,admin,manager',
        ]);

        $user = Auth::user();
        $targetCabangId = $request->filled('cabang_id') ? $request->cabang_id : $user->cabang_id;

        if (!$targetCabangId) {
            return back()->with('error', 'Cabang belum ditentukan.');
        }

        DB::transaction(function () use ($request, $targetCabangId) {
            // 1. Catat mutasi di kas_kecil
            DB::table('kas_kecil')->insert([
                'cabang_id' => $targetCabangId,
                'user_id' => Auth::id(),
                'jenis' => $request->jenis,
                'jumlah' => $request->jumlah,
                'keterangan' => $request->keterangan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Jika jenis 'keluar', catat ke tabel pengeluaran
            if ($request->jenis === 'keluar') {
                DB::table('pengeluaran')->insert([
                    'tanggal' => now()->toDateString(),
                    'cabang_id' => $targetCabangId,
                    'user_id' => Auth::id(),
                    'jumlah' => $request->jumlah,
                    'deskripsi' => '[Operasional Laci] ' . $request->keterangan,
                    'kategori_id' => 6,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }); // <-- Penutup Transaction

        return redirect()->back()->with('success', 'Data kas berhasil dicatat.');
    } // <-- Penutup Fungsi Store

    public function laporanManager(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'manager'])) {
            abort(403);
        }

        $tanggal = $request->get('tanggal', now()->toDateString());
        $cabangId = $request->get('cabang_id');
        $listCabang = DB::table('cabang')->get();

        $queryKasKecil = DB::table('kas_kecil')
            ->join('users', 'kas_kecil.user_id', '=', 'users.id')
            ->join('cabang', 'kas_kecil.cabang_id', '=', 'cabang.id')
            ->select(
                'kas_kecil.created_at',
                'cabang.nama_cabang',
                'users.name as user_name',
                'kas_kecil.jenis',
                'kas_kecil.keterangan',
                'kas_kecil.jumlah'
            )
            ->whereDate('kas_kecil.created_at', $tanggal);

        $queryPenjualan = DB::table('laporan_penjualan')
            ->join('cabang', 'laporan_penjualan.cabang_id', '=', 'cabang.id')
            ->select(
                'laporan_penjualan.created_at',
                'cabang.nama_cabang',
                DB::raw("'Sistem POS' as user_name"),
                DB::raw("'masuk' as jenis"),
                DB::raw("'Penjualan Produk (Tunai)' as keterangan"),
                'laporan_penjualan.total_penjualan as jumlah'
            )
            ->whereDate('laporan_penjualan.created_at', $tanggal);

        if ($cabangId) {
            $queryKasKecil->where('kas_kecil.cabang_id', $cabangId);
            $queryPenjualan->where('laporan_penjualan.cabang_id', $cabangId);
        }

        $riwayatKas = $queryKasKecil->unionAll($queryPenjualan)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cash_on_hand.manager_report', compact('riwayatKas', 'tanggal', 'listCabang'));
    }
}