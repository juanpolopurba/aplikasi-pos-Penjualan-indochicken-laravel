<!DOCTYPE html>
<html>
<head>
    <title>Nota #{{ $laporan->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #f3f4f6; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .total-section { margin-top: 20px; text-align: right; }
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="color: #4f46e5; margin: 0;">INDOCHICKEN</h1>
        <p style="margin: 5px 0;">Laporan Transaksi Resmi</p>
    </div>

    <div class="info">
        <table style="border: none;">
            <tr>
                <td style="border: none; width: 50%;">
                    <strong>Cabang:</strong> {{ $laporan->cabang->nama_cabang ?? 'Pusat' }}<br>
                    <strong>ID Transaksi:</strong> #{{ $laporan->id }}<br>
                    {{-- TAMBAHKAN BARIS INI --}}
                    <strong>Petugas:</strong> {{ $laporan->user->name ?? 'System' }} 
                </td>
                <td style="border: none; text-align: right; vertical-align: top;">
                    <strong>Tanggal:</strong> {{ $tanggal }}<br>
                    <strong>Waktu:</strong> {{ $jam }} WIB
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Menu</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan->details as $item)
            <tr>
                <td>{{ $item->menu->nama_menu ?? 'Menu Dihapus' }}</td>
                <td style="text-align: center;">{{ $item->jumlah_terjual }}</td>
                <td style="text-align: right;">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <p style="font-size: 16px;"><strong>Total Bayar: Rp{{ number_format($laporan->total_penjualan, 0, ',', '.') }}</strong></p>
    </div>

    @if($laporan->catatan)
    <div style="margin-top: 10px; font-style: italic; color: #666;">
        <strong>Catatan:</strong> {{ $laporan->catatan }}
    </div>
    @endif

    <div class="footer">
        <p>Dokumen ini dihasilkan secara otomatis oleh sistem pada {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>