<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PemakaianController extends Controller
{
    /**
     * Menampilkan riwayat pemakaian lengkap (Laporan)
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = DB::table('pemakaian_bahan')
            ->join('cabang', 'pemakaian_bahan.cabang_id', '=', 'cabang.id')
            ->join('bahan_baku', 'pemakaian_bahan.bahan_baku_id', '=', 'bahan_baku.id')
            ->select(
                'pemakaian_bahan.*',
                'cabang.nama_cabang',
                'bahan_baku.nama as nama_bahan',
                'bahan_baku.satuan'
            );

        // Filter Cabang berdasarkan Role
        if (! in_array($user->role, ['admin', 'manager'])) {
            $query->where('pemakaian_bahan.cabang_id', $user->cabang_id);
        } elseif ($request->filled('cabang_id')) {
            $query->where('pemakaian_bahan.cabang_id', $request->cabang_id);
        }

        // Filter Tanggal (Menggunakan kolom 'tanggal' sesuai struktur tabel Anda)
        if ($request->filled('tanggal')) {
            $query->whereDate('pemakaian_bahan.tanggal', $request->tanggal);
        }

        $riwayat = $query->orderBy('pemakaian_bahan.tanggal', 'desc')->paginate(10);
        $listCabang = DB::table('cabang')->get();

        return view('pemakaian.index', compact('riwayat', 'listCabang'));
    }

    /**
     * Menampilkan form input pemakaian
     */
    public function create()
    {
        $user = auth()->user();

        // Ambil stok bahan baku hanya untuk cabang user yang login
        $bahanBakus = DB::table('bahan_baku')
            ->join('inventory', 'bahan_baku.id', '=', 'inventory.bahan_id')
            ->where('inventory.cabang_id', $user->cabang_id)
            ->select('bahan_baku.id', 'bahan_baku.nama', 'bahan_baku.satuan', 'inventory.stok_saat_ini')
            ->get();

        // AMBIL DATA SEMUA CABANG (Untuk Admin/Manager)
        $cabangs = DB::table('cabang')->get();

        $riwayatSingkat = DB::table('pemakaian_bahan')
            ->join('bahan_baku', 'pemakaian_bahan.bahan_baku_id', '=', 'bahan_baku.id')
            ->where('pemakaian_bahan.cabang_id', $user->cabang_id)
            ->select('pemakaian_bahan.*', 'bahan_baku.nama as nama_bahan', 'bahan_baku.satuan')
            ->orderBy('pemakaian_bahan.tanggal', 'desc')
            ->limit(5)
            ->get();

        return view('pemakaian.create', compact('bahanBakus', 'riwayatSingkat', 'cabangs'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'bahan_baku_id' => 'required',
            'jumlah' => 'required|numeric|min:0.01',
            'cabang_id' => 'nullable|exists:cabang,id',
        ]);

        $cabangId = in_array($user->role, ['admin', 'manager'])
                    ? $request->cabang_id
                    : $user->cabang_id;

        if (! $cabangId) {
            return back()->with('error', 'Pilih cabang terlebih dahulu.');
        }

        // --- MULAI PROSES TRANSAKSI ---
        DB::beginTransaction();

        try {
            // 1. Cek stok di tabel inventory
            $inventory = DB::table('inventory')
                ->where('bahan_id', $request->bahan_baku_id)
                ->where('cabang_id', $cabangId)
                ->first();

            if (! $inventory || $inventory->stok_saat_ini < $request->jumlah) {
                return back()->with('error', 'Stok tidak mencukupi atau data inventory tidak ditemukan.');
            }

            // 2. Simpan Data ke tabel pemakaian_bahan
            DB::table('pemakaian_bahan')->insert([
                'tanggal' => now(),
                'bahan_baku_id' => $request->bahan_baku_id,
                'jumlah' => $request->jumlah,
                'keterangan' => $request->keterangan,
                'cabang_id' => $cabangId,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. UPDATE STOK DI TABEL INVENTORY (PENTING!)
            DB::table('inventory')
                ->where('bahan_id', $request->bahan_baku_id)
                ->where('cabang_id', $cabangId)
                ->decrement('stok_saat_ini', $request->jumlah);

            DB::commit(); // Simpan permanen jika semua ok

            return redirect()->route('pemakaian.index')->with('success', 'Pemakaian bahan berhasil dicatat dan stok diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua jika ada error

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function getStok($cabang_id)
    {
        // Kita join tabel inventory dengan tabel bahan_bakus untuk ambil Nama & Satuan
        $stok = \DB::table('inventory')
            ->join('bahan_baku', 'inventory.bahan_id', '=', 'bahan_baku.id')
            ->where('inventory.cabang_id', $cabang_id)
            ->select(
                'bahan_baku.id',
                'bahan_baku.nama',
                'bahan_baku.satuan',
                'inventory.stok_saat_ini'
            )
            ->get();

        return response()->json($stok);
    }
}
