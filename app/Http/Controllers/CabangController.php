<?php

namespace App\Http\Controllers;

use App\Models\Cabang; // Pastikan Anda memiliki model Cabang
use Illuminate\Http\Request;

class CabangController extends Controller
{
    /**
     * Menampilkan daftar semua data cabang.
     */
    public function index()
    {
        // Ambil semua data cabang dan lakukan pagination
        $cabangs = Cabang::latest()->paginate(10);
        
        // Kembalikan view index.blade.php di folder cabang
        return view('cabang.index', compact('cabangs'));
    }

    /**
     * Menampilkan form untuk membuat cabang baru.
     */
    public function create()
    {
        return view('cabang.create');
    }

    /**
     * Menyimpan data cabang baru dari form.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'nama_cabang' => 'required|string|max:255|unique:cabang',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
        ]);

        // 2. Simpan data
        Cabang::create([
            'nama_cabang' => $request->nama_cabang,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
        ]);

        // 3. Redirect dengan pesan sukses
        return redirect()->route('cabang.index')->with('success', 'Data cabang berhasil ditambahkan!');
    }

    /**
     * Menampilkan data cabang tertentu (opsional).
     */
    public function show(Cabang $cabang)
    {
        return view('cabang.show', compact('cabang'));
    }

    /**
     * Menampilkan form untuk mengedit data cabang.
     */
    public function edit(Cabang $cabang)
    {
        return view('cabang.edit', compact('cabang'));
    }

    /**
     * Memperbarui data cabang yang sudah ada.
     */
    public function update(Request $request, Cabang $cabang)
    {
        // 1. Validasi input
        $request->validate([
            // Unique harus mengabaikan ID cabang yang sedang diedit
            'nama_cabang' => 'required|string|max:255|unique:cabang,nama_cabang,' . $cabang->id,
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
        ]);

        // 2. Perbarui data
        $cabang->update([
            'nama_cabang' => $request->nama_cabang,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
        ]);

        // 3. Redirect dengan pesan sukses
        return redirect()->route('cabang.index')->with('success', 'Data cabang berhasil diperbarui!');
    }

    /**
     * Menghapus data cabang.
     */
    public function destroy(Cabang $cabang)
    {
        // Tambahkan logika pengecekan di sini jika cabang memiliki relasi (misal: user, laporan)
        // Jika ada relasi, jangan dihapus, berikan pesan error.
        
        $cabang->delete();

        return redirect()->route('cabang.index')->with('success', 'Cabang berhasil dihapus.');
    }
}