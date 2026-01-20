<?php

namespace App\Http\Controllers;

use App\Models\Cabang; // Model User standar Laravel
use App\Models\User; // Diperlukan untuk dropdown Cabang
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna.
     */
    public function index()
    {
        // Ambil semua pengguna dengan relasi cabang
        $users = User::with('cabang')->latest()->paginate(10);

        $usersTerhapus = User::onlyTrashed()->get();

        // Kembalikan view index.blade.php di folder user
        return view('user.index', compact('users', 'usersTerhapus'));
    }

    /**
     * Menampilkan form untuk membuat pengguna baru.
     */
    public function create()
    {
        // Ambil data cabang untuk dropdown
        $cabangs = Cabang::all();
        // Definisikan roles yang tersedia (sesuaikan dengan aplikasi Anda)
        $roles = ['admin', 'manager', 'kasir'];

        return view('user.create', compact('cabangs', 'roles'));
    }

    /**
     * Menyimpan data pengguna baru dari form.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:8',
            'role' => 'required',
            'cabang_id' => 'nullable|exists:cabang,id', // Pastikan nama tabel benar 'cabangs'
        ]);

        // 2. Simpan Data User (Hanya satu kali panggil Create)
        $user = \App\Models\User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => $request->password,
            'role' => $request->role,
            'cabang_id' => $request->cabang_id,
            'created_by' => auth()->id(), // Mencatat siapa pembuatnya (Admin/Owner)
        ]);

        // 3. CATAT LOG AKTIVITAS (Activity Log)
        // Pastikan Model ActivityLog sudah dibuat
        if (class_exists('\App\Models\ActivityLog')) {
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'aksi' => 'TAMBAH USER',
                'keterangan' => 'Menambah user baru: '.$user->username.' dengan role: '.$user->role,
                'ip_address' => $request->ip(),
            ]);
        }

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit data pengguna.
     */
    public function edit(User $user)
    {
        $cabangs = Cabang::all();
        $roles = ['admin', 'manager', 'kasir'];

        return view('user.edit', compact('user', 'cabangs', 'roles'));
    }

    /**
     * Memperbarui data pengguna yang sudah ada.
     */
    public function update(Request $request, User $user)
    {
        // 1. Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            // Ganti email menjadi username sesuai input di Blade
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'cabang_id' => 'nullable|exists:cabang,id', // Ubah ke nullable jika admin tidak wajib punya cabang
            'role' => ['required', \Illuminate\Validation\Rule::in(['admin', 'manager', 'kasir'])],
        ]);

        // 2. Siapkan data
        $data = [
            'name' => $request->name,
            'username' => $request->username, // Gunakan username
            'cabang_id' => $request->cabang_id,
            'role' => $request->role,
        ];

        // Jika password diisi, baru kita update
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        // 3. Eksekusi Update
        $user->update($data);

        return redirect()->route('user.index')->with('success', 'Pengguna berhasil diperbarui!');
    }

    /**
     * Menghapus data pengguna.
     */
    public function destroy(User $user)
    {
        // Cek apakah user yang dihapus adalah user yang sedang login (Penting!)
        if (auth()->user()->id === $user->id) {
            return redirect()->route('user.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if (auth()->user()->role !== 'manager') {
            return back()->with('error', 'Hanya Owner yang berhak menghapus user!');
        }

        \App\Models\ActivityLog::record(
            'HAPUS USER',
            'Menghapus user: '.$user->username
        );

        $user->delete();

        return redirect()->route('user.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function restore($id)
    {
        // Cari user termasuk yang sudah di-softdelete
        $user = \App\Models\User::withTrashed()->find($id);

        if (! $user) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Proses mengembalikan data
        $user->restore();

        return redirect()->route('user.index')->with('success', 'User berhasil aktif kembali.');
    }
}
