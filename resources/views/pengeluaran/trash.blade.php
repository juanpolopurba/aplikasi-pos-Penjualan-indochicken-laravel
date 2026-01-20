<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('Sampah Pengeluaran') }}
            </h2>
            <a href="{{ route('pengeluaran.index') }}" class="text-xs font-bold text-indigo-600 uppercase tracking-widest hover:underline">
                ← Kembali ke Riwayat
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-red-100">
                <div class="p-4 bg-red-50 border-b border-red-100">
                    <p class="text-[10px] font-black text-red-600 uppercase tracking-widest">Daftar Pengeluaran yang Dihapus</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase">Tanggal Hapus</th>
                                <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase">Deskripsi</th>
                                <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-400 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($pengeluarans as $p)
                                <tr class="hover:bg-red-50/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                        {{ $p->deleted_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <span class="block font-bold">{{ $p->kategori->nama_kategori ?? 'Umum' }}</span>
                                        <span class="text-xs italic text-gray-400">"{{ $p->deskripsi }}"</span>
                                    </td>
                                    <td class="px-6 py-4 font-black text-gray-900 whitespace-nowrap">
                                        Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center space-x-2">
                                            {{-- Tombol Restore --}}
                                            <form action="{{ route('pengeluaran.restore', $p->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-green-50 text-green-600 rounded-lg text-[10px] font-bold uppercase hover:bg-green-600 hover:text-white transition-all border border-green-100">
                                                    Pulihkan
                                                </button>
                                            </form>

                                            {{-- Tombol Hapus Permanen --}}
                                            <form action="{{ route('pengeluaran.forceDelete', $p->id) }}" method="POST" onsubmit="return confirm('Hapus permanen? Data tidak bisa dikembalikan lagi!')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 bg-white text-red-600 rounded-lg text-[10px] font-bold uppercase hover:bg-red-600 hover:text-white transition-all border border-red-100">
                                                    Hapus Permanen
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic text-sm">
                                        Kotak sampah kosong.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">
                {{ $pengeluarans->links() }}
            </div>
        </div>
    </div>
</x-app-layout>