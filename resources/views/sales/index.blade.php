@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-4 sm:p-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Daftar Penjualan</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Cabang: <span class="font-semibold">{{ auth()->user()->branch->name ?? '-' }}</span> |
                    Kasir: <span class="font-semibold">{{ auth()->user()->name ?? '-' }}</span>
                </p>
            </div>
            <a href="{{ route('sales.create') }}"
                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition w-full sm:w-auto text-center">
                + Tambah Penjualan
            </a>
        </div>

        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        {{-- Filter Tanggal --}}
        <form method="GET" action="{{ route('sales.index') }}" class="mb-6">
            <div class="flex flex-col sm:flex-row gap-4 items-end">

                {{-- Tanggal Mulai --}}
                <div>
                    <label class="text-sm text-gray-600">Dari Tanggal</label>
                    <input type="date" name="start_date" class="border rounded-lg px-3 py-2"
                        value="{{ request('start_date', now()->format('Y-m-d')) }}">
                </div>

                {{-- Tanggal Sampai --}}
                <div>
                    <label class="text-sm text-gray-600">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="border rounded-lg px-3 py-2"
                        value="{{ request('end_date', now()->format('Y-m-d')) }}">
                </div>

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Filter
                </button>
            </div>
        </form>

        {{-- Tabel --}}
        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full text-sm text-gray-700">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left">Invoice</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($sales as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $sale->invoice_number }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $sale->sale_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right font-semibold">
                                Rp {{ number_format($sale->total, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($sale->is_fully_paid ?? $sale->status === 'paid')
                                    <span
                                        class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Lunas
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center space-x-1">
                                <a href="{{ route('sales.show', $sale) }}"
                                    class="inline-flex items-center px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition">
                                    Detail
                                </a>

                                <a href="{{ route('sales.edit', $sale) }}"
                                    class="inline-flex items-center px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">
                                    Edit
                                </a>

                                {{-- Tombol Print --}}
                                <a href="{{ route('sales.print', $sale->invoice_number) }}" target="_blank"
                                    class="inline-flex items-center px-3 py-1 text-xs bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition">
                                    Print
                                </a>

                                {{-- Tombol Hapus --}}
                                <form x-data action="{{ route('sales.destroy', $sale) }}" method="POST" class="inline"
                                    @submit.prevent="if (confirm('Yakin hapus penjualan ini?')) $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-1 text-xs bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                Belum ada data penjualan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $sales->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
