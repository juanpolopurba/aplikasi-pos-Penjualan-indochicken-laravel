<!DOCTYPE html>
<html>

<head>
    <title>Rekap Penjualan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Memberi warna selang-seling */
        th {
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        td {
            vertical-align: top;
        }

        /* Agar teks panjang tidak berantakan */
    </style>
</head>

<body>
    <div class="header">
        <h2>REKAP PENJUALAN INDOCHICKEN</h2>
        <p>Cabang: {{ $cabang }} | Tanggal: {{ $tanggal }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Jam</th>
                <th>Detail Menu</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($laporans as $laporan)
                <tr>
                    <td>#{{ $laporan->id }}</td>
                    <td>{{ $laporan->created_at->format('H:i') }}</td>
                    <td>
                        @foreach ($laporan->details as $d)
                            {{ $d->menu->nama_menu ?? 'Menu' }} (x{{ $d->jumlah_terjual }}),
                        @endforeach
                    </td>
                    <td class="text-right">Rp{{ number_format($laporan->total_penjualan, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #eee;">
                <td colspan="3" class="text-right">TOTAL PENDAPATAN</td>
                <td class="text-right">Rp{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
