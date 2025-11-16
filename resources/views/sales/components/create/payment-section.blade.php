<div class="bg-white/95 rounded-3xl shadow-xl p-6 flex flex-col w-full mt-4 md:mt-6">
    <label class="block text-sm font-semibold text-yellow-800 mb-2">💳 Metode Pembayaran</label>
    <select name="payment_method_id" x-model="payment_method_id"
        class="w-full border border-yellow-300 rounded-xl py-2 px-3 focus:ring-2 focus:ring-yellow-400">
        <option value="">Pilih Metode</option>
        @foreach ($paymentMethods as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
        @endforeach
    </select>

    <div class="mt-4">
        <label class="block text-sm font-semibold text-yellow-800 mb-2">💵 Uang Diterima</label>

        <!-- INPUT -->
        <input type="number" x-model.number="amount_received" name="amount_received"
            class="w-full border border-yellow-300 rounded-xl py-2 px-3 focus:ring-2 focus:ring-yellow-400" />

        <!-- SHORTCUT BUTTONS (MODE +=) -->
        <div class="flex flex-wrap gap-2 mt-3">
            <template x-for="nom in [5000, 20000, 50000, 100000, 200000, 500000]" :key="nom">
                <button type="button"
                    @click="amount_received = (amount_received || 0) + nom"
                    class="px-3 py-2 bg-yellow-100 hover:bg-yellow-200 border border-yellow-300 rounded-xl text-sm font-semibold text-yellow-800 transition">
                    <span x-text="'+' + 'Rp ' + format(nom)"></span>
                </button>
            </template>
        </div>
    </div>

    <template x-if="amount_received > 0">
        <div class="flex justify-between mt-3 text-lg font-bold text-green-700 border-t border-yellow-200 pt-3">
            <span>Kembalian:</span>
            <span>Rp <span x-text="format(change)"></span></span>
        </div>
    </template>

    <div class="mt-3">
        <label class="block text-sm font-semibold text-yellow-800 mb-2">📝 Catatan</label>
        <textarea name="note" x-model="note" rows="2"
            class="w-full border border-yellow-300 rounded-xl py-2 px-3 focus:ring-2 focus:ring-yellow-400"></textarea>
    </div>
</div>
