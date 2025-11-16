<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Branch;
use App\Models\Product;
use App\Models\PaymentMethod;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        // Query dasar
        $query = Sale::where('branch_id', $user->branch_id)
            ->whereDate('sale_date', '>=', $start)
            ->whereDate('sale_date', '<=', $end);

        // Filter by invoice jika ada
        if ($request->invoice) {
            $keyword = str_replace([' ', '-', '.', '_'], '', strtolower($request->invoice));

            $query->whereRaw("
        REPLACE(REPLACE(REPLACE(REPLACE(LOWER(invoice_number), '-', ''), ' ', ''), '.', ''), '_', '')
        LIKE ?
    ", ["%{$keyword}%"]);
        }


        // Hitung total omzet sesuai filter
        $totalOmzet = (clone $query)->sum('total');

        // Ambil data + eager load + paginate
        $sales = $query->with(['user', 'branch'])
            ->orderBy('sale_date', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('sales.index', compact('sales', 'totalOmzet'));
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

        // === Produk ===
        $products = Product::where('is_sellable', true)
            ->whereHas('branches', function ($q) use ($branch) {
                $q->where('branches.id', $branch->id);
            })
            ->with([
                'unit',
                'stocks' => function ($q) use ($branch) {
                    $q->where('branch_id', $branch->id);
                },
                'promotions' // ⬅ TAMBAHKAN INI
            ])
            ->get()
            ->map(function ($product) use ($branch) {

                // stok
                $product->stock_quantity = (int) $product->stockQuantityForBranch($branch->id);
                $product->stock_label = $product->stock_quantity . ' ' . ($product->unit->name ?? '');

                // AMBIL PROMO ITEM (kalau ada)
                if ($product->promotions->isNotEmpty()) {
                    $promo = $product->promotions->first(); // hanya ambil 1 promo dulu
                    $product->item_discount_type = $promo->pivot->discount_type;
                    $product->item_discount_value = $promo->pivot->discount_value;
                } else {
                    $product->item_discount_type = null;
                    $product->item_discount_value = null;
                }

                return $product;
            });


        // === Promo (aktif & sedang berlaku) ===
        $promotions = Promotion::where('is_active', true)
            ->where(function ($q) {
                $q->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
            })
            ->orderBy('type')     // fixed/percentage
            ->orderBy('value', 'desc')
            ->with('products')
            ->get();


        return view('sales.create', compact(
            'branches',
            'paymentMethods',
            'products',
            'promotions',
        ));
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

            // Ambil nama cabang dari user
            $branchName = auth()->user()->branch->code ?? 'CABANG';

            // Bersihkan nama (hilangkan spasi, karakter aneh)
            $branchCode = Str::upper(Str::slug($branchName, ''));

            // Buat invoice baru
            $invoiceNumber = 'INV-' . $branchCode . '-' . now()->format('YmdHis');

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
