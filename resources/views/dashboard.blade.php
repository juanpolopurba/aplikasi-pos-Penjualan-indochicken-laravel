<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Pengeluaran & Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Filter Cabang --}}
            @if ($hasFullAccess)
                <div class="bg-white p-4 rounded-xl shadow-sm mb-6 border border-gray-100">
                    <form action="{{ route('dashboard') }}" method="GET" class="flex items-center gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1 tracking-wider">Filter Cabang</label>
                            <select name="cabang_id" onchange="this.form.submit()"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Semua Cabang</option>
                                @foreach ($cabangs as $cabang)
                                    <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                        {{ $cabang->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Ringkasan Angka --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase">Penjualan Hari Ini</p>
                    <h3 class="text-xl font-black text-gray-800">Rp {{ number_format($penjualanHariIni) }}</h3>
                    <p class="text-[10px] {{ $persentaseKinerja >= 0 ? 'text-green-600' : 'text-red-600' }} font-bold mt-1">
                        {{ $persentaseKinerja >= 0 ? '↑' : '↓' }} {{ abs($persentaseKinerja) }}% dibanding kemarin
                    </p>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase">Pengeluaran Hari Ini</p>
                    <h3 class="text-xl font-black text-red-600">Rp {{ number_format($pengeluaranHariIni) }}</h3>
                </div>

                <div class="bg-indigo-600 p-5 rounded-xl shadow-lg text-white">
                    <p class="text-xs font-bold opacity-80 uppercase">Omzet Bulan Ini</p>
                    <h3 class="text-xl font-black">Rp {{ number_format($penjualanBulanIni) }}</h3>
                </div>

                <div class="bg-red-600 p-5 rounded-xl shadow-lg text-white">
                    <p class="text-xs font-bold opacity-80 uppercase">Biaya Bulan Ini</p>
                    <h3 class="text-xl font-black">Rp {{ number_format($pengeluaranBulanIni) }}</h3>
                </div>
            </div>

            {{-- Grafik --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-widest">Tren 7 Hari Terakhir</h3>
                <div class="h-64">
                    <canvas id="mainChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    {{-- Letakkan di Dashboard (bukan di halaman laba rugi) --}}
<div class="mt-8 bg-gradient-to-r from-indigo-600 to-blue-700 rounded-2xl p-6 text-white shadow-xl">
    <div class="flex justify-between items-center">
        <div>
            <h4 class="text-sm font-bold opacity-80 uppercase tracking-widest">Estimasi Laba Bersih Bulan Ini</h4>
            <p class="text-3xl font-black mt-1">Rp {{ number_format($penjualanBulanIni - $pengeluaranBulanIni) }}</p>
        </div>
        <a href="{{ route('laporan.labarugi') }}" class="bg-white text-indigo-600 px-4 py-2 rounded-lg text-xs font-bold hover:bg-indigo-50 transition">
            Lihat Laporan Detail →
        </a>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('mainChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labelHari) !!},
                datasets: [
                    {
                        label: 'Penjualan',
                        data: {!! json_encode($dataPenjualan) !!},
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Pengeluaran',
                        data: {!! json_encode($dataPengeluaran) !!},
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</x-app-layout>