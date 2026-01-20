<!DOCTYPE html>
<html>
<head>
    <title>Rekap Penjualan</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        
        /* Table Layout */
        .table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        
        /* Header Styling */
        .table th { 
            background-color: #f2f2f2;
            border: 1px solid #ccc;
            padding: 8px 4px;
            font-size: 10px;
            text-transform: uppercase;
        }
        
        /* Body Styling */
        .table td { 
            border: 1px solid #eee;
            padding: 6px 4px;
            vertical-align: middle;
            word-wrap: break-word; /* Agar teks panjang tidak merusak layout */
        }
        
        /* Alignment Helpers */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-muted { color: #666; font-size: 9px; }

        /* Footer Styling */
        .footer-total td { 
            background-color: #f9f9f9; 
            border-top: 2px solid #000 !important; 
            padding: 10px 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0;">LAPORAN REKAP PENJUALAN INDOCHICKEN</h2>
        <p style="margin:5px 0;">Cabang: <strong>{{ $nama_cabang }}</strong> | Periode: <strong>{{ $periode }}</strong></p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 20%;">Tanggal & Jam</th>
                <th style="width: 22%;">Cabang</th>
                <th style="width: 25%;">Petugas</th>
                <th style="width: 25%;" class="text-right">Total Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporans as $index => $laporan)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">
                    <span class="font-bold">{{ \Carbon\Carbon::parse($laporan->tanggal)->format('d/m/Y') }}</span><br>
                    <span class="text-muted">{{ $laporan->created_at->format('H:i') }} WIB</span>
                </td>
                <td class="text-center">{{ $laporan->cabang->nama_cabang ?? 'Pusat' }}</td>
                <td class="text-center">{{ $laporan->user->name ?? 'System' }}</td>
                <td class="text-right font-bold">
                    Rp{{ number_format($laporan->total_penjualan, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="footer-total">
                <td colspan="4" class="text-right font-bold">TOTAL KESELURUHAN</td>
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