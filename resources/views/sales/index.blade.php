@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">
                    📄 Daftar Penjualan
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    Cabang:
                    <span class="font-semibold text-gray-800">{{ auth()->user()->branch->name ?? '-' }}</span>
                    · Kasir:
                    <span class="font-semibold text-gray-800">{{ auth()->user()->name ?? '-' }}</span>
                </p>
            </div>

            <a href="{{ route('sales.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl shadow hover:bg-blue-700 transition-all">
                ➕ Tambah Penjualan
            </a>
        </div>

        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="p-3 bg-green-100 border border-green-300 text-green-800 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter + Omzet --}}
        <div class="bg-white shadow rounded-2xl p-5 border border-gray-100">
            <form method="GET" action="{{ route('sales.index') }}">
                <div class="flex flex-col sm:flex-row sm:items-end gap-4">

                    <div>
                        <label class="text-sm text-gray-600 font-medium">Cari Invoice</label>
                        <input type="text" name="invoice" placeholder="Contoh: INV-00123" value="{{ request('invoice') }}"
                            class="border border-gray-300 rounded-xl px-3 py-2 w-full focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600 font-medium">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ request('start_date', now()->format('Y-m-d')) }}"
                            class="border border-gray-300 rounded-xl px-3 py-2 w-full focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600 font-medium">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}"
                            class="border border-gray-300 rounded-xl px-3 py-2 w-full focus:ring-2 focus:ring-blue-400">
                    </div>

                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-xl shadow hover:bg-blue-700 transition-all">
                        Filter
                    </button>

                    {{-- Card Omzet --}}
                    <div
                        class="ml-auto bg-linier-to-br from-yellow-100 to-yellow-50 border border-yellow-300 rounded-2xl px-5 py-3 shadow text-right">
                        <div class="text-sm text-yellow-800 font-medium">Total Penjualan (Omzet)</div>
                        <div class="text-2xl font-extrabold text-yellow-700 leading-tight">
                            Rp {{ number_format($totalOmzet, 0, ',', '.') }}
                        </div>
                    </div>

                </div>
            </form>
        </div>

        {{-- Table --}}
        @include('sales.components.home.table', ['sales' => $sales])
        {{-- Pagination --}}
        <div class="mt-6">
            {{ $sales->links('pagination::tailwind') }}
        </div>

    </div>
@endsection
