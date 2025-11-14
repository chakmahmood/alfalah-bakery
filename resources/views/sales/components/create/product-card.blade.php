<div class="rounded-2xl overflow-hidden shadow transition border"
    :class="p.stock_quantity <= 0 ? 'bg-red-100 border-red-300' : 'bg-yellow-50 border-yellow-200 hover:shadow-lg'">

    <img loading="lazy" :src="p.image_url" onerror="this.src='https://via.placeholder.com/150?text=No+Image'"
        class="h-32 w-full object-cover">

    <div class="p-3 text-center">
        <p class="font-semibold text-yellow-800 text-sm truncate" x-text="p.name"></p>
        <p class="text-yellow-600 font-bold text-sm">Rp <span x-text="format(p.sell_price)"></span></p>

        <p class="text-xs text-gray-500">
            <template x-if="p.stock_quantity <= 0">
                <span class="font-bold text-red-600">Habis</span>
            </template>
            <template x-if="p.stock_quantity > 0">
                Stok: <span class="font-semibold text-yellow-700" x-text="p.stock_label"></span>
            </template>
        </p>

        {{-- Counter --}}
        <div class="mt-2 flex justify-center items-center gap-2">
            <button type="button" @click="decrease(p.id)" class="w-7 h-7 rounded-full flex items-center justify-center"
                :class="p.stock_quantity <= 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-yellow-200 hover:bg-yellow-300'"
                :disabled="p.stock_quantity <= 0">–</button>

            <span class="w-5 text-center" x-text="cart[p.id]?.quantity || 0"></span>
            <button type="button" @click="add(p.id, p.name, p.sell_price)"
                class="w-7 h-7 rounded-full flex items-center justify-center font-bold" :class="(p.stock_quantity <= 0 || (cart[p.id]?.quantity >= p.stock_quantity))
        ? 'bg-gray-300 cursor-not-allowed text-gray-500'
        : 'bg-yellow-500 hover:bg-yellow-600 text-white'"
                :disabled="p.stock_quantity <= 0 || (cart[p.id]?.quantity >= p.stock_quantity)">
                +
            </button>

        </div>
    </div>
</div>
