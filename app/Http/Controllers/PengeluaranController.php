<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\KategoriPengeluaran;
use App\Models\Pengeluaran;
use Illuminate\Http\Request; // Pastikan Model Cabang di-import
use Illuminate\Support\Facades\Auth;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Ambil semua cabang untuk isi dropdown filter (hanya untuk admin/manager)
        $cabangs = \App\Models\Cabang::all();

        $query = \App\Models\Pengeluaran::with(['kategori', 'user', 'cabang']);

        // LOGIKA FILTER
        // 1. Jika User adalah Kasir, kunci hanya di cabangnya saja
        if ($user->role === 'kasir') {
            $query->where('cabang_id', $user->cabang_id);
        }
        // 2. Jika Admin/Manager pilih filter cabang tertentu
        elseif ($request->has('cabang_id') && $request->cabang_id != '') {
            $query->where('cabang_id', $request->cabang_id);
        }

        // Filter Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));

        $pengeluarans = $query->latest('created_at')->paginate(10);

        return view('pengeluaran.index', compact('pengeluarans', 'cabangs', 'bulan', 'tahun'));
    }

    public function create()
    {
        $cabangs = Cabang::all();
        $kategoris = KategoriPengeluaran::all();

        return view('pengeluaran.create', compact('kategoris', 'cabangs'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // 1. Validasi data
        // Kita buat cabang_id nullable di validasi dulu agar tidak error jika tidak dikirim (untuk Kasir)
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kategori_id' => 'required|exists:kategori_pengeluaran,id',
            'cabang_id' => 'required|exists:cabang,id',
            'deskripsi' => 'nullable|string|max:255',
            'jumlah' => 'required|numeric|min:1',
        ]);

        // 2. Proteksi Logika Cabang
        // Jika admin, ambil dari form. Jika kasir, PAKSA dari data user login.
        // Sekarang Admin DAN Manager bisa memilih cabang dari form
        $fixCabangId = in_array($user->role, ['admin', 'manager'])
                        ? $request->cabang_id
                        : $user->cabang_id;

        // Pastikan jika kasir, cabang_id tidak kosong
        if (! $fixCabangId) {
            return back()->with('error', 'Akun Anda tidak terhubung ke cabang manapun.');
        }

        // 3. Simpan data
        Pengeluaran::create([
            'tanggal' => $validated['tanggal'],
            'kategori_id' => $validated['kategori_id'],
            'cabang_id' => $fixCabangId, // Gunakan variabel yang sudah diproteksi
            'deskripsi' => $validated['deskripsi'],
            'jumlah' => $validated['jumlah'],
            'user_id' => $user->id,
        ]);

        \App\Models\ActivityLog::record(
            'PENGELUARAN',
            'Mencatat pengeluaran: '.$request->deskripsi.' senilai Rp'.number_format($request->jumlah)
        );

        return redirect()->route('pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil dicatat!');
    }

    /**
     * Menampilkan form untuk mengedit pengeluaran.
     */
    // public function edit(Pengeluaran $pengeluaran)
    // {
    //     // Tetap konsisten menggunakan jamak (plural) untuk koleksi
    //     $cabangs = \App\Models\Cabang::all();
    //     $kategoris = \App\Models\KategoriPengeluaran::all();

    //     // Mengirim data tunggal ($pengeluaran) dan data jamak untuk dropdown
    //     return view('pengeluaran.edit', compact('pengeluaran', 'cabangs', 'kategoris'));
    // }

    /**
     * Memperbarui data pengeluaran di database.
     */
    // public function update(Request $request, Pengeluaran $pengeluaran)
    // {
    //     $validated = $request->validate([
    //         'tanggal' => 'required|date',
    //         'kategori_id' => 'required|exists:kategori_pengeluaran,id',
    //         'cabang_id' => 'required|exists:cabang,id',
    //         'deskripsi' => 'nullable|string|max:255',
    //         'jumlah' => 'required|numeric|min:1',
    //     ]);

    //     // Tetap pastikan user_id tidak berubah atau sesuai user yang login
    //     $validated['user_id'] = auth()->id();

    //     $pengeluaran->update($validated);

    //     return redirect()->route('pengeluaran.index')
    //         ->with('success', 'Pengeluaran berhasil diperbarui!');
    // }

    public function show($id)
    {
        // Pastikan memanggil semua relasi yang dibutuhkan view show
        $pengeluaran = Pengeluaran::with(['kategori', 'user', 'cabang'])->findOrFail($id);

        return view('pengeluaran.show', compact('pengeluaran'));
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $pengeluaran = Pengeluaran::findOrFail($id);

        // Proteksi: Kasir hanya boleh hapus datanya sendiri dalam waktu 1 jam (salah input langsung hapus)
        // Di atas itu, hanya Admin atau Manager yang bisa hapus/void.
        if ($user->role === 'kasir') {
            if ($pengeluaran->user_id !== $user->id || $pengeluaran->created_at->diffInMinutes(now()) > 60) {
                return back()->with('error', 'Sudah lewat 60 menit. Silahkan hubungi Manager untuk pembatalan (Void).');
            }
        }

        $pengeluaran->delete(); // Soft Delete

        return redirect()->route('pengeluaran.index')
            ->with('success', 'Data berhasil di-VOID (Dibatalkan). Silahkan input ulang jika ada perbaikan.');
    }

    public function trash()
    {
        // Mengambil data yang statusnya soft deleted
        $pengeluarans = \App\Models\Pengeluaran::onlyTrashed()
            ->with(['kategori', 'user', 'cabang'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('pengeluaran.trash', compact('pengeluarans'));
    }

    public function restore($id)
    {
        $pengeluaran = Pengeluaran::withTrashed()->findOrFail($id);
        $pengeluaran->restore();

        return back()->with('success', 'Data pengeluaran berhasil dikembalikan.');
    }

    public function forceDelete($id)
    {
        if (auth()->user()->role !== 'manager') {
            abort(403);
        }

        $pengeluaran = Pengeluaran::withTrashed()->findOrFail($id);
        $pengeluaran->forceDelete(); // Menghapus permanen

        return back()->with('success', 'Data dihapus permanen.');
    }
}
