<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <h2 class="font-bold text-lg md:text-xl text-gray-800 leading-tight">Riwayat Pembelian</h2>

            <div class="flex gap-2 w-full md:w-auto">
                {{-- File: resources/views/pembelian/index.blade.php --}}

                <a href="/cetak-pembelian-pdf?cabang_id={{ request('cabang_id') }}&start_date={{ request('start_date') }}&end_date={{ request('end_date') }}"
                    target="_blank"
                    class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Cetak PDF
                </a>
                <a href="{{ route('pembelian.create') }}"
                    class="w-full md:w-auto text-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg font-bold text-xs uppercase tracking-wider shadow-sm">
                    + Input Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4 md:py-8">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
                <form action="{{ route('pembelian.index') }}" method="GET"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                    {{-- Filter Cabang (Hanya tampil untuk Admin/Manager) --}}
                    @if (in_array(auth()->user()->role, ['admin', 'manager']))
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Cabang</label>
                            <select name="cabang_id"
                                class="w-full border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
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
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="w-full border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- Filter Tanggal Selesai --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="w-full border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-indigo-700 transition">
                            Filter
                        </button>
                        <a href="{{ route('pembelian.index') }}"
                            class="flex-1 bg-gray-100 text-center text-gray-600 px-4 py-2 rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-gray-200 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- VIEW UNTUK MOBILE --}}
            <div class="block md:hidden space-y-3">
                @forelse($pembelian as $p)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex justify-between items-start border-b pb-2 mb-2">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Tanggal</p>
                                <p class="text-xs font-bold text-gray-800">
                                    {{ date('d/m/Y H:i', strtotime($p->created_at)) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Total Bayar</p>
                                <p class="text-sm font-black text-emerald-600">
                                    Rp{{ number_format($p->total_pembelian ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        {{-- Info Bahan di Mobile --}}
                        <div class="mb-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Bahan Yang Dibeli</p>
                            <p class="text-xs text-indigo-700 font-bold leading-tight">
                                {{ $p->daftar_bahan ?? 'Tidak ada data' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Supplier</p>
                                <p class="text-xs text-gray-700 font-medium">{{ $p->supplier }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Cabang</p>
                                <p class="text-xs text-gray-700 font-medium">{{ $p->nama_cabang }}</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center bg-gray-50 -mx-4 -mb-4 p-3 rounded-b-xl">
                            <span class="text-[10px] text-gray-500 uppercase font-bold italic">
                                <i class="fas fa-user-circle mr-1"></i> Petugas:
                                {{ $p->nama_penginput ?? $p->user->name }}
                            </span>
                            <a href="{{ route('pembelian.show', $p->id) }}"
                                class="text-xs font-bold text-indigo-600 uppercase tracking-tighter">
                                Lihat Detail →
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 text-center rounded-xl text-gray-400 text-xs italic">Data tidak ditemukan.
                    </div>
                @endforelse
            </div>

            {{-- VIEW UNTUK DESKTOP --}}
            <div class="hidden md:block bg-white shadow-sm sm:rounded-xl overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Petugas</th>

                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Supplier</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Bahan</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Cabang</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Total</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($pembelian as $p)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ date('d/m/Y H:i', strtotime($p->created_at)) }}</td>
                                      <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $p->nama_penginput ?? $p->user->name }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $p->supplier }}</td>
                                {{-- Kolom Bahan di Desktop --}}
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="max-w-xs truncate font-semibold text-indigo-600"
                                        title="{{ $p->daftar_bahan }}">
                                        {{ $p->daftar_bahan ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">{{ $p->nama_cabang }}</td>
                                <td class="px-6 py-4 text-sm text-right font-black text-emerald-600">
                                    Rp{{ number_format($p->total_pembelian ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('pembelian.show', $p->id) }}"
                                        class="text-indigo-600 font-bold hover:underline">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 px-2">
                {{ $pembelian->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
