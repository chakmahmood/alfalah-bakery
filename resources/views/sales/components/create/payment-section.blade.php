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
        <input type="number" x-model.number="amount_received" name="amount_received"
            class="w-full border border-yellow-300 rounded-xl py-2 px-3 focus:ring-2 focus:ring-yellow-400" />
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
