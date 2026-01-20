<x-app-layout>
    {{-- Header Dashboard --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Indo Chicken') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Bagian Selamat Datang --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                <div class="text-gray-900">
                    Selamat datang, **{{ $user->name }}**! Anda login sebagai **{{ strtoupper($user->role) }}**.
                    <p class="text-sm text-gray-500 mt-1">
                        Total Cabang Dikelola: {{ number_format($totalCabang) }}
                    </p>
                </div>
            </div>

            {{-- Bagian KPI Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                
                {{-- Card 1: Penjualan Hari Ini --}}
                <div class="bg-white p-5 rounded-lg shadow border-l-4 border-blue-500">
                    <p class="text-sm font-medium text-gray-500">Penjualan Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}
                    </p>
                </div>

                {{-- Card 2: Jumlah Transaksi --}}
                <div class="bg-white p-5 rounded-lg shadow border-l-4 border-green-500">
                    <p class="text-sm font-medium text-gray-500">Total Transaksi (Hari Ini)</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ number_format($transaksiHariIni, 0, ',', '.') }}
                    </p>
                </div>

                {{-- Card 3: Penjualan Bulan Ini --}}
                <div class="bg-white p-5 rounded-lg shadow border-l-4 border-yellow-500">
                    <p class="text-sm font-medium text-gray-500">Penjualan Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        Rp {{ number_format($penjualanBulanIni, 0, ',', '.') }}
                    </p>
                </div>

                {{-- Card 4: Kinerja VS Kemarin --}}
                <div class="bg-white p-5 rounded-lg shadow border-l-4 @if($persentaseKinerja >= 0) border-teal-500 @else border-red-500 @endif">
                    <p class="text-sm font-medium text-gray-500">Kinerja VS Kemarin</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ abs($persentaseKinerja) }}%
                        <span class="text-sm @if($persentaseKinerja >= 0) text-teal-500 @else text-red-500 @endif">
                            @if($persentaseKinerja >= 0)
                                <i class="fas fa-arrow-up"></i> Naik
                            @else
                                <i class="fas fa-arrow-down"></i> Turun
                            @endif
                        </span>
                    </p>
                </div>

            </div>
            
            {{-- Bagian Chart/Grafik --}}
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
    <h3 class="text-lg font-semibold text-gray-700 mb-4">Penjualan 7 Hari Terakhir</h3>
    
    <div class="h-64">
        <canvas id="salesChart"></canvas>
    </div>
</div>

{{-- Load Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'line', // Grafik garis agar tren terlihat jelas
            data: {
                labels: {!! json_encode($labelHari) !!}, // Label tanggal dari controller
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: {!! json_encode($dataPenjualan) !!}, // Data angka dari controller
                    borderColor: '#3b82f6', // Warna Biru
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.3, // Membuat garis sedikit melengkung (smooth)
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Penjualan: Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
</script>
        </div>
    </div>
</x-app-layout>