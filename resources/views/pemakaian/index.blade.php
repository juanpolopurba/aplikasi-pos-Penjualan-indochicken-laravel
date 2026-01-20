<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('Riwayat Pemakaian Bahan') }}
            </h2>
            <a href="{{ route('pemakaian.create') }}"
                class="w-full sm:w-auto text-center bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 sm:py-2 rounded-lg text-sm font-bold uppercase tracking-widest transition shadow-md">
                <i class="fas fa-plus mr-2"></i> Input Pemakaian
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- FILTER BOX --}}
            <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm mb-6 border border-gray-100">
                <form action="{{ route('pemakaian.index') }}" method="GET" class="grid grid-cols-1 sm:flex sm:flex-wrap gap-4 items-end">
                    <div class="w-full sm:w-auto">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Filter Tanggal</label>
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 text-sm">
                    </div>

                    @if (auth()->user()->role === 'admin' || auth()->user()->role === 'manager')
                        <div class="w-full sm:w-auto">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Pilih Cabang</label>
                            <select name="cabang_id" onchange="this.form.submit()"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 text-sm">
                                <option value="">Semua Cabang</option>
                                @foreach ($listCabang as $cabang)
                                    <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                        {{ $cabang->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="submit" class="flex-1 sm:flex-none bg-indigo-600 text-white px-5 py-2.5 rounded-md font-bold hover:bg-indigo-700 transition shadow-sm">
                            <i class="fas fa-filter mr-2 sm:mr-0"></i> <span class="sm:hidden">Filter</span>
                        </button>
                        <a href="{{ route('pemakaian.index') }}" class="flex-1 sm:flex-none text-center bg-gray-100 text-gray-600 px-5 py-2.5 rounded-md font-bold hover:bg-gray-200 transition">
                            <i class="fas fa-sync-alt mr-2 sm:mr-0"></i> <span class="sm:hidden">Reset</span>
                        </a>
                    </div>
                </form>
            </div>

            {{-- DATA AREA --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Waktu & Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Cabang</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Bahan Baku</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-widest">Jumlah Keluar</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($riwayat as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }} WIB</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">{{ $item->nama_cabang }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">{{ $item->nama_bahan }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-red-600">
                                        - {{ number_format($item->jumlah, 2) }} <span class="text-xs font-normal text-gray-500">{{ $item->satuan }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-600 italic">{{ $item->keterangan ?? '-' }}</td>
                                </tr>
                            @empty
                                @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="block sm:hidden divide-y divide-gray-100">
                    @forelse($riwayat as $item)
                        <div class="p-4 active:bg-gray-50">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">
                                        {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y') }} | {{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }}
                                    </div>
                                    <div class="text-base font-black text-gray-800">{{ $item->nama_bahan }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-black text-red-600">-{{ number_format($item->jumlah, 2) }}</div>
                                    <div class="text-[10px] text-gray-500 uppercase font-bold">{{ $item->satuan }}</div>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="px-2 py-0.5 text-[10px] font-bold bg-blue-50 text-blue-600 rounded border border-blue-100 uppercase italic">
                                    {{ $item->nama_cabang }}
                                </span>
                                <div class="text-[11px] text-gray-500 italic max-w-[60%] truncate">
                                    {{ $item->keterangan ?? 'No Ket.' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 italic text-sm">Belum ada data.</div>
                    @endforelse
                </div>

                {{-- PAGINATION --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {{ $riwayat->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>