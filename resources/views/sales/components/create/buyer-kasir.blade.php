<div class="rounded-2xl overflow-hidden shadow transition border bg-white border-yellow-200 hover:shadow-lg mt-4 mb-6">
    <div class="p-3 text-center">
        <!-- Label -->
        <p class="font-semibold text-yellow-800 text-sm mb-1">Nama Pembeli</p>

        <!-- Input -->
        <input type="text" x-model="customer_name" x-on:input="$refs.customer_name_hidden.value = customer_name"
            placeholder="Masukkan nama pembeli"
            class="w-full border border-yellow-300 rounded-xl py-2 px-3 focus:ring-2 focus:ring-yellow-400 focus:outline-none" />

    </div>
</div>
