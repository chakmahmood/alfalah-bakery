<div class="bg-white/95 rounded-3xl shadow-xl p-6 flex flex-col w-full">
    <h2 class="text-2xl font-extrabold text-yellow-800 mb-4">🧾 Ringkasan Transaksi</h2>

    <template x-if="Object.keys(cart).length === 0">
        <p class="text-gray-500 text-center py-6">Belum ada item.</p>
    </template>

    <div class="flex-1 overflow-y-auto max-h-[40vh] space-y-3">
        <template x-for="(item, id) in cart" :key="id">
            <div class="flex items-center justify-between bg-yellow-50 rounded-lg px-3 py-2">
                <div class="truncate">
                    <p class="font-semibold text-yellow-800 text-sm truncate" x-text="item.name"></p>
                    <p class="text-xs text-gray-500">Rp <span x-text="format(item.price)"></span> × <span x-text="item.quantity"></span></p>
                </div>
                <p class="font-bold text-yellow-700 text-sm whitespace-nowrap">Rp <span x-text="format(item.subtotal)"></span></p>
            </div>
        </template>
    </div>

    {{-- Totals --}}
    <div class="border-t border-yellow-300 mt-4 pt-4 space-y-2 text-sm">
        <div class="flex justify-between">
            <span>Subtotal:</span>
            <span>Rp <span x-text="format(subtotal)"></span></span>
        </div>

        {{-- Diskon --}}
        <div class="flex justify-between items-center">
            <span>Diskon:</span>
            <input type="number" x-model.number="discount"
                   @input="updateTotal()"
                   class="w-20 text-right border border-yellow-300 rounded-lg py-1 px-2 text-sm focus:ring-2 focus:ring-yellow-400"
                   placeholder="0" />
        </div>

        <div class="flex justify-between"><span>Pajak (10%):</span><span>Rp <span x-text="format(tax)"></span></span></div>
        <div class="flex justify-between font-bold text-lg text-yellow-700"><span>Total:</span><span>Rp <span x-text="format(total)"></span></span></div>
    </div>
</div>
