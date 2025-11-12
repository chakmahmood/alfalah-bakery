<!DOCTYPE html>
<html>

<head>
    <title>Struk POS - {{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: monospace;
            font-size: 12px;
            width: 80mm;
            margin: 0;
            padding: 0;
            padding: 0 5px;
        }

        .center {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 2px 0;
        }

        .right {
            text-align: right;
        }

        .center-col {
            text-align: center;
        }

        hr.dashed {
            border: 0;
            border-top: 1px dashed #000;
            margin: 3px 0;
        }

        .total-table td {
            padding: 2px 0;
        }

        .total-table tr.total-row td {
            border-top: 1px solid #000;
            font-weight: bold;
        }

        .footer {
            margin-top: 3px;
            font-size: 11px;
        }

        .footer-table {
            width: 100%;
            text-align: center;
            margin-top: 2px;
        }

        .footer-table td {
            padding: 0 2px;
        }
    </style>
</head>

<body>
    <!-- HEADER -->
    <div class="center">
        <h2 style="margin:0;">AL FALAH BAKERY</h2>
        <p style="margin:0;">{{ $sale->branch->name ?? '-' }}</p>
        <p style="margin:0 0 2px 0;">{{ $sale->branch->address ?? '-' }}</p>
        <p style="margin:0;">Kasir: {{ $sale->user->name }}</p>
        <p style="margin:0;">Tanggal: {{ $sale->sale_date->format('d-m-Y H:i') }}</p>
        <p style="margin:0 0 2px 0;">Invoice: {{ $sale->invoice_number }}</p>
        <hr class="dashed">
    </div>

    <!-- ITEMS -->
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th class="center-col">Qty</th>
                <th class="right">Harga</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td class="center-col">
                        {{ fmod($item->quantity, 1) == 0 ? number_format($item->quantity, 0) : number_format($item->quantity, 2) }}
                        {{ $item->unit->name ?? '' }}
                    </td>
                    <td class="right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr class="dashed">

    <!-- TOTALS -->
    <table class="total-table">
        <tr>
            <td>Subtotal</td>
            <td class="right">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Diskon</td>
            <td class="right">Rp {{ number_format($sale->discount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Pajak</td>
            <td class="right">Rp {{ number_format($sale->tax, 0, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td>Total</td>
            <td class="right">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total Bayar</td>
            <td class="right">Rp {{ number_format($totalPayment, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembalian</td>
            <td class="right">Rp {{ number_format($changeDue, 0, ',', '.') }}</td>
        </tr>
    </table>

    <hr class="dashed">

    <!-- FOOTER -->
    <div class="center footer">
        Terima kasih atas kunjungannya!<br>
        Barang yang sudah dibeli tidak dapat dikembalikan.<br><br>

        <!-- Baris 1: Situs web -->
        <p style="margin:0;">🌐 www.alfalahbakery.id</p>

        <!-- Baris 2: IG & WA -->
        <table class="footer-table" style="width:100%; margin-top:2px;">
            <tr>
                <td style="text-align:center;">📸 @alfalahbakery</td>
                <td style="text-align:center;">📱 {{ $sale->branch->phone ?? '-' }}</td>
            </tr>
        </table>
    </div>


    <script>
        window.onload = function () {
            window.print();
        }
    </script>
</body>

</html>
