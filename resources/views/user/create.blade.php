<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg text-gray-800 leading-tight">
            {{ __('Tambah Pengguna Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            <a href="{{ route('user.index') }}"
                class="inline-flex items-center text-sm text-gray-500 hover:text-indigo-600 mb-4 transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar User
            </a>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="p-6 md:p-8">
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                            <p class="font-bold text-xs uppercase mb-2">Terjadi Kesalahan:</p>
                            <ul class="list-disc list-inside text-xs">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('user.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Nama
                                    Lengkap</label>
                                <input type="text" name="name"
                                    class="block w-full rounded-xl border-gray-200 text-sm p-3 focus:ring-indigo-500"
                                    placeholder="Contoh: Budi Santoso" required>
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Username</label>
                                <input type="text" name="username"
                                    class="block w-full rounded-xl border-gray-200 text-sm p-3 focus:ring-indigo-500"
                                    placeholder="budi_indociken" required>
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Role
                                    / Jabatan</label>
                                <select name="role"
                                    class="block w-full rounded-xl border-gray-200 text-sm p-3 focus:ring-indigo-500">
                                    <option value="admin">Admin (Pusat)</option>
                                    <option value="manager">Manager</option>
                                    <option value="kasir">Kasir</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Penempatan
                                    Cabang</label>
                                <select name="cabang_id"
                                    class="block w-full rounded-xl border-gray-200 text-sm p-3 focus:ring-indigo-500">
                                    <option value="">-- Pilih Cabang --</option>
                                    @foreach ($cabangs as $c)
                                        <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Password</label>
                                <input type="password" name="password"
                                    class="block w-full rounded-xl border-gray-200 text-sm p-3 focus:ring-indigo-500"
                                    placeholder="Minimal 8 karakter" required>
                            </div>
                        </div>

                        <div class="mt-8">
                            <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 shadow-lg transition duration-150">
                                <i class="fas fa-user-plus mr-2"></i> Daftarkan User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
