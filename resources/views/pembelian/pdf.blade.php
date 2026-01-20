<!DOCTYPE html>
<html>

<head>
    <title>Laporan Pembelian Bahan Baku</title>
    <style>
        @page {
            margin: 1cm;
        }

        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .table th {
            background-color: #f2f2f2;
            border: 1px solid #ccc;
            padding: 8px 4px;
            font-size: 10px;
            text-transform: uppercase;
        }

        .table td {
            border: 1px solid #eee;
            padding: 6px 4px;
            vertical-align: top;
            /* Diubah ke top agar list bahan rapi */
            word-wrap: break-word;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-muted {
            color: #666;
            font-size: 9px;
        }

        .footer-total td {
            background-color: #f9f9f9;
            border-top: 2px solid #000 !important;
            padding: 10px 4px;
        }

        /* Style khusus list bahan */
        .list-bahan {
            margin: 0;
            padding-left: 12px;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="margin:0;">LAPORAN PEMBELIAN BAHAN BAKU INDOCHICKEN</h2>
        <p style="margin:5px 0;">
            Cabang: <strong>{{ $namaCabang }}</strong> |
            Periode: <strong>{{ request('start_date') ?? 'Awal' }} - {{ request('end_date') ?? 'Sekarang' }}</strong>
        </p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 14%;">Tanggal</th>
                <th style="width: 25%;">Detail Bahan</th> {{-- KOLOM BARU --}}
                <th style="width: 15%;">Supplier</th>
                <th style="width: 12%;">Cabang</th>
                <th style="width: 12%;">Petugas</th>
                <th style="width: 18%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $totalSemua = 0; @endphp
            @foreach ($pembelian as $index => $item)
                @php $totalSemua += $item->total_pembelian; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        <span class="font-bold">
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                        </span><br>
                        <span class="text-muted">
                            {{-- Tambahkan Carbon::parse di sini --}}
                            {{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }} WIB
                        </span>
                    </td>
                    <td>
                        @if (count($item->details) > 0)
                            <ul class="list-bahan">
                                @foreach ($item->details as $detail)
                                    <li>
                                        {{ $detail->nama }} {{-- Hasil dari join tabel bahan_baku --}}
                                        ({{ $detail->kuantitas }} {{ $detail->satuan }})
                                        {{-- Pakai kuantitas sesuai kolom Anda --}}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted">Tidak ada detail</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->supplier }}</td>
                   <td class="text-center">{{ $item->nama_lokasi_cabang ?? 'Pusat' }}</td>
                    <td class="text-center">
                        {{ $item->nama_petugas ?? 'System' }}
                    </td>
                    <td class="text-right font-bold">
                        Rp{{ number_format($item->total_pembelian, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="footer-total">
                <td colspan="6" class="text-right font-bold">TOTAL KESELURUHAN</td>
                <td class="text-right font-bold">
                    Rp{{ number_format($totalSemua, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; text-align: right; font-size: 9px; color: #999;">
        Dicetak pada: {{ date('d/m/Y H:i:s') }}
    </div>
</body>

</html>
