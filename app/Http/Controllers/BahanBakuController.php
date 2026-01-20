<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BahanBakuController extends Controller
{
    /**
     * TAMPILAN INDEX: Menggabungkan Master Bahan + Stok di Inventory + Nama Cabang
     */
   public function index(Request $request) // Tambahkan Request $request di sini
{
    $user = auth()->user();

    $query = DB::table('bahan_baku')
        ->leftJoin('inventory', 'bahan_baku.id', '=', 'inventory.bahan_id')
        ->leftJoin('cabang', 'inventory.cabang_id', '=', 'cabang.id')
        ->select(
            'bahan_baku.id',
            'bahan_baku.nama',
            'bahan_baku.satuan',
            'inventory.stok_saat_ini',
            'cabang.nama_cabang',
            'inventory.cabang_id'
        );

    // 1. LOGIKA AKSES & FILTER DROPDOWN
    if ($user->role !== 'admin' && $user->role !== 'manager') {
        // Jika Kasir, paksa hanya melihat cabangnya sendiri
        $query->where('inventory.cabang_id', $user->cabang_id);
    } else {
        // Jika Admin/Manager, cek apakah dia sedang memilih cabang tertentu di dropdown
        if ($request->filled('cabang_id')) {
            $query->where('inventory.cabang_id', $request->cabang_id);
        }
    }

    $bahanBaku = $query->orderBy('bahan_baku.nama', 'asc')->paginate(10);

    // 2. AMBIL DATA SEMUA CABANG (Untuk isi pilihan di Dropdown)
    // Hanya Admin/Manager yang perlu list ini
    $listCabang = [];
    if ($user->role === 'admin' || $user->role === 'manager') {
        $listCabang = DB::table('cabang')->orderBy('nama_cabang', 'asc')->get();
    }

    return view('bahan_baku.index', compact('bahanBaku', 'listCabang'));
}

    public function create()
    {
        $cabangs = Cabang::all();

        return view('bahan_baku.create', compact('cabangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'satuan' => 'required|string|max:10',
            'stok_saat_ini' => 'required|numeric|min:0',
            'cabang_id' => 'required|exists:cabang,id',
        ]);

        try {
            DB::beginTransaction();

            // 1. Cek apakah bahan dengan nama ini sudah ada?
            $bahan = DB::table('bahan_baku')->where('nama', $request->nama)->first();

            if (! $bahan) {
                // Jika belum ada, buat baru di tabel bahan_baku
                $bahanId = DB::table('bahan_baku')->insertGetId([
                    'nama' => $request->nama,
                    'satuan' => $request->satuan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // Jika sudah ada, ambil ID yang sudah ada
                $bahanId = $bahan->id;
            }

            // 2. Simpan atau Update ke tabel inventory
            // Karena bahan_id + cabang_id adalah PK, kita gunakan updateOrInsert
            DB::table('inventory')->updateOrInsert(
                ['bahan_id' => $bahanId, 'cabang_id' => $request->cabang_id],
                [
                    'stok_saat_ini' => $request->stok_saat_ini,
                    'updated_at' => now(),
                    'created_at' => now(), // hanya diisi jika data baru
                ]
            );

            DB::commit();

            return redirect()->route('bahan_baku.index')->with('success', 'Stok bahan berhasil diperbarui di cabang tersebut.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Gagal simpan: '.$e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $cabangs = DB::table('cabang')->get();
        $bahanBaku = DB::table('bahan_baku')
            ->leftJoin('inventory', 'bahan_baku.id', '=', 'inventory.bahan_id')
            ->select('bahan_baku.*', 'inventory.stok_saat_ini', 'inventory.cabang_id')
            ->where('bahan_baku.id', $id)
            ->first();

        return view('bahan_baku.edit', compact('bahanBaku', 'cabangs'));
    }

    /**
     * UPDATE: Memperbarui data di tabel bahan_baku DAN inventory
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'stok' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:10',
            'cabang_id' => 'required|exists:cabang,id',
        ]);

        try {
            DB::beginTransaction();

            // 1. UPDATE TABEL BAHAN BAKU (Tambahkan baris stok di sini)
            DB::table('bahan_baku')->where('id', $id)->update([
                'nama' => $request->nama,
                'satuan' => $request->satuan,
                // 'stok' => $request->stok, // BARIS INI YANG KURANG
                // 'cabang_id' => $request->cabang_id,
                'updated_at' => now(),
            ]);

            // 2. Update Tabel Inventory (Sudah benar)
            DB::table('inventory')->updateOrInsert(
                ['bahan_id' => $id],
                [
                    'cabang_id' => $request->cabang_id,
                    'stok_saat_ini' => $request->stok,
                    'updated_at' => now(),
                ]
            );

            DB::commit();

            return redirect()->route('bahan_baku.index')->with('success', 'Data berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::table('inventory')->where('bahan_id', $id)->delete();
        DB::table('bahan_baku')->where('id', $id)->delete();

        return redirect()->route('bahan_baku.index')->with('success', 'Data dihapus.');
    }
}
