<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Branch;
use App\Models\Product;
use App\Models\PaymentMethod;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Tampilkan daftar penjualan
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Default tanggal = hari ini
        $start = $request->start_date ?? now()->format('Y-m-d');
        $end = $request->end_date ?? now()->format('Y-m-d');

        $sales = Sale::with(['user', 'branch'])
            ->where('branch_id', $user->branch_id) // filter cabang
            ->whereDate('sale_date', '>=', $start)
            ->whereDate('sale_date', '<=', $end)
            ->orderBy('sale_date', 'desc')
            ->paginate(10)
            ->appends($request->query()); // agar filter tidak hilang saat pagination

        return view('sales.index', compact('sales'));
    }

    /**
     * Form tambah penjualan baru
     */
    public function create()
    {
        $user = auth()->user();
        $branch = $user->branch;

        $branches = Branch::pluck('name', 'id');

        $paymentMethods = PaymentMethod::where('is_active', true)
            ->pluck('name', 'id');

        $products = Product::where('is_sellable', true)
            ->whereHas('branches', function ($q) use ($branch) {
                $q->where('branches.id', $branch->id);
            })
            ->with([
                'unit',
                'stocks' => function ($q) use ($branch) {
                    $q->where('branch_id', $branch->id);
                }
            ])
            ->get()
            ->map(function ($product) use ($branch) {

                // Ambil stok cabang → hilangkan .00
                $product->stock_quantity = (int) $product->stockQuantityForBranch($branch->id);

                // Tambahkan unit ke attribute product
                $product->stock_label = $product->stock_quantity . ' ' . ($product->unit->name ?? '');

                return $product;
            });

        return view('sales.create', compact('branches', 'paymentMethods', 'products'));
    }



    /**
     * Simpan penjualan baru
     */
    public function store(Request $request)
    {
        // Validasi dasar + diskon
        $validated = $request->validate([
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0', // ✅ Tambahkan validasi diskon
            'tax' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount_received' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'items' => 'required|string', // JSON string
        ]);

        try {
            DB::beginTransaction();

            // 🔹 Decode JSON items dari form
            $items = json_decode($validated['items'], true);

            if (empty($items)) {
                throw new \Exception('Tidak ada item yang dikirim.');
            }

            // 🔹 Generate nomor invoice otomatis
            $invoiceNumber = 'INV-' . now()->format('YmdHis');

            // Ambil diskon
            $discount = $validated['discount'] ?? 0;

            // 🔹 Simpan ke tabel sales
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'branch_id' => auth()->user()->branch_id,
                'user_id' => auth()->id(),
                'payment_method_id' => $validated['payment_method_id'],
                'subtotal' => $validated['subtotal'],
                'discount' => $discount,
                'tax' => $validated['tax'] ?? 0,
                'total' => $validated['total'],
                'status' => 'paid',
                'note' => $validated['note'] ?? null,
                'sale_date' => now(),
            ]);

            // 🔹 Simpan ke tabel sale_items
            foreach ($items as $item) {
                $sale->items()->create([
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => 0,       // Bisa ditambahkan per-item jika ada
                    'subtotal' => $item['subtotal'],
                ]);
            }

            // 🔹 Simpan ke tabel sale_payments
            $sale->payments()->create([
                'payment_method_id' => $validated['payment_method_id'],
                'amount' => $validated['amount_received'],
                'reference_number' => null,
                'note' => 'Pembayaran tunai',
                'status' => 'confirmed',
            ]);

            // 🔹 Update stok
            foreach ($sale->items as $item) {
                StockService::move(
                    'out',
                    $sale->branch_id,
                    $item->product_id,
                    $item->quantity,
                    $sale->invoice_number,
                    'Penjualan #' . $sale->invoice_number
                );
            }

            DB::commit();

            $customerName = $request->customer_name ?? '-';

            return redirect()->route('sales.print', $sale->invoice_number)
                ->with([
                    'success' => 'Penjualan berhasil disimpan.',
                    'customer_name' => $customerName,
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan penjualan: ' . $e->getMessage()]);
        }
    }


    /**
     * Tampilkan detail penjualan
     */
    public function show(Sale $sale)
    {
        $sale->load(['items.product.unit', 'branch', 'user', 'payments.paymentMethod']);

        return view('sales.show', compact('sale'));
    }

    /**
     * Form edit penjualan
     */
    public function edit(Sale $sale)
    {
        $user = auth()->user();
        $branch = $user->branch;

        $branches = Branch::pluck('name', 'id');
        $paymentMethods = PaymentMethod::where('is_active', true)->pluck('name', 'id');
        $products = Product::where('is_sellable', true)
            ->whereHas('branches', fn($q) => $q->where('branches.id', $branch->id))
            ->with('unit')
            ->get();

        $sale->load(['items.product', 'payments']);

        return view('sales.edit', compact('sale', 'branches', 'paymentMethods', 'products'));
    }

    /**
     * Update penjualan
     */
    public function update(Request $request, Sale $sale)
    {
        // (opsional — bisa ditambahkan nanti sesuai kebutuhan edit)
    }

    /**
     * Hapus penjualan
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();

        return redirect()->route('sales.index')
            ->with('success', 'Penjualan berhasil dihapus.');
    }
}
