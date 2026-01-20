<x-app-layout>
    <x-slot name="header">
        {{-- Class no-print agar header web tidak ikut tercetak --}}
        <div class="flex justify-between items-center no-print">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Pembelian #{{ $pembelian->id }}
            </h2>
            <a href="{{ route('pembelian.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md text-sm">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12 main-content-wrapper">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- ID nota-pembelian digunakan oleh CSS @media print --}}
            <div id="nota-pembelian" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">

                <div class="grid grid-cols-2 gap-4 mb-8 border-b pb-6">
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-bold">Supplier</p>
                        <p class="text-lg font-semibold">{{ $pembelian->supplier }}</p>

                        <p class="text-sm text-gray-500 uppercase font-bold mt-4">Cabang Tujuan</p>
                        <p class="text-lg font-semibold">{{ $pembelian->nama_cabang }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500 uppercase font-bold">Tanggal Transaksi</p>
                        <p class="text-lg font-semibold">{{ date('d F Y', strtotime($pembelian->tanggal)) }}</p>

                        <p class="text-sm text-gray-500 uppercase font-bold mt-4">Admin Penginput</p>
                        <p class="text-lg font-semibold">{{ $pembelian->nama_penginput }}</p>
                    </div>
                </div>

                <table class="min-w-full divide-y divide-gray-200 mb-6">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Bahan</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Harga Satuan</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($details as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-gray-900 font-bold print-text-black">
                                        {{ $item->nama_bahan }}
                                    </div>
                                    <div class="text-xs text-gray-500">ID: {{ $item->bahan_id }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{ number_format($item->kuantitas, 2) }} {{ $item->satuan }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right font-semibold">
                                    Rp {{ number_format($item->kuantitas * $item->harga_satuan, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100 font-bold">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right uppercase">Total Pembelian</td>
                            <td class="px-6 py-4 text-right text-lg text-indigo-700">
                                Rp {{ number_format($pembelian->total_pembelian, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <div class="flex justify-end no-print">
                    <button onclick="window.print()"
                        class="bg-green-600 text-white px-6 py-2 rounded shadow hover:bg-green-700">
                        Cetak Nota
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            /* 1. Sembunyikan SEMUA elemen UI web */
            nav, aside, header, .sidebar, [role="navigation"], .no-print, button {
                display: none !important;
                visibility: hidden !important;
            }

            /* 2. Reset paksa layout agar nempel ke kiri dan atas kertas */
            /* Kita me-reset semua wrapper yang mungkin punya padding/margin kiri */
            body, html, main, .main-content-wrapper, .py-12, .max-w-7xl, .mx-auto, .sm\:px-6, .lg\:px-8 {
                margin: 0 !important;
                padding: 0 !important;
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                left: 0 !important;
                position: relative !important;
                background-color: white !important;
            }

            /* 3. Atur nota agar rapi di kertas */
            #nota-pembelian {
                padding: 10mm !important;
                box-shadow: none !important;
                border: none !important;
            }

            /* 4. Pastikan teks terlihat hitam pekat */
            .print-text-black, .text-gray-900, .font-bold, td, th, p {
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
        }
    </style>
</x-app-layout>