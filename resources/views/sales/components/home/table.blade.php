<div class="overflow-hidden bg-white shadow rounded-2xl border border-gray-100">
    <table class="min-w-full text-sm text-gray-700">
        <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 text-xs font-semibold uppercase">
            <tr>
                <th class="px-4 py-3 text-left">Invoice</th>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-right">Total</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
            @forelse ($sales as $sale)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $sale->invoice_number }}
                    </td>

                    <td class="px-4 py-3 whitespace-nowrap">
                        {{ $sale->sale_date->format('d M Y') }}
                    </td>

                    <td class="px-4 py-3 text-right font-semibold text-gray-900">
                        Rp {{ number_format($sale->total, 0, ',', '.') }}
                    </td>

                    <td class="px-4 py-3 text-center">
                        @if ($sale->is_fully_paid ?? $sale->status === 'paid')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                Lunas
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                Belum Lunas
                            </span>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-center space-x-2">
                        <a href="{{ route('sales.show', $sale) }}"
                            class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition">
                            Detail
                        </a>

                        <a href="{{ route('sales.edit', $sale) }}"
                            class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">
                            Edit
                        </a>

                        <a href="{{ route('sales.print', $sale->invoice_number) }}" target="_blank"
                            class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition">
                            Print
                        </a>

                        <form x-data action="{{ route('sales.destroy', $sale) }}" method="POST" class="inline"
                            @submit.prevent="if(confirm('Yakin hapus penjualan ini?')) $el.submit()">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition">
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
