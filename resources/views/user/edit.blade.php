<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Edit Pengguna: ') }} {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('user.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Username</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500"
                            required>
                        @error('username')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Role Akses</label>
                            <select name="role"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}"
                                        {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                        {{ strtoupper($role) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Penempatan Cabang</label>
                            <select name="cabang_id"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Pilih Cabang --</option>
                                @foreach ($cabangs as $cabang)
                                    <option value="{{ $cabang->id }}"
                                        {{ old('cabang_id', $user->cabang_id) == $cabang->id ? 'selected' : '' }}>
                                        {{ $cabang->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-100">

                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100 mb-6">
                        <h3 class="text-sm font-bold text-yellow-800 mb-1 italic">Ganti Password (Opsional)</h3>
                        <p class="text-xs text-yellow-700 mb-3">Kosongkan kolom di bawah ini jika tidak ingin mengubah
                            password user.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="password" name="password" autocomplete="new-password"
                                class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                                placeholder="********">

                            <input type="password" name="password_confirmation" autocomplete="new-password"
                                class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                                placeholder="********">
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('user.index') }}"
                            class="px-6 py-2 border border-gray-300 text-gray-600 rounded-lg font-bold text-sm hover:bg-gray-50 transition">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-bold text-sm hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
