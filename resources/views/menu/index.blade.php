<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Menu & Produk') }}
            </h2>
            
            {{-- Tombol Tambah Menu hanya untuk Manager --}}
            @if (auth()->user()->role === 'manager')
                <a href="{{ route('menu.create') }}"
                    class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                    <i class="fas fa-plus mr-2"></i> Tambah Menu
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">

                {{-- TAMPILAN MOBILE --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @forelse ($menus as $menu)
                        <div class="p-4 bg-white active:bg-gray-50 transition">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="text-base font-bold text-gray-900">{{ $menu->nama_menu }}</h4>
                                    <p class="text-indigo-600 font-black text-sm mt-1">
                                        Rp {{ number_format($menu->harga, 0, ',', '.') }}
                                    </p>
                                </div>
                                <span class="text-xs text-gray-300 font-mono">#{{ ($menus->currentPage() - 1) * $menus->perPage() + $loop->iteration }}</span>
                            </div>

                            <div class="flex mt-4 space-x-2">
                                @if (auth()->user()->role === 'manager')
                                    <a href="{{ route('menu.edit', $menu->id) }}"
                                        class="flex-1 text-center bg-indigo-50 text-indigo-700 px-3 py-2 rounded-lg text-xs font-bold transition border border-indigo-100">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <form action="{{ route('menu.destroy', $menu->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus?')" class="flex-1">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="w-full bg-red-50 text-red-600 px-3 py-2 rounded-lg text-xs font-bold transition border border-red-100">
                                            <i class="fas fa-trash mr-1"></i> Hapus
                                        </button>
                                    </form>
                                @else
                                    <div class="w-full text-center py-2 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                                        <span class="text-[10px] text-gray-400 italic">
                                            <i class="fas fa-lock mr-1"></i> Perubahan harga hanya oleh Manager
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center text-gray-500 text-sm">Belum ada data menu.</div>
                    @endforelse
                </div>

                {{-- TAMPILAN DESKTOP --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-16 text-center">No</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Menu</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Harga</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($menus as $menu)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-400 text-center font-mono">
                                        {{ ($menus->currentPage() - 1) * $menus->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $menu->nama_menu }}</td>
                                    <td class="px-6 py-4 text-sm font-black text-indigo-600">Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if (auth()->user()->role === 'manager')
                                            <div class="flex justify-center space-x-3">
                                                <a href="{{ route('menu.edit', $menu->id) }}" class="text-indigo-600 hover:text-indigo-900 transition" title="Edit Harga">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('menu.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Hapus menu?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-600 transition" title="Hapus Menu">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-400 rounded text-[10px] uppercase font-bold tracking-widest">
                                                <i class="fas fa-lock mr-1"></i> Locked
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100 bg-gray-50">
                    {{ $menus->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>