<?php

namespace App\Http\Controllers;

use App\Models\KategoriPengeluaran; // Pastikan nama model sesuai
use Illuminate\Http\Request;

class KategoriPengeluaranController extends Controller
{
    /**
     * Menampilkan daftar kategori
     */
    public function index()
    {
        $kategoris = KategoriPengeluaran::orderBy('nama_kategori', 'asc')->get();
        return view('kategori_pengeluaran.index', compact('kategoris'));
    }

    /**
     * Menampilkan form tambah kategori
     */
    public function create()
    {
        return view('kategori_pengeluaran.create');
    }

    /**
     * Menyimpan kategori baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_pengeluaran,nama_kategori',
        ], [
            'nama_kategori.unique' => 'Nama kategori ini sudah ada!',
            'nama_kategori.required' => 'Nama kategori wajib diisi.'
        ]);

        KategoriPengeluaran::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit kategori
     */
    public function edit($id)
    {
        $kategori = KategoriPengeluaran::findOrFail($id);
        return view('kategori_pengeluaran.edit', compact('kategori'));
    }

    /**
     * Memperbarui data kategori
     */
    public function update(Request $request, $id)
    {
        $kategori = KategoriPengeluaran::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_pengeluaran,nama_kategori,' . $id,
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Menghapus kategori
     */
    public function destroy($id)
    {
        $kategori = KategoriPengeluaran::findOrFail($id);
        
        // Cek apakah kategori sedang digunakan di tabel pengeluaran
        // Jika Anda ingin mencegah penghapusan kategori yang sudah punya data pengeluaran:
        // if ($kategori->pengeluarans()->exists()) {
        //     return back()->with('error', 'Kategori tidak bisa dihapus karena sedang digunakan.');
        // }

        $kategori->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus!');
    }
}