<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pembelian Bahan</title>
    <style>
        /* Optimasi untuk render PDF */
        @page { margin: 1cm; }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px; /* Sedikit lebih kecil agar muat banyak data */
            line-height: 1.4;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            table-layout: fixed; /* Mencegah tabel melebar keluar kertas */
        }

        th, td {
            border: 1px solid #000; /* Garis hitam lebih tegas untuk cetak */
            padding: 6px;
            word-wrap: break-word; /* Agar teks panjang tidak merusak layout */
        }

        th {
            background-color: #f0f0f0;
            text-align: center;
            text-transform: uppercase;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h2 { margin: 0; padding: 0; }
        .header p { margin: 2px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PEMBELIAN BAHAN BAKU</h2>
        <p><strong>Cabang:</strong> {{ $namaCabang }}</p>
        <p><strong>Periode:</strong> {{ $startDate ? date('d/m/Y', strtotime($startDate)) : 'Semua' }} s/d {{ $endDate ? date('d/m/Y', strtotime($endDate)) : 'Sekarang' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 40%;">Supplier / Bahan</th>
                <th style="width: 20%;">Cabang</th>
                <th style="width: 25%;" class="text-right">Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pembelian as $p)
            <tr>
                <td class="text-center">{{ date('d/m/Y', strtotime($p->tanggal)) }}</td>
                <td>
                    <strong>{{ $p->supplier }}</strong><br>
                    <span style="color: #555; font-size: 9px;">
                        {{ $p->daftar_bahan ?? 'Tidak ada detail bahan' }}
                    </span>
                </td>
                <td class="text-center">{{ $p->nama_cabang }}</td>
                <td class="text-right">Rp {{ number_format($p->total_pembelian, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td colspan="3" class="text-right">TOTAL KESELURUHAN</td>
                <td class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>