@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-6" x-data>
    <h1 class="text-2xl font-bold mb-6">Edit Penjualan</h1>

    <form action="{{ route('sales.update', $sale) }}" method="POST" class="space-y-6 bg-white shadow p-6 rounded-xl">
        @csrf
        @method('PUT')

        {{-- Informasi Utama --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Invoice</label>
                <input type="text" value="{{ $sale->invoice_number }}" class="w-full border-gray-300 rounded-lg shadow-sm" readonly>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cabang</label>
                <select name="branch_id" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                    @foreach ($branches as $id => $name)
                        <option value="{{ $id }}" @selected($sale->branch_id == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                <select name="payment_method_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                    <option value="">-- Pilih Metode --</option>
                    @foreach ($paymentMethods as $id => $name)
                        <option value="{{ $id }}" @selected($sale->payment_method_id == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Penjualan</label>
                <input type="date" name="sale_date" value="{{ $sale->sale_date->format('Y-m-d') }}" class="w-full border-gray-300 rounded-lg shadow-sm" required>
            </div>
        </div>

        {{-- Detail Nominal --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                <input type="number" name="subtotal" class="w-full border-gray-300 rounded-lg shadow-sm" value="{{ $sale->subtotal }}" step="0.01" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Diskon</label>
                <input type="number" name="discount" class="w-full border-gray-300 rounded-lg shadow-sm" value="{{ $sale->discount }}" step="0.01">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pajak</label>
                <input type="number" name="tax" class="w-full border-gray-300 rounded-lg shadow-sm" value="{{ $sale->tax }}" step="0.01">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total</label>
                <input type="number" name="total" class="w-full border-gray-300 rounded-lg shadow-sm bg-gray-50" value="{{ $sale->total }}" step="0.01" required readonly>
            </div>
        </div>

        {{-- Status dan Catatan --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                    <option value="pending" @selected($sale->status == 'pending')>Pending</option>
                    <option value="paid" @selected($sale->status == 'paid')>Lunas</option>
                    <option value="cancelled" @selected($sale->status == 'cancelled')>Dibatalkan</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <textarea name="note" class="w-full border-gray-300 rounded-lg shadow-sm" rows="2">{{ $sale->note }}</textarea>
            </div>
        </div>

        {{-- Daftar Produk --}}
        <div class="mt-6">
            <h2 class="text-lg font-semibold mb-2">Produk yang Dibeli</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left">Produk</th>
                            <th class="px-3 py-2 text-right">Qty</th>
                            <th class="px-3 py-2 text-right">Harga</th>
                            <th class="px-3 py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->items as $item)
                            <tr class="border-t">
                                <td class="px-3 py-2">{{ $item->product->name ?? '-' }}</td>
                                <td class="px-3 py-2 text-right">{{ $item->quantity }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pembayaran --}}
        <div class="mt-6">
            <h2 class="text-lg font-semibold mb-2">Pembayaran</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2">Metode</th>
                            <th class="px-3 py-2 text-right">Jumlah</th>
                            <th class="px-3 py-2 text-left">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->payments as $payment)
                            <tr class="border-t">
                                <td class="px-3 py-2">{{ $payment->paymentMethod->name ?? '-' }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td class="px-3 py-2">{{ $payment->payment_date->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                        <tr class="font-semibold border-t bg-gray-50">
                            <td class="px-3 py-2 text-right" colspan="1">Total Dibayar</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($sale->payments->sum('amount'), 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                        <tr class="font-semibold border-t">
                            <td class="px-3 py-2 text-right" colspan="1">Kembalian</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format(max(0, $sale->payments->sum('amount') - $sale->total), 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex justify-end space-x-3 mt-6">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg">💾 Simpan</button>
            <a href="{{ route('sales.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-5 py-2 rounded-lg">Batal</a>
        </div>
    </form>
</div>
@endsection
