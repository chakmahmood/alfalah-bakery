@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-4 sm:p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Detail Penjualan</h1>
        <div class="flex space-x-2">
            <a href="{{ route('sales.print', $sale->invoice_number) }}" target="_blank"
               class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                Print Struk
            </a>
            <a href="{{ route('sales.edit', $sale) }}"
               class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                Edit
            </a>
            <a href="{{ route('sales.index') }}"
               class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-lg hover:bg-gray-300 transition">
                Kembali
            </a>
        </div>
    </div>

    {{-- Info Utama --}}
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-700">
            <p><span class="font-semibold">Invoice:</span> {{ $sale->invoice_number }}</p>
            <p><span class="font-semibold">Tanggal:</span> {{ $sale->sale_date->format('d M Y') }}</p>
            <p><span class="font-semibold">Cabang:</span> {{ $sale->branch->name ?? '-' }}</p>
            <p><span class="font-semibold">Kasir:</span> {{ $sale->user->name ?? '-' }}</p>
            <p class="sm:col-span-2"><span class="font-semibold">Catatan:</span> {{ $sale->note ?? '-' }}</p>
        </div>
    </div>

    {{-- Daftar Produk --}}
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="font-semibold text-gray-800 text-lg">Produk yang Dibeli</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-gray-700">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-4 py-2 text-left">Produk</th>
                        <th class="px-4 py-2 text-right">Qty</th>
                        <th class="px-4 py-2 text-right">Harga</th>
                        <th class="px-4 py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($sale->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $item->product->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-right">{{ $item->quantity }}</td>
                            <td class="px-4 py-2 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 text-right font-semibold">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Rincian Pembayaran --}}
    @php
        $totalPayment = $sale->payments->sum('amount');
        $changeDue = max(0, $totalPayment - $sale->total);
    @endphp

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="font-semibold text-gray-800 text-lg mb-4">Rincian Pembayaran</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-700">
            <p><span class="font-semibold">Subtotal:</span> Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</p>
            <p><span class="font-semibold">Diskon:</span> Rp {{ number_format($sale->discount, 0, ',', '.') }}</p>
            <p><span class="font-semibold">Pajak:</span> Rp {{ number_format($sale->tax, 0, ',', '.') }}</p>
            <p><span class="font-semibold">Total:</span> <span class="font-bold text-gray-900">Rp {{ number_format($sale->total, 0, ',', '.') }}</span></p>
            <p><span class="font-semibold">Dibayar:</span> Rp {{ number_format($totalPayment, 0, ',', '.') }}</p>
            <p><span class="font-semibold">Kembalian:</span> Rp {{ number_format($changeDue, 0, ',', '.') }}</p>
            <p class="sm:col-span-2">
                <span class="font-semibold">Status:</span>
                @if ($sale->is_fully_paid ?? $sale->status === 'paid')
                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 ml-1">
                        Lunas
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 ml-1">
                        Belum Lunas
                    </span>
                @endif
            </p>
        </div>
    </div>
</div>
@endsection
