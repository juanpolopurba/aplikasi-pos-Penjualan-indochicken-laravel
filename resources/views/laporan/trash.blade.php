<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sampah Laporan (Trash)</h2>
            <a href="{{ route('laporan.penjualan.index') }}" class="text-sm text-indigo-600 font-bold uppercase">Kembali</a>
        </div>
    </x-slot>

    <div class="flex flex-col gap-3">
        @forelse ($laporans as $laporan)
            <div class="bg-red-50 border border-red-100 rounded-xl p-4 md:p-3">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                    <div class="md:col-span-2">
                        <span class="text-[10px] font-bold text-red-400">#{{ $laporan->id }}</span><br>
                        <span class="text-sm font-black text-gray-800">{{ $laporan->cabang->nama_cabang ?? 'Pusat' }}</span>
                    </div>
                    <div class="md:col-span-3 text-center">
                        <span class="text-[10px] text-gray-400 uppercase block">Dihapus Pada</span>
                        <span class="text-xs font-medium text-gray-600">{{ $laporan->deleted_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="md:col-span-3 text-right">
                        <span class="text-sm font-black text-red-600">Rp{{ number_format($laporan->total_penjualan, 0, ',', '.') }}</span>
                    </div>
                    <div class="md:col-span-4 flex justify-end gap-2">
                        {{-- Tombol Restore --}}
                        <form action="{{ route('laporan.penjualan.restore', $laporan->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-green-600 text-white px-4 py-1.5 rounded-lg text-[10px] font-bold uppercase">Restore</button>
                        </form>
                        
                        {{-- Tombol Hapus Permanen --}}
                        <form action="{{ route('laporan.penjualan.forceDelete', $laporan->id) }}" method="POST" onsubmit="return confirm('Hapus PERMANEN? Data tidak bisa kembali lagi!')">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-4 py-1.5 rounded-lg text-[10px] font-bold uppercase">Hapus Permanen</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-gray-50 rounded-xl p-10 text-center border border-dashed border-gray-200">
                <p class="text-sm text-gray-400 font-medium uppercase">Trash Kosong</p>
            </div>
        @endforelse
    </div>
</x-app-layout>