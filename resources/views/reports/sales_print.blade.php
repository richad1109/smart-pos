<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - {{ $storeSetting->store_name ?? 'SmartPOS' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; padding: 25px; color: #1e293b; font-size: 12px; }
        .header { text-align: center; border-bottom: 2px solid #334155; padding-bottom: 12px; margin-bottom: 18px; }
        .header h1 { font-size: 18px; margin-bottom: 4px; text-transform: uppercase; }
        .header p { font-size: 11px; color: #64748b; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 11px; }
        .summary-box { display: flex; gap: 10px; margin-bottom: 18px; }
        .summary-item { flex: 1; border: 1px solid #cbd5e1; padding: 10px; border-radius: 6px; text-align: center; }
        .summary-item .title { font-size: 10px; text-transform: uppercase; color: #64748b; font-weight: bold; }
        .summary-item .value { font-size: 14px; font-weight: bold; color: #0f172a; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 11px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background-color: #f1f5f9; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .no-print { margin-bottom: 15px; }
        .btn { padding: 8px 16px; background: #4f46e5; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: bold; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn">🖨️ Cetak / Simpan PDF</button>
    </div>

    <div class="header">
        <h1>{{ $storeSetting->store_name ?? 'SmartPOS Coffee & Retail' }}</h1>
        <p>{{ $storeSetting->address ?? 'Surabaya' }} &bull; Telp: {{ $storeSetting->phone ?? '-' }}</p>
        <p style="margin-top: 6px; font-weight: bold; color: #0f172a;">LAPORAN REKAPITULASI PENJUALAN</p>
    </div>

    <div class="meta">
        <div><strong>Periode:</strong> {{ $startDate->format('d M Y') }} s/d {{ $endDate->format('d M Y') }}</div>
        <div><strong>Dicetak pada:</strong> {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <div class="title">Total Transaksi</div>
            <div class="value">{{ $orders->where('status', 'completed')->count() }}</div>
        </div>
        <div class="summary-item">
            <div class="title">Total Omzet Bersih</div>
            <div class="value">Rp {{ number_format($totalNetSales, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="title">Estimasi Laba Kotor</div>
            <div class="value" style="color: #16a34a;">Rp {{ number_format($totalGrossProfit, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="title">Pajak Resto</div>
            <div class="value">Rp {{ number_format($totalTaxes, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Invoice</th>
                <th>Tanggal & Waktu</th>
                <th>Kasir</th>
                <th>Pelanggan</th>
                <th class="text-center">Metode</th>
                <th class="text-right">Total Transaksi</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $idx => $order)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td style="font-family: monospace; font-weight: bold;">{{ $order->invoice_number }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $order->user->name ?? 'Kasir' }}</td>
                    <td>{{ $order->customer->name ?? 'Tamu' }}</td>
                    <td class="text-center" style="text-transform: uppercase;">{{ $order->payment_method }}</td>
                    <td class="text-right" style="font-weight: bold;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $order->status === 'completed' ? 'Selesai' : 'Dibatalkan' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
