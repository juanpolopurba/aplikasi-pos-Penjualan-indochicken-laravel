<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-xl text-gray-800 leading-tight italic">Log Aktivitas Kas</h2>

            <form action="" method="GET" class="grid grid-cols-2 md:flex gap-2 w-full md:w-auto">
                <select name="cabang_id" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-xs md:text-sm w-full">
                    <option value="">Semua Cabang</option>
                    @foreach ($listCabang as $cabang)
                        <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                            {{ $cabang->nama_cabang }}
                        </option>
                    @endforeach
                </select>
                <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-xs md:text-sm w-full">
            </form>
        </div>
    </x-slot>

    @php
        // 1. Hitung total kumulatif untuk menentukan Saldo Awal perhitungannya
        $totalMasuk = $riwayatKas->where('jenis', 'masuk')->sum('jumlah');
        $totalKeluar = $riwayatKas->whereIn('jenis', ['keluar', 'setoran'])->sum('jumlah');
        $sisaLaci = $totalMasuk - $totalKeluar;
        
        // Variabel untuk running balance (Logika Mundur)
        $currentBalance = $sisaLaci; 
    @endphp

    @if (request('cabang_id'))
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 px-4 sm:px-6 lg:px-8 mt-6">
            <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl p-5 text-white shadow-lg">
                <p class="text-xs opacity-80 uppercase font-bold tracking-wider">Sisa Uang di Laci (Akhir)</p>
                <h3 class="text-2xl font-black mt-1">Rp {{ number_format($sisaLaci) }}</h3>
            </div>

            <div class="hidden md:flex bg-white rounded-2xl p-5 border border-gray-100 shadow-sm items-center gap-4">
                <div class="p-3 bg-green-50 rounded-full text-green-600">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase">Total Masuk</p>
                    <p class="text-lg font-bold text-gray-800">Rp {{ number_format($totalMasuk) }}</p>
                </div>
            </div>

            <div class="hidden md:flex bg-white rounded-2xl p-5 border border-gray-100 shadow-sm items-center gap-4">
                <div class="p-3 bg-red-50 rounded-full text-red-600">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase">Total Keluar</p>
                    <p class="text-lg font-bold text-gray-800">Rp {{ number_format($totalKeluar) }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- DESKTOP VIEW --}}
            <div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Transaksi</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase text-green-600">Masuk</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase text-red-600">Keluar</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($riwayatKas as $rk)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-xs font-mono text-gray-500">
                                    {{ date('H:i:s', strtotime($rk->created_at)) }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-800">{{ $rk->keterangan }}</div>
                                    <div class="text-[10px] text-gray-400 uppercase">{{ $rk->user_name }} • {{ $rk->nama_cabang }}</div>
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-green-600 font-bold">
                                    {{ $rk->jenis == 'masuk' ? 'Rp'.number_format($rk->jumlah) : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-red-600 font-bold">
                                    {{ $rk->jenis != 'masuk' ? 'Rp'.number_format($rk->jumlah) : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-black bg-gray-50">
                                    Rp{{ number_format($currentBalance) }}
                                </td>
                            </tr>
                            
                            {{-- LOGIKA HITUNG MUNDUR UNTUK BARIS SELANJUTNYA (KE BAWAH = KE WAKTU LAMA) --}}
                            @php
                                if ($rk->jenis == 'masuk') {
                                    $currentBalance -= $rk->jumlah;
                                } else {
                                    $currentBalance += $rk->jumlah;
                                }
                            @endphp
                        @empty
                            <tr><td colspan="5" class="p-10 text-center text-gray-400 italic">Data tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE VIEW --}}
            <div class="block md:hidden space-y-3">
                @php $currentBalanceMobile = $sisaLaci; @endphp
                @foreach ($riwayatKas as $rk)
                    <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 {{ $rk->jenis == 'masuk' ? 'border-green-500' : 'border-red-500' }}">
                        <div class="flex justify-between items-start">
                            <span class="text-[10px] font-mono text-gray-400">{{ date('H:i', strtotime($rk->created_at)) }}</span>
                            <span class="text-sm font-black {{ $rk->jenis == 'masuk' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $rk->jenis == 'masuk' ? '+' : '-' }}Rp{{ number_format($rk->jumlah) }}
                            </span>
                        </div>
                        <h4 class="text-sm font-bold text-gray-800">{{ $rk->keterangan }}</h4>
                        <div class="flex justify-between items-center mt-3 pt-2 border-t border-dashed border-gray-100">
                            <span class="text-[9px] text-gray-400 uppercase">{{ $rk->user_name }}</span>
                            <span class="text-[10px] font-bold py-1 px-2 bg-gray-100 rounded text-gray-700">
                                Sld: Rp{{ number_format($currentBalanceMobile) }}
                            </span>
                        </div>
                    </div>
                    @php
                        if ($rk->jenis == 'masuk') { $currentBalanceMobile -= $rk->jumlah; }
                        else { $currentBalanceMobile += $rk->jumlah; }
                    @endphp
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>