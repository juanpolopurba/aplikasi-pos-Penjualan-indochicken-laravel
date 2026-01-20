<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Laba Rugi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- HEADER & FILTER SECTION --}}
            <div
                class="flex flex-col md:flex-row justify-between items-center mb-6 bg-white p-6 rounded-lg shadow border-l-4 border-indigo-500">
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-800">Ringkasan Keuangan</h2>
                    <p class="text-sm text-gray-500 font-medium italic">
                        Periode: <span class="text-indigo-600">{{ date('F', mktime(0, 0, 0, $bulan, 1)) }}
                            {{ $tahun }}</span>
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 mt-4 md:mt-0">
                    <form action="{{ route('laporan.labarugi') }}" method="GET"
                        class="flex flex-wrap items-center gap-2">
                        {{-- Filter Cabang --}}
                        <select name="cabang_id"
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabangs as $cabang)
                                <option value="{{ $cabang->id }}"
                                    {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                    {{ $cabang->nama_cabang }}
                                </option>
                            @endforeach
                        </select>

                        <select name="bulan"
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </select>

                        <select name="tahun"
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                            @for ($y = date('Y'); $y >= 2023; $y--)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endfor
                        </select>

                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 rounded-md font-bold text-xs text-white uppercase hover:bg-indigo-700 transition shadow-sm">
                            Filter
                        </button>
                    </form>

                    <a href="{{ route('laporan.labarugi_pdf', ['bulan' => $bulan, 'tahun' => $tahun, 'cabang_id' => request('cabang_id')]) }}"
                        target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export PDF
                    </a>
                </div>
            </div>

            {{-- GRID UTAMA --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Data Nominal --}}
                <div class="bg-white p-8 rounded-lg shadow-md border border-gray-100 h-full">
                    <div class="flex justify-between items-center mb-6 border-b pb-2">
                        <h3 class="text-md font-bold text-gray-600 uppercase tracking-wider">Data Nominal</h3>

                        {{-- SARAN A: PROFIT MARGIN BADGE --}}
                        @php
                            $margin = $totalPendapatan > 0 ? ($labaBersih / $totalPendapatan) * 100 : 0;
                        @endphp
                        <span
                            class="px-3 py-1 rounded-full text-xs font-bold {{ $margin >= 20 ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                            Margin: {{ number_format($margin, 1) }}%
                        </span>
                    </div>

                    <table class="w-full border-collapse">
                        <tr class="border-b">
                            <td class="py-4 font-medium text-gray-600 uppercase text-xs tracking-tighter">Total
                                Pendapatan</td>
                            <td class="py-4 text-right text-green-600 font-bold text-xl">Rp
                                {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-4 font-medium text-gray-600 uppercase text-xs tracking-tighter">Total
                                Pengeluaran</td>
                            <td class="py-4 text-right text-red-500 font-bold text-xl">(Rp
                                {{ number_format($totalPengeluaran, 0, ',', '.') }})</td>
                        </tr>
                        <tr class="bg-indigo-50">
                            <td class="py-5 px-3 font-bold text-gray-800 text-lg tracking-tighter">LABA / RUGI BERSIH
                            </td>
                            <td
                                class="py-5 px-3 text-right font-black text-2xl {{ $labaBersih >= 0 ? 'text-indigo-700' : 'text-red-700' }}">
                                Rp {{ number_format($labaBersih, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Visualisasi Chart --}}
                <div class="bg-white p-8 rounded-lg shadow-md border border-gray-100 h-full flex flex-col">
                    <h3 class="text-md font-bold mb-6 text-gray-600 uppercase tracking-wider border-b pb-2 text-center">
                        Tren Keuangan 6 Bulan Terakhir</h3>
                    <div class="flex-grow relative" style="min-height: 300px;">
                        <canvas id="labaRugiChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- SECTION ANALISIS PENGELUARAN --}}
            <div class="bg-white p-8 rounded-lg shadow-md border border-gray-100 mb-6">
                <h3 class="text-md font-bold mb-6 text-gray-600 uppercase tracking-wider border-b pb-2">
                    Analisis Pengeluaran Per Kategori
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 items-center">
                    <div style="max-height: 300px;">
                        <canvas id="kategoriChart"></canvas>
                    </div>
                    <div class="mt-6 md:mt-0 md:ml-10">
                        @php
                            $colors = [
                                'rgba(255, 99, 132, 0.8)',
                                'rgba(54, 162, 235, 0.8)',
                                'rgba(255, 206, 86, 0.8)',
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(153, 102, 255, 0.8)',
                                'rgba(255, 159, 64, 0.8)',
                            ];
                        @endphp
                        <ul class="space-y-3">
                            @forelse ($labelsKategori as $index => $label)
                                <li class="flex justify-between items-center text-sm">
                                    <span class="flex items-center">
                                        <span class="w-3 h-3 rounded-full mr-2"
                                            style="background-color: {{ $colors[$index % count($colors)] }}"></span>
                                        {{ $label }}
                                    </span>
                                    <span class="font-bold text-gray-700 font-mono">
                                        Rp {{ number_format($dataKategori[$index], 0, ',', '.') }}
                                    </span>
                                </li>
                            @empty
                                <li class="text-gray-400 italic text-sm">Tidak ada data pengeluaran</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            {{-- RINCIAN TRANSAKSI --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Rincian Pendapatan --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-green-50 px-6 py-4 border-b border-green-100 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-green-700 uppercase tracking-widest">Rincian Pendapatan</h3>
                        <span
                            class="text-[10px] bg-green-200 text-green-800 px-2 py-1 rounded shadow-sm">Real-time</span>
                    </div>
                    <div class="overflow-y-auto max-h-80">
                        <table class="w-full text-left">
                            <tbody class="divide-y divide-gray-100">
                                @forelse($detailPendapatan as $p)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="p-4 text-sm text-gray-600 font-medium">
                                            {{ date('d M Y H:i', strtotime($p->created_at)) }}</td>
                                        <td class="p-4 text-sm text-right font-bold text-green-600">Rp
                                            {{ number_format($p->total_penjualan) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="p-8 text-center text-gray-400">Belum ada transaksi
                                            pendapatan periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Rincian Pengeluaran --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-red-50 px-6 py-4 border-b border-red-100">
                        <h3 class="text-sm font-bold text-red-700 uppercase tracking-widest">Rincian Pengeluaran</h3>
                    </div>
                    <div class="overflow-y-auto max-h-80">
                        <table class="w-full text-left">
                            <tbody class="divide-y divide-gray-100">
                                @forelse($detailPengeluaran as $e)
                                    {{-- SARAN B: HIGHLIGHT PENGELUARAN TINGGI --}}
                                    <tr
                                        class="hover:bg-gray-50 transition {{ $e->jumlah >= 5000000 ? 'bg-red-50' : '' }}">
                                        <td class="p-4">
                                            <p class="text-sm font-bold text-gray-800">
                                                {{ $e->deskripsi }}
                                                @if ($e->jumlah >= 5000000)
                                                    <span
                                                        class="ml-2 text-[10px] text-red-600 font-black uppercase tracking-tighter">[High
                                                        Spend]</span>
                                                @endif
                                            </p>
                                            <p class="text-[10px] text-gray-400 uppercase font-medium">
                                                {{ date('d/m/Y', strtotime($e->created_at)) }}</p>
                                        </td>
                                        <td class="p-4 text-sm text-right font-bold text-red-600">Rp
                                            {{ number_format($e->jumlah) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="p-8 text-center text-gray-400">Belum ada catatan
                                            pengeluaran periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT CHART JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('labaRugiChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labelsLabaRugi) !!},
                    datasets: [{
                            label: 'Pendapatan',
                            data: {!! json_encode($dataOmzet) !!},
                            backgroundColor: 'rgba(34, 197, 94, 0.7)',
                            borderColor: '#16a34a',
                            borderWidth: 1.5,
                            borderRadius: 4,
                        },
                        {
                            label: 'Pengeluaran',
                            data: {!! json_encode($dataBiaya) !!},
                            backgroundColor: 'rgba(239, 68, 68, 0.7)',
                            borderColor: '#dc2626',
                            borderWidth: 1.5,
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    }
                }
            });
        });

        // Inisialisasi Pie Chart Kategori
        // Inisialisasi Pie Chart Kategori
        const ctxKategori = document.getElementById('kategoriChart').getContext('2d');

        // Definisikan array warna yang sama dengan di atas
        const chartColors = [
            'rgba(255, 99, 132, 0.8)',
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 206, 86, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(153, 102, 255, 0.8)',
            'rgba(255, 159, 64, 0.8)',
        ];

        new Chart(ctxKategori, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($labelsKategori) !!},
                datasets: [{
                    data: {!! json_encode($dataKategori) !!},
                    backgroundColor: chartColors, // Menggunakan variabel chartColors
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</x-app-layout>
