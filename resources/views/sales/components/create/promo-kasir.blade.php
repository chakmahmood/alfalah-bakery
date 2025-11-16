{{-- Promo --}}
<div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-2xl p-4">
    <select x-model="promotion_id" @change="applyPromo()"
        class="w-full border border-yellow-300 rounded-xl py-2 px-3 bg-white/80 focus:ring-2 focus:ring-yellow-400">
        <option value="">Tanpa Promo</option>

        @foreach ($promotions as $promo)
            <option value="{{ $promo->id }}" data-type="{{ $promo->type }}" data-value="{{ $promo->value }}">
                {{ $promo->name }}
                ({{ $promo->type == 'percentage' ? $promo->value . '%' : 'Rp ' . number_format($promo->value) }})
            </option>
        @endforeach
    </select>

    {{-- Tampilkan hasil promo --}}
    <template x-if="promo_discount > 0">
        <div class="mt-2 text-sm text-yellow-700 font-semibold">
            Diskon promo: - Rp <span x-text="format(promo_discount)"></span>
        </div>
    </template>
</div>
