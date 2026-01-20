<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <h2 class="font-bold text-lg md:text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Cabang') }}
            </h2>
            <a href="{{ route('cabang.create') }}" class="w-full md:w-auto flex justify-center items-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 shadow-md transition duration-150">
                <i class="fas fa-plus mr-2"></i> Tambah Cabang
            </a>
        </div>
    </x-slot>

    <div class="py-4 md:py-12">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- --- TAMPILAN MOBILE (Card Style) --- --}}
            <div class="block md:hidden space-y-3">
                @forelse ($cabangs as $cabang)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase leading-none">Nama Cabang</span>
                                <p class="text-sm font-bold text-gray-800">{{ $cabang->nama_cabang }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('cabang.edit', $cabang->id) }}" class="p-2 bg-indigo-50 text-indigo-600 rounded-lg border border-indigo-100">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('cabang.destroy', $cabang->id) }}" method="POST" onsubmit="return confirm('Hapus cabang ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg border border-red-100">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mt-3 pt-3 border-t border-dashed border-gray-100">
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase block">Telepon</span>
                                <p class="text-xs text-gray-600 font-medium">{{ $cabang->telepon ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase block">Alamat</span>
                                <p class="text-xs text-gray-600 line-clamp-2">{{ $cabang->alamat ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-10 text-center rounded-xl text-gray-400 text-xs italic shadow-inner border">Belum ada data cabang.</div>
                @endforelse
            </div>

            {{-- --- TAMPILAN DESKTOP (Table Style) --- --}}
            <div class="hidden md:block bg-white overflow-hidden shadow-sm sm:rounded-xl p-6 border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Cabang</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Alamat</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Telepon</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($cabangs as $cabang)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $cabang->nama_cabang }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $cabang->alamat ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $cabang->telepon ?? '-' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('cabang.edit', $cabang->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-lg transition" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('cabang.destroy', $cabang->id) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1.5 rounded-lg transition" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 px-2">
                {{ $cabangs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>