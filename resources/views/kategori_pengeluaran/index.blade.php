<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <h2 class="font-bold text-lg md:text-xl text-gray-800 leading-tight">
                {{ __('Kategori Pengeluaran') }}
            </h2>
            <a href="{{ route('kategori.create') }}" class="w-full md:w-auto flex justify-center items-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 shadow-md transition duration-150">
                <i class="fas fa-plus mr-2"></i> Tambah Kategori
            </a>
        </div>
    </x-slot>

    <div class="py-6 md:py-12">
        <div class="max-w-4xl mx-auto px-2 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-6">
                    {{-- Tabel Responsive --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">No</th>
                                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama Kategori</th>
                                    <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($kategoris as $k)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800 uppercase">
                                            {{ $k->nama_kategori }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex justify-center space-x-2">
                                                {{-- Edit --}}
                                                <a href="{{ route('kategori.edit', $k->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-lg">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                {{-- Hapus --}}
                                                <form action="{{ route('kategori.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini? Pengeluaran dengan kategori ini mungkin akan bermasalah.')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1.5 rounded-lg">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-10 text-center text-gray-400 italic text-sm">
                                            Belum ada kategori pengeluaran.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            {{-- Info Tambahan --}}
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-100 rounded-xl flex items-start gap-3">
                <i class="fas fa-info-circle text-yellow-600 mt-1"></i>
                <p class="text-xs text-yellow-700 leading-relaxed">
                    <strong>Tips:</strong> Gunakan kategori yang spesifik (seperti: Bahan Baku, Listrik, Gaji Karyawan) untuk membantu Anda menganalisis laporan laba rugi dengan lebih detail di dashboard.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>