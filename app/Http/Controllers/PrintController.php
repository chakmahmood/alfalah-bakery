<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    /**
     * Tampilkan struk POS berdasarkan nomor invoice
     */
    public function printStruk(string $invoice, Request $request)
    {
        $sale = Sale::where('invoice_number', $invoice)
            ->with(['items.product.unit', 'payments.paymentMethod', 'branch', 'user'])
            ->firstOrFail();

        $totalPayment = $sale->payments->sum('amount');
        $changeDue = max(0, $totalPayment - $sale->total);

        // Ambil nama pembeli dari query parameter
        $customerName = session('customer_name', '-');

        return view('sales.print', compact('sale', 'totalPayment', 'changeDue', 'customerName'));
    }

}
