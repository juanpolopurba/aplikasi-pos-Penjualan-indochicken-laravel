<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Stok Bahan Baku') }}
            </h2>

            {{-- TOMBOL TAMBAH: Mobile Full Width --}}
            @if (in_array(Auth::user()->role, ['admin', 'manager']))
                <a href="{{ route('bahan_baku.create') }}"
                    class="w-full sm:w-auto bg-indigo-600 text-white text-center px-4 py-2.5 rounded-lg text-sm font-bold uppercase tracking-widest hover:bg-indigo-700 transition shadow-sm">
                    <i class="fas fa-plus mr-2"></i> Tambah Stok
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Alert Success --}}
            @if (session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm"
                    role="alert">
                    <p class="text-sm font-bold">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Filter Area --}}
            @if (in_array(Auth::user()->role, ['admin', 'manager']))
                <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <form action="{{ route('bahan_baku.index') }}" method="GET" class="space-y-4">
                        <div class="flex flex-col md:flex-row md:items-end gap-4">
                            <div class="flex-1">
                                <label for="cabang_id"
                                    class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Filter
                                    Cabang</label>
                                <select name="cabang_id" id="cabang_id"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm">
                                    <option value="">Semua Cabang</option>
                                    @foreach ($listCabang as $cabang)
                                        <option value="{{ $cabang->id }}"
                                            {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                            {{ $cabang->nama_cabang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit"
                                    class="flex-1 md:flex-none bg-gray-800 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-gray-900 transition h-[42px]">
                                    <i class="fas fa-filter mr-1"></i> Filter
                                </button>
                                @if (request('cabang_id'))
                                    <a href="{{ route('bahan_baku.index') }}"
                                        class="flex-1 md:flex-none bg-red-50 text-red-600 px-6 py-2 rounded-lg text-sm text-center font-bold hover:bg-red-100 transition h-[42px] leading-[26px]">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Kontainer Utama --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">

                {{-- 1. TAMPILAN MOBILE: Mode Kartu --}}
                <div class="block md:hidden">
                    <div class="divide-y divide-gray-100">
                        @forelse ($bahanBaku as $item)
                            <div class="p-5 hover:bg-gray-50 transition active:bg-gray-100">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="font-black text-gray-900 text-base uppercase tracking-tight">
                                            {{ $item->nama }}</h3>
                                        <p class="text-xs text-gray-500 mt-1 flex items-center">
                                            <i class="fas fa-store mr-1"></i> {{ $item->nama_cabang ?? 'Cabang Utama' }}
                                        </p>
                                    </div>

                                    @if (in_array(Auth::user()->role, ['admin', 'manager']))
                                        <a href="{{ route('bahan_baku.edit', $item->id) }}"
                                            class="ml-4 bg-white border border-gray-200 text-indigo-600 p-2.5 rounded-lg shadow-sm hover:bg-indigo-50 transition">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </div>

                                <div class="flex items-end justify-between mt-4">
                                    <div>
                                        <span
                                            class="text-xs text-gray-400 block mb-1 uppercase font-bold tracking-widest">Sisa
                                            Stok</span>
                                        <div class="flex items-baseline gap-1">
                                            <span
                                                class="text-2xl font-black {{ ($item->stok_saat_ini ?? 0) <= 5 ? 'text-red-600' : 'text-emerald-600' }}">
                                                {{ number_format($item->stok_saat_ini ?? 0, 2) }}
                                            </span>
                                            <span class="text-sm font-bold text-gray-500">{{ $item->satuan }}</span>
                                        </div>
                                    </div>

                                    {{-- Badge Status Stok --}}
                                    @if (($item->stok_saat_ini ?? 0) <= 5)
                                        <span
                                            class="bg-red-100 text-red-700 text-[10px] font-black px-2 py-1 rounded-full uppercase tracking-tighter animate-pulse">
                                            Stok Menipis
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-10 text-center">
                                <i class="fas fa-box-open text-gray-200 text-5xl mb-3"></i>
                                <p class="text-gray-500 italic">Belum ada data bahan baku.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- 2. TAMPILAN DESKTOP: Mode Tabel --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">
                                    Nama Bahan</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">
                                    Cabang</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-widest">
                                    Stok Saat Ini</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">
                                    Satuan</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-widest">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($bahanBaku as $item)
                                <tr class="hover:bg-indigo-50/30 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900 uppercase">{{ $item->nama }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <span
                                            class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $item->nama_cabang ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <span
                                                class="text-lg font-black {{ ($item->stok_saat_ini ?? 0) <= 5 ? 'text-red-600' : 'text-gray-900' }}">
                                                {{ number_format($item->stok_saat_ini ?? 0, 2) }}
                                            </span>

                                            @if (($item->stok_saat_ini ?? 0) <= 5)
                                                <span
                                                    class="mt-1 bg-red-100 text-red-700 text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-tighter animate-pulse border border-red-200">
                                                    Menipis
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-500">{{ $item->satuan }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if (in_array(Auth::user()->role, ['admin', 'manager']))
                                            <a href="{{ route('bahan_baku.edit', $item->id) }}"
                                                class="text-indigo-600 hover:text-indigo-900 transition">
                                                <i class="fas fa-edit text-lg"></i>
                                            </a>
                                        @else
                                            <i class="fas fa-lock text-gray-300" title="Akses Terbatas"></i>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination (Jika ada) --}}
            <div class="mt-6">
                {{ $bahanBaku->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
