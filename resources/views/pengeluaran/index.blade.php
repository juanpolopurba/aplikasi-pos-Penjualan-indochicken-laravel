<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">

            {{-- BAGIAN FILTER --}}
            <div class="mb-5 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <form action="{{ route('pengeluaran.index') }}" method="GET" class="flex flex-wrap items-end gap-3">
                    {{-- Filter Cabang --}}
                    @if (auth()->user()->role !== 'kasir')
                        <div class="w-full md:w-48">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">Cabang</label>
                            <select name="cabang_id"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-xs py-2">
                                <option value="">Semua Cabang</option>
                                @foreach ($cabangs as $cabang)
                                    <option value="{{ $cabang->id }}"
                                        {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                        {{ $cabang->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Filter Tanggal Mulai --}}
                    <div class="w-[47%] md:w-40">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">Dari
                            Tanggal</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-xs py-2">
                    </div>

                    {{-- Filter Tanggal Selesai --}}
                    <div class="w-[47%] md:w-40">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">Sampai
                            Tanggal</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-xs py-2">
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex gap-2 ml-auto md:ml-0">
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700 transition shadow-sm">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>

                        @if (request()->anyFilled(['cabang_id', 'start_date', 'end_date']))
                            <a href="{{ route('pengeluaran.index') }}"
                                class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold hover:bg-gray-200 transition">
                                Reset
                            </a>
                        @endif

                        {{-- SATU-SATUNYA TOMBOL PDF DI HALAMAN INI --}}
                        <a href="{{ route('laporan.pengeluaran_pdf', request()->query()) }}" target="_blank"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700 transition shadow-sm">
                            <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                        </a>
                    </div>
                </form>
            </div>

            {{-- JUDUL DAN TOMBOL TAMBAH --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 mt-8">
                <h2 class="font-bold text-lg md:text-xl text-gray-800 leading-tight">
                    {{ __('Riwayat Pengeluaran') }}
                </h2>

                <div class="flex gap-2 w-full md:w-auto">
                    @if (auth()->user()->role === 'manager' || auth()->user()->role === 'admin')
                        <a href="{{ route('pengeluaran.trash') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-lg font-bold text-[10px] text-white uppercase tracking-widest hover:bg-gray-700 shadow-md transition">
                            <i class="fas fa-trash-restore mr-2"></i> Audit Sampah
                        </a>
                    @endif

                    {{-- Hanya Admin dan Manager yang bisa tambah pengeluaran umum --}}
                    @if (auth()->user()->role !== 'kasir')
                        <a href="{{ route('pengeluaran.create') }}"
                            class="flex-1 md:flex-none flex justify-center items-center px-4 py-2.5 bg-red-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-red-700 shadow-md transition duration-150">
                            <i class="fas fa-plus mr-2"></i> Tambah Pengeluaran
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-4 md:py-12">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">

            {{-- TAMPILAN MOBILE (Card Style) --}}
            <div class="block md:hidden space-y-3">
                @forelse ($pengeluarans as $p)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase italic">#{{ $p->id }}
                                    - Tanggal</span>
                                <p class="text-xs font-bold text-gray-800">
                                    {{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</p>
                            </div>
                            <span
                                class="px-2 py-1 text-[10px] font-black rounded-lg bg-blue-50 text-blue-700 uppercase border border-blue-100">
                                {{ $p->kategori->nama_kategori ?? 'Umum' }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <span class="text-[10px] font-bold text-indigo-600 uppercase italic">
                                <i class="fas fa-building mr-1"></i> {{ $p->cabang->nama_cabang ?? 'Pusat' }}
                            </span>

                            <span class="text-[10px] font-bold text-gray-400 uppercase italic ml-2">
                                <i class="fas fa-user mr-1"></i> {{ $p->user->name ?? 'System' }}
                            </span>
                            <p class="text-sm text-gray-700 font-medium leading-tight mt-1">"{{ $p->deskripsi }}"</p>
                        </div>

                        <div class="flex justify-between items-end border-t border-dashed pt-3">
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase">Total</span>
                                <p class="text-xl font-black text-red-600 leading-none">Rp
                                    {{ number_format($p->jumlah, 0, ',', '.') }}</p>
                            </div>

                            <div class="flex space-x-2">
                                <a href="{{ route('pengeluaran.show', $p->id) }}"
                                    class="p-2 bg-gray-100 text-gray-600 rounded-lg border border-gray-200">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if (Auth::user()->role === 'admin')
                                    <form action="{{ route('pengeluaran.destroy', $p->id) }}" method="POST"
                                        onsubmit="return confirm('Batalkan pengeluaran ini? Data akan dipindahkan ke folder sampah.')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="p-2 bg-red-50 text-red-500 rounded-lg border border-red-100">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-10 text-center rounded-xl text-gray-400 text-xs italic shadow-inner border">
                        Belum ada catatan pengeluaran.
                    </div>
                @endforelse
            </div>

            {{-- TAMPILAN DESKTOP (Table Style) --}}
            <div class="hidden md:block bg-white shadow-sm sm:rounded-xl border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 table-fixed">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="w-24 px-4 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">Waktu
                            </th>
                            <th class="w-32 px-4 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">Info</th>
                            <th class="w-32 px-4 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">Kategori
                            </th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">Deskripsi</th>
                            <th class="w-32 px-4 py-3 text-right text-[10px] font-bold text-gray-500 uppercase">Jumlah
                            </th>
                            <th class="w-20 px-4 py-3 text-center text-[10px] font-bold text-gray-500 uppercase">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach ($pengeluarans as $p)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-xs font-bold text-gray-700">
                                        {{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/y') }}</div>
                                    <div class="text-[9px] text-gray-400">
                                        {{ \Carbon\Carbon::parse($p->created_at)->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-xs font-bold text-indigo-700 truncate">
                                        {{ $p->cabang->nama_cabang ?? 'Pusat' }}</div>
                                    <div class="text-[10px] text-gray-500 truncate">{{ $p->user->name ?? 'System' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-block px-2 py-0.5 text-[9px] font-bold rounded bg-blue-50 text-blue-700 border border-blue-100 uppercase">
                                        {{ $p->kategori->nama_kategori ?? 'Umum' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-xs text-gray-600 leading-tight break-words line-clamp-2"
                                        title="{{ $p->deskripsi }}">
                                        {{ $p->deskripsi }}
                                    </p>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <span class="text-xs font-black text-red-600">
                                        {{ number_format($p->jumlah, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <a href="{{ route('pengeluaran.show', $p->id) }}"
                                            class="text-gray-400 hover:text-indigo-600">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        @if (Auth::user()->role === 'admin' || Auth::user()->role === 'manager')
                                            <form action="{{ route('pengeluaran.destroy', $p->id) }}" method="POST"
                                                onsubmit="return confirm('Hapus?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-gray-300 hover:text-red-500">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 px-2">
                {{ $pengeluarans->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
