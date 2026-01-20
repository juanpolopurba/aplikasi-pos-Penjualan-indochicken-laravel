<!DOCTYPE html>
<html>
<head>
    <title>Laporan Laba Rugi</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; }
        .table td { padding: 8px 0; }
        .border-top { border-top: 1px solid #000; }
        .font-bold { font-weight: bold; }
        .text-right { text-align: right; }
        .text-danger { color: #d9534f; }
        .text-success { color: #5cb85c; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN LABA RUGI INDOCHICKEN</h2>
        <p>Cabang: {{ $cabang }} <br> Periode: {{ $tanggal }}</p>
    </div>

    <table class="table">
        <tr>
            <td class="font-bold" colspan="2">PENDAPATAN</td>
        </tr>
        <tr>
            <td>Total Penjualan</td>
            <td class="text-right">Rp{{ number_format($pemasukan, 0, ',', '.') }}</td>
        </tr>
        <tr class="font-bold">
            <td>TOTAL PENDAPATAN (A)</td>
            <td class="text-right border-top">Rp{{ number_format($pemasukan, 0, ',', '.') }}</td>
        </tr>

        <tr><td colspan="2">&nbsp;</td></tr>

        <tr>
            <td class="font-bold" colspan="2">BEBAN & PENGELUARAN</td>
        </tr>
        <tr>
            <td>Biaya Operasional (Listrik, Gaji, dll)</td>
            <td class="text-right">Rp{{ number_format($pengeluaran, 0, ',', '.') }}</td>
        </tr>

        <tr><td colspan="2" style="padding: 20px 0;"><hr></td></tr>

        <tr class="font-bold" style="font-size: 16px; background: #f9f9f9;">
            <td>LABA / RUGI BERSIH (A - B)</td>
            <td class="text-right {{ $laba_bersih >= 0 ? 'text-success' : 'text-danger' }}">
                Rp{{ number_format($laba_bersih, 0, ',', '.') }}
            </td>
        </tr>
    </table>
</body>
</html>