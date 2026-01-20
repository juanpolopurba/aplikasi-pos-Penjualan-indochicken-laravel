<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Monitoring Kas Operasional (Cash On Hand)
            </h2>
            
            {{-- Filter Cabang untuk Admin/Manager --}}
            @if(in_array(Auth::user()->role, ['admin', 'manager']))
            <form action="{{ route('cash_on_hand.index') }}" method="GET" class="flex gap-2">
                <select name="cabang_id" class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($listCabang as $cab)
                        <option value="{{ $cab->id }}" {{ request('cabang_id') == $cab->id ? 'selected' : '' }}>
                            {{ $cab->nama_cabang }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-bold">
                    Cek Kas
                </button>
            </form>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Jika Admin belum pilih cabang, tampilkan pesan instruksi --}}
            @if(!$currentCabangId)
                <div class="bg-white p-10 rounded-xl shadow-sm text-center border-2 border-dashed border-gray-300">
                    <i class="fas fa-store-alt text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-600">Silakan Pilih Cabang Terlebih Dahulu</h3>
                    <p class="text-gray-500">Pilih cabang di pojok kanan atas untuk melihat data kas dan melakukan input.</p>
                </div>
            @else

                {{-- Dashboard Ringkasan --}}
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
                        <p class="text-xs font-bold text-gray-500 uppercase font-mono">Modal Awal</p>
                        <p class="text-lg font-bold">Rp{{ number_format($kasMasuk) }}</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-green-500">
                        <p class="text-xs font-bold text-gray-500 uppercase font-mono">Penjualan Tunai</p>
                        <p class="text-lg font-bold">Rp{{ number_format($penjualanTunai) }}</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-red-500">
                        <p class="text-xs font-bold text-gray-500 uppercase font-mono">Total Kas Keluar</p>
                        <p class="text-lg font-bold text-red-600">- Rp{{ number_format($kasKeluar) }}</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-orange-500">
                        <p class="text-xs font-bold text-gray-500 uppercase font-mono">Sudah Disetor</p>
                        <p class="text-lg font-bold text-orange-600">Rp{{ number_format($totalSetoran) }}</p>
                    </div>
                    <div class="bg-indigo-600 p-4 rounded-lg shadow-md text-white md:scale-105 transform transition">
                        <p class="text-xs font-bold uppercase opacity-80 font-mono italic">Uang di Laci (Fisik)</p>
                        <p class="text-2xl font-black tracking-tight">Rp{{ number_format($cashOnHand) }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Form Input --}}
                    <div class="bg-white p-6 rounded-xl shadow-sm h-fit border border-gray-100">
                        <div class="flex items-center mb-4">
                            <div class="p-2 bg-indigo-50 rounded-lg mr-3">
                                <i class="fas fa-edit text-indigo-600"></i>
                            </div>
                            <h3 class="font-bold text-gray-700">Input Kas Cabang</h3>
                        </div>
                        
                        <form action="{{ route('cash_on_hand.store') }}" method="POST">
                            @csrf
                            {{-- Input Hidden Cabang ID agar data masuk ke cabang yang sedang difilter --}}
                            <input type="hidden" name="cabang_id" value="{{ $currentCabangId }}">

                            <div class="mb-4">
                                <label class="block text-sm font-bold text-gray-600 mb-1">Jenis Transaksi</label>
                                <select name="jenis" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="keluar">Kas Keluar (Biaya Operasional)</option>
                                    <option value="masuk">Kas Masuk (Modal Tambahan)</option>
                                    <option value="setoran">Setoran ke Owner (Ambil Uang)</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-bold text-gray-600 mb-1">Jumlah Uang (Rp)</label>
                                <input type="number" name="jumlah" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Rp 0" required>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-bold text-gray-600 mb-1">Keterangan / Alasan</label>
                                <input type="text" name="keterangan" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: Bayar Listrik / Galon" required>
                            </div>

                            <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-black uppercase tracking-widest hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition active:scale-95">
                                Simpan Transaksi
                            </button>
                        </form>
                    </div>

                    {{-- Tabel Riwayat --}}
                    <div class="md:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                        <div class="bg-gray-50 p-4 border-b border-gray-200">
                            <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wider">Riwayat Kas Hari Ini</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Waktu</th>
                                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Keterangan</th>
                                        <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($riwayatKas as $rk)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-xs font-mono text-gray-400">
                                                {{ date('H:i', strtotime($rk->created_at)) }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <p class="text-sm font-bold text-gray-800 uppercase">{{ $rk->keterangan }}</p>
                                                <p class="text-[10px] text-gray-400 font-mono">{{ strtoupper($rk->jenis) }}</p>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-right font-black {{ $rk->jenis == 'masuk' ? 'text-emerald-600' : 'text-red-600' }}">
                                                {{ $rk->jenis == 'masuk' ? '+' : '-' }} Rp{{ number_format($rk->jumlah) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-10 text-center text-gray-400 italic">Belum ada aktivitas kas hari ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>