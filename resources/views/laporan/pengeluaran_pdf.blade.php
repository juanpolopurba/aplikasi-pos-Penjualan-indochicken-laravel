<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengeluaran - {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            margin: 0;
            text-transform: uppercase;
            font-size: 18px;
            color: #000;
        }

        .header p {
            margin: 5px 0;
            font-size: 12px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
        }

        .content-table th {
            background-color: #444;
            color: white;
            padding: 8px;
            border: 1px solid #333;
            text-transform: uppercase;
        }

        .content-table td {
            padding: 8px;
            border: 1px solid #ccc;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer-section {
            margin-top: 30px;
            width: 100%;
        }

        .total-box {
            background-color: #eee;
            font-weight: bold;
            font-size: 13px;
        }

        .ttd-table {
            width: 100%;
            margin-top: 50px;
        }

        .ttd-table td {
            width: 50%;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>INDOCHICKEN - LAPORAN PENGELUARAN</h2>
        <p>Cabang: <strong>{{ $namaCabang }}</strong> | Periode: <strong>{{ $namaBulan }}
                {{ $tahun }}</strong></p>
    </div>

    <table class="content-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="18%">Kategori</th>
                <th>Keterangan / Keperluan</th>
                <th width="15%">Admin</th>
                <th width="15%" class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengeluaran as $key => $row)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}
                        {{-- Contoh di dalam @foreach pengeluaran --}}
                        {{ \Carbon\Carbon::parse($row->created_at)->format('H:i') }}</td>
                    <td>{{ $row->kategori->nama_kategori ?? 'Umum' }}</td>
                    <td>{{ $row->deskripsi }}</td>
                    <td class="text-center">{{ $row->user->name ?? '-' }}</td>
                    <td class="text-right">{{ number_format($row->jumlah, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data pengeluaran pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-box">
                <td colspan="5" class="text-right" style="padding: 10px;">TOTAL SELURUH PENGELUARAN :</td>
                <td class="text-right" style="padding: 10px;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-section">
        <p><em>* Laporan ini dibuat secara otomatis oleh sistem pada {{ date('d/m/Y H:i') }}</em></p>

        <table class="ttd-table">
            <tr>
                <td>
                    Disetujui Oleh,<br><br><br><br>
                    ( ............................ )<br>
                    <strong>Manager</strong>
                </td>
                <td>
                    Dibuat Oleh,<br><br><br><br>
                    ( {{ auth()->user()->name }} )<br>
                    <strong>Admin Keuangan</strong>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
