<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $order->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Courier New', Courier, monospace;
        }
        body {
            background-color: #f1f5f9;
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        .receipt {
            background: #fff;
            width: 320px;
            padding: 20px 15px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            font-size: 12px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .store-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }
        .items-table th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding-bottom: 4px;
        }
        .items-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .no-print {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        .btn {
            flex: 1;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            font-family: sans-serif;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }
        .btn-print { background: #4f46e5; color: #fff; }
        .btn-back { background: #e2e8f0; color: #334155; }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .receipt {
                box-shadow: none;
                width: 100%;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="receipt">
        <!-- Header Toko -->
        <div class="text-center">
            <div class="store-name">{{ $storeSetting->store_name ?? 'SmartPOS Coffee & Retail' }}</div>
            <div>{{ $storeSetting->address ?? 'Surabaya' }}</div>
            <div>Telp: {{ $storeSetting->phone ?? '08123456789' }}</div>
        </div>

        <div class="divider"></div>

        <!-- Metadata Transaksi -->
        <div class="row">
            <span>No: {{ $order->invoice_number }}</span>
            <span>{{ $order->created_at->format('d/m/y H:i') }}</span>
        </div>
        <div class="row">
            <span>Kasir: {{ $order->user->name ?? 'Kasir' }}</span>
            <span>Metode: {{ strtoupper($order->payment_method) }}</span>
        </div>
        @if($order->customer)
            <div class="row">
                <span>Member: {{ $order->customer->name }}</span>
                <span>Poin: +{{ (int) floor($order->grand_total / 10000) }}</span>
            </div>
        @endif

        <div class="divider"></div>

        <!-- Daftar Rincian Barang -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div class="font-bold">{{ $item->product_name }}</div>
                            <div style="font-size: 10px;">@ Rp {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                        </td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Ringkasan Perhitungan Pembayaran -->
        <div class="row">
            <span>Subtotal:</span>
            <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
        </div>

        @if($order->discount_amount > 0)
            <div class="row">
                <span>Diskon:</span>
                <span>-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
            </div>
        @endif

        @if($order->tax_amount > 0)
            <div class="row">
                <span>Pajak ({{ $storeSetting->tax_percentage ?? 0 }}%):</span>
                <span>Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
            </div>
        @endif

        <div class="row font-bold" style="font-size: 13px; margin-top: 4px;">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
        </div>

        <div class="row">
            <span>Tunai/Bayar:</span>
            <span>Rp {{ number_format($order->cash_amount, 0, ',', '.') }}</span>
        </div>

        <div class="row font-bold">
            <span>Kembalian:</span>
            <span>Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span>
        </div>

        <div class="divider"></div>

        <!-- Footer Struk -->
        <div class="text-center" style="font-size: 11px; white-space: pre-line;">
            {{ $storeSetting->receipt_footer ?? "Terima Kasih Atas Kunjungan Anda!\nBarang yang sudah dibeli tidak dapat ditukar." }}
        </div>

        <!-- Tombol Aksi (Tidak Ikut Ter-print) -->
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-print">🖨️ Cetak Struk</button>
            <a href="{{ route('pos.index') }}" class="btn btn-back">&larr; Kembali ke POS</a>
        </div>
    </div>

</body>
</html>
