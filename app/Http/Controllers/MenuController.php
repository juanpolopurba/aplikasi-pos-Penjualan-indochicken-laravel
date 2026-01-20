<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Menampilkan daftar menu/produk.
     */
    public function index()
    {
        $menus = Menu::latest()->paginate(10);

        return view('menu.index', compact('menus'));
    }

    /**
     * Form tambah menu.
     */
    public function create()
    {
        return view('menu.create');
    }

    /**
     * Simpan menu baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'kategori' => 'required|string', // Contoh: Makanan, Minuman, Snack
            'status' => 'required|in:tersedia,habis',
        ]);

        Menu::create($request->all());

        return redirect()->route('menu.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    /**
     * Form edit menu.
     */
    public function edit($id)
    {
        if (auth()->user()->role !== 'manager') {
            abort(403, 'Hanya Manager yang boleh mengubah harga menu!');
        }

        $menu = Menu::findOrFail($id);

        return view('menu.edit', compact('menu'));
    }

    /**
     * Update data menu.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'kategori' => 'required|string',
            'status' => 'required|in:tersedia,habis',
        ]);

        $menu->update($request->all());

        return redirect()->route('menu.index')->with('success', 'Menu berhasil diperbarui!');
    }

    /**
     * Hapus menu.
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();

        return redirect()->route('menu.index')->with('success', 'Menu berhasil dihapus.');
    }
}
