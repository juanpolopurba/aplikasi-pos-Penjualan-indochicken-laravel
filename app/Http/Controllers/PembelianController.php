<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

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
            );

        // --- PERBAIKAN DI SINI ---
        // Jika role-nya BUKAN admin DAN BUKAN manager, barulah filter per cabang
        if (! in_array($user->role, ['admin', 'manager'])) {
            $query->where('pembelian_bahan_baku.cabang_id', $user->cabang_id);
        }
        // Jika dia Admin/Manager dan ada filter dropdown cabang dipilih:
        elseif ($request->filled('cabang_id')) {
            $query->where('pembelian_bahan_baku.cabang_id', $request->cabang_id);
        }

        // Filter Tanggal Mulai
        if ($request->filled('start_date')) {
            $query->whereDate('pembelian_bahan_baku.created_at', '>=', $request->start_date);
        }

        // Filter Tanggal Selesai
        if ($request->filled('end_date')) {
            $query->whereDate('pembelian_bahan_baku.created_at', '<=', $request->end_date);
        }

        $pembelian = $query->groupBy(
            'pembelian_bahan_baku.id',
            'pembelian_bahan_baku.tanggal',
            'pembelian_bahan_baku.supplier',
            'pembelian_bahan_baku.total_pembelian',
            'pembelian_bahan_baku.cabang_id',
            'pembelian_bahan_baku.user_id',
            'pembelian_bahan_baku.created_at',
            'cabang.nama_cabang',
            'users.username'
        )
            ->orderBy('pembelian_bahan_baku.created_at', 'desc')
            ->paginate(10);

        // Ambil daftar cabang untuk dropdown filter bagi Admin/Manager
        // $listCabang = DB::table('cabang')->get();
        $cabangs = \App\Models\Cabang::all();

        return view('pembelian.index', compact('pembelian', 'cabangs'));
    }

    public function create()
    {
        $user = Auth::user();
        $bahanBaku = DB::table('bahan_baku')->get();

        // PERBAIKAN: Tambahkan 'manager' dalam pengecekan
        if (in_array($user->role, ['admin', 'manager'])) {
            // Admin & Manager bisa melihat semua cabang
            $cabangs = DB::table('cabang')->get();
        } else {
            // Selain itu (Kasir), hanya melihat cabangnya sendiri
            $cabangs = DB::table('cabang')->where('id', $user->cabang_id)->get();
        }

        return view('pembelian.create', compact('bahanBaku', 'cabangs'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $cabangId = in_array($user->role, ['admin', 'manager'])
                        ? $request->cabang_id
                        : $user->cabang_id;
        $request->validate([
            'tanggal' => 'required|date',
            'supplier' => 'required|string',
            'cabang_id' => in_array($user->role, ['admin', 'manager']) ? 'required' : 'nullable',
            'items' => 'required|array',
            'items.*.bahan_id' => 'required',
            'items.*.kuantitas' => 'required|numeric|min:0.01',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $cabangId, $user) {
                $totalSemua = 0;
                foreach ($request->items as $item) {
                    $totalSemua += (float) $item['kuantitas'] * (float) $item['harga_satuan'];
                }

                // 1. Simpan ke tabel pembelian_bahan_baku
                $pembelianId = DB::table('pembelian_bahan_baku')->insertGetId([
                    'tanggal' => $request->tanggal.' '.now()->format('H:i:s'), // Gabung tgl input + jam skrg
                    'supplier' => $request->supplier,
                    'total_pembelian' => $totalSemua,
                    'cabang_id' => $cabangId,
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($request->items as $item) {
                    // 2. Simpan Detail
                    DB::table('detail_pembelian_baku')->insert([
                        'pembelian_id' => $pembelianId,
                        'bahan_id' => $item['bahan_id'],
                        'kuantitas' => $item['kuantitas'],
                        'harga_satuan' => $item['harga_satuan'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // 3. Update Stok di Inventory (Gunakan Composite Key tanpa ->id)
                    $existing = DB::table('inventory')
                        ->where('bahan_id', $item['bahan_id'])
                        ->where('cabang_id', $cabangId)
                        ->first();

                    if ($existing) {
                        DB::table('inventory')
                            ->where('bahan_id', $item['bahan_id'])
                            ->where('cabang_id', $cabangId)
                            ->update([
                                'stok_saat_ini' => $existing->stok_saat_ini + $item['kuantitas'],
                                'updated_at' => now(),
                            ]);
                    } else {
                        DB::table('inventory')->insert([
                            'bahan_id' => $item['bahan_id'],
                            'cabang_id' => $cabangId,
                            'stok_saat_ini' => $item['kuantitas'],
                            'harga_beli_rata_rata' => $item['harga_satuan'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // 4. Catat ke Tabel KAS_KECIL (OTOMATIS POTONG LACI)
                DB::table('kas_kecil')->insert([
                    'cabang_id' => $cabangId,
                    'user_id' => $user->id,
                    'jenis' => 'keluar', // Uang keluar dari laci
                    'jumlah' => $totalSemua,
                    'keterangan' => 'Pembelian bahan dari: '.$request->supplier.' (Otomatis)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 5. Catat ke tabel pengeluaran (untuk laporan laba rugi)
                DB::table('pengeluaran')->insert([
                    'tanggal' => $request->tanggal,
                    'kategori_id' => 4, // Kategori Pembelian Bahan
                    'deskripsi' => 'Pembelian bahan baku: '.$request->supplier,
                    'jumlah' => $totalSemua,
                    'user_id' => $user->id,
                    'cabang_id' => $cabangId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            return redirect()->route('pembelian.index')->with('success', 'Data Pembelian Berhasil Disimpan & Kas Laci Berkurang!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal Simpan: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        // 1. Ambil data induk nota
        $pembelian = DB::table('pembelian_bahan_baku')
            ->join('cabang', 'pembelian_bahan_baku.cabang_id', '=', 'cabang.id')
            ->join('users', 'pembelian_bahan_baku.user_id', '=', 'users.id')
            ->select('pembelian_bahan_baku.*', 'cabang.nama_cabang', 'users.username as nama_penginput')
            ->where('pembelian_bahan_baku.id', $id)
            ->first();

        if (! $pembelian) {
            abort(404);
        }

        // 2. Ambil rincian barang (Lakukan JOIN agar nama bahan muncul)
        $details = DB::table('detail_pembelian_baku')
            ->join('bahan_baku', 'detail_pembelian_baku.bahan_id', '=', 'bahan_baku.id')
            ->where('detail_pembelian_baku.pembelian_id', $id)
            ->select(
                'detail_pembelian_baku.*',
                'bahan_baku.nama as nama_bahan', // Ini alias untuk di Blade
                'bahan_baku.satuan'
            )
            ->get();

        // 3. PENTING: Kembalikan variabel ke VIEW
        return view('pembelian.show', compact('pembelian', 'details'));
    }

public function cetakPdf(Request $request)
{
    $user = auth()->user(); // Ambil data user yang login

    // 1. Query Utama
    $query = DB::table('pembelian_bahan_baku')
        ->join('users', 'pembelian_bahan_baku.user_id', '=', 'users.id')
        ->leftJoin('cabang', 'pembelian_bahan_baku.cabang_id', '=', 'cabang.id')
        ->select(
            'pembelian_bahan_baku.*',
            'users.name as nama_petugas',
            'cabang.nama_cabang as nama_lokasi_cabang'
        );

    // 2. LOGIKA ROLE (Tambahkan di sini)
    if ($user->role === 'kasir') {
        // Jika Kasir, paksa hanya ambil data dari cabang tempat dia bertugas
        $query->where('pembelian_bahan_baku.cabang_id', $user->cabang_id);
    } else {
        // Jika Admin/Manager, baru boleh menggunakan filter dari request (dropdown)
        if ($request->filled('cabang_id')) {
            $query->where('pembelian_bahan_baku.cabang_id', $request->cabang_id);
        }
    }

    // 3. Filter Tanggal
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('pembelian_bahan_baku.tanggal', [$request->start_date, $request->end_date]);
    }

    // 4. Eksekusi Query
    $pembelian = $query->orderBy('pembelian_bahan_baku.created_at', 'desc')->get();

    // 5. Ambil detail bahan baku
    foreach ($pembelian as $item) {
        $item->details = DB::table('detail_pembelian_baku')
            ->join('bahan_baku', 'detail_pembelian_baku.bahan_id', '=', 'bahan_baku.id')
            ->where('detail_pembelian_baku.pembelian_id', $item->id)
            ->select('detail_pembelian_baku.*', 'bahan_baku.nama', 'bahan_baku.satuan')
            ->get();
    }

    // 6. Logika Nama Cabang untuk Judul PDF
    $namaCabang = "Semua Cabang";
    if ($user->role === 'kasir') {
        // Jika kasir, judul otomatis nama cabangnya
        $cb = DB::table('cabang')->where('id', $user->cabang_id)->first();
        $namaCabang = $cb ? $cb->nama_cabang : "Cabang";
    } elseif ($request->filled('cabang_id')) {
        // Jika admin memilih cabang tertentu
        $cb = DB::table('cabang')->where('id', $request->cabang_id)->first();
        $namaCabang = $cb ? $cb->nama_cabang : "Semua Cabang";
    }

    // 7. Generate PDF
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pembelian.pdf', [
        'pembelian' => $pembelian,
        'namaCabang' => $namaCabang
    ]);

    return $pdf->stream('Laporan-Pembelian.pdf');
}
}
