<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('pengeluaran.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="font-bold text-lg md:text-xl text-gray-800 leading-tight">
                Detail Pengeluaran
            </h2>
        </div>
    </x-slot>

    <div class="py-8 px-4">
        <div class="max-w-lg mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Header Nominal --}}
            <div class="bg-gray-50 p-6 text-center border-b border-dashed">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Pengeluaran</p>
                <h1 class="text-4xl font-black text-red-600">
                    Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}
                </h1>
            </div>

            {{-- Detail Informasi --}}
            <div class="p-6 space-y-5">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-400">Tanggal</span>
                    <span class="text-sm font-bold text-gray-800">
                        {{ \Carbon\Carbon::parse($pengeluaran->tanggal)->format('d F Y') }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-400">Kategori</span>
                    <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-black uppercase rounded-full border border-blue-100">
                        {{ $pengeluaran->kategori->nama_kategori ?? 'Umum' }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-400">Cabang</span>
                    <span class="text-sm font-bold text-gray-700">
                        {{ $pengeluaran->cabang->nama_cabang ?? 'Pusat' }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-400">Dicatat Oleh</span>
                    <span class="text-sm font-medium text-gray-600 italic">
                        {{ $pengeluaran->user->name ?? ($pengeluaran->user->username ?? 'System') }}
                    </span>
                </div>

                <div class="pt-4 border-t border-gray-50">
                    <span class="text-xs font-bold text-gray-400 uppercase block mb-2">Keterangan / Deskripsi</span>
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 text-gray-700 text-sm leading-relaxed">
                        "{{ $pengeluaran->deskripsi ?? 'Tidak ada deskripsi' }}"
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="p-6 bg-gray-50 flex gap-3">
                @if (in_array(auth()->user()->role, ['admin', 'manager']))
                    {{-- Tombol Void: Hanya untuk Admin & Manager --}}
                    <form action="{{ route('pengeluaran.destroy', $pengeluaran->id) }}" method="POST" class="flex-1"
                        onsubmit="return confirm('Batalkan pengeluaran ini? Data akan dipindahkan ke folder sampah.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full bg-red-600 text-white text-center py-3 rounded-xl font-bold text-sm shadow-sm hover:bg-red-700 transition">
                            <i class="fas fa-ban mr-2"></i> Void (Hapus)
                        </button>
                    </form>
                @endif

                {{-- Tombol Kembali: Muncul untuk semua role --}}
                <a href="{{ route('pengeluaran.index') }}"
                    class="flex-1 bg-white text-gray-600 border border-gray-200 text-center py-3 rounded-xl font-bold text-sm shadow-sm hover:bg-gray-50 transition flex items-center justify-center">
                    <i class="fas fa-arrow-left mr-2 md:hidden"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</x-app-layout>