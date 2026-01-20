<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Pengguna') }}
            </h2>
            <a href="{{ route('user.create') }}"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold uppercase">
                Tambah User
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm font-bold">
                        {{ session('success') }}
                    </div>
                @endif
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Username</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Cabang</th>
                            {{-- Tambahkan Header Aksi --}}
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-6 py-4 text-sm">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-sm">{{ $user->username }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span
                                        class="px-2 py-1 rounded-full text-[10px] font-bold {{ $user->role == 'admin' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ strtoupper($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">{{ $user->cabang->nama_cabang ?? '-' }}</td>

                                {{-- Tambahkan Tombol Aksi --}}
                                <td class="px-6 py-4 text-sm text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('user.edit', $user->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </a>

                                        @if (auth()->id() !== $user->id)
                                            <form action="{{ route('user.destroy', $user->id) }}" method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- TABEL USER AKTIF --}}
                <div class="mb-8">
                    <h3 class="text-lg font-bold mb-4">Pengguna Aktif</h3>
                    {{-- ... Tabel User Anda yang sudah ada ... --}}
                </div>

                {{-- TABEL USER TERHAPUS (ARSIP) --}}
                @if ($usersTerhapus->count() > 0)
                    <div class="mt-12 bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <h3 class="text-md font-bold text-gray-600 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Arsip Pengguna (Dinonaktifkan)
                        </h3>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl
                                        Hapus</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($usersTerhapus as $u)
                                    <tr class="opacity-60 italic">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $u->username }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ strtoupper($u->role) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $u->deleted_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                            <form action="{{ url('user/' . $u->id . '/restore') }}" method="POST">
                                                @csrf
                                                <button type="submit">Pulihkan</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
