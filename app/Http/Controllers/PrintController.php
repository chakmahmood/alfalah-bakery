<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    /**
     * Tampilkan struk POS untuk penjualan tertentu
     */
    public function printStruk(Sale $sale)
{
    $sale->load(['items.product', 'items.unit', 'payments.paymentMethod', 'branch', 'user']);

    // Total yang dibayarkan pembeli
    $totalPayment = $sale->payments->sum('amount');

    // Kembalian jika bayar lebih dari total tagihan
    $changeDue = max(0, $totalPayment - $sale->total);

    return view('sales.print', compact('sale', 'totalPayment', 'changeDue'));
}

}
