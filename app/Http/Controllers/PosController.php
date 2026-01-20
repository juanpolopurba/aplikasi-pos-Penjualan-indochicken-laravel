<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use App\Models\LaporanPenjualan;
use App\Models\DetailPenjualan;
use App\Models\Cabang;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;

class PosController extends Controller
{
    
    public function create(): View
    {
        $menus = Menu::where('is_active', true)->get();
        // Ambil semua cabang yang aktif (asumsi ada kolom 'is_aktif')
        $cabangs = Cabang::where('is_aktif', true)->get(); 
        
        // Kirim $cabangs ke view
        return view('pos.create', compact('menus', 'cabangs'));
    }

    /**
     * Menyimpan transaksi penjualan baru.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi Data
        $request->validate([
            // Pastikan nama tabel di sini benar (cabang/cabangs)
            'cabang_id' => 'required|exists:cabang,id', 
            'items' => 'required|array|min:1', 
            'items.*.menu_id' => 'required|exists:menu,id',
            'items.*.qty' => 'required|integer|min:1',
            'catatan' => 'nullable|string|max:500',
        ]);
        
        // Menggunakan Database Transaction untuk memastikan data konsisten
        DB::beginTransaction();

        try {
            $user = Auth::user();
            $totalHarga = 0;
            
            // Ambil ID menu yang terlibat dari input form
            $menuIds = collect($request->items)->pluck('menu_id')->unique();
            // Ambil harga menu dari database (SUMBER KEBENARAN)
            $menusData = Menu::whereIn('id', $menuIds)->pluck('harga', 'id'); 

            // Hitung total harga final dari item yang diterima
            foreach ($request->items as $item) {
                // Ambil harga dari database
                $harga = $menusData[$item['menu_id']] ?? 0; 
                $totalHarga += ($harga * $item['qty']);
            }

            // 2. Simpan Laporan Penjualan (Header/Transaksi Utama)
            $laporan = LaporanPenjualan::create([
                'cabang_id' => $request->cabang_id, 
                'tanggal' => Carbon::today()->toDateString(),
                'total_penjualan' => $totalHarga,
                'catatan' => $request->catatan,
                'user_id' => auth()->id(),
            ]);

            // 3. Siapkan data Detail Penjualan
            $details = [];
            $now = Carbon::now();
            foreach ($request->items as $item) {
                $harga = $menusData[$item['menu_id']] ?? 0; // Harga dari DB
                
                $details[] = [
                    'laporan_id' => $laporan->id,
                    'menu_id' => $item['menu_id'],
                    'jumlah_terjual' => $item['qty'],
                    'subtotal' => $harga * $item['qty'],
                    // Penting untuk Bulk Insert
                    'created_at' => $now, 
                    'updated_at' => $now,
                ];
            }
            
            // 4. Bulk Insert Detail Penjualan
            DetailPenjualan::insert($details); 

            DB::commit(); // Selesai, simpan ke database

            return redirect()->route('dashboard')->with('success', 'Transaksi penjualan senilai Rp ' . number_format($totalHarga) . ' berhasil dicatat!');

        } catch (\Exception $e) {
            DB::rollBack(); // Jika ada error, batalkan semua perubahan
            // Tambahkan dd() sementara di sini jika error persisten untuk melihat detail error:
            // dd($e->getMessage()); 
            return redirect()->back()->with('error', 'Gagal mencatat transaksi: ' . $e->getMessage())->withInput();
        }
    }
}