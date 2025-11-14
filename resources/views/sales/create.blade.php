@extends('layouts.app')

@section('content')
    <div x-data="kasirApp()" x-init="init()" class="max-w-7xl mx-auto p-6">

        {{-- Header --}}
        <div
            class="bg-white/90 rounded-3xl shadow-lg p-6 flex flex-col md:flex-row justify-between items-center backdrop-blur-sm border border-yellow-200 mb-6">
            <div>
                <h1 class="text-3xl font-extrabold text-yellow-800 font-[Playfair_Display]">Tambah Penjualan</h1>
                <p class="text-gray-600 mt-1 text-sm">
                    <span class="font-semibold text-yellow-700">Cabang:</span> {{ auth()->user()->branch->name ?? '-' }} |
                    <span class="font-semibold text-yellow-700">Kasir:</span> {{ auth()->user()->name ?? '-' }}
                </p>
            </div>
            <div class="mt-3 md:mt-0 text-right">
                <p x-text="datetime" class="font-semibold text-yellow-800 text-lg"></p>
            </div>
        </div>

        {{-- Form --}}
        <form x-ref="form" @submit.prevent="submitForm()" method="POST" action="{{ route('sales.store') }}">
            @csrf
            <div class="flex flex-col md:flex-row gap-6">

                {{-- Produk --}}
                <div class="w-full md:w-2/3 bg-white/90 rounded-3xl shadow-xl p-6 backdrop-blur-sm">
                    <h2 class="text-2xl font-extrabold text-yellow-800 mb-4">🧁 Pilih Produk</h2>
                    <div class="mb-4">
                        <input type="text" x-model="search" placeholder="Cari produk..."
                            class="w-full border border-yellow-300 rounded-xl py-2 px-3 focus:ring-2 focus:ring-yellow-400" />
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        <template x-for="p in filteredProducts" :key="p.id">
                            @include('sales.components.create.product-card')
                        </template>
                    </div>
                </div>

                {{-- Ringkasan + Pembayaran --}}
                <div class="w-full md:w-1/3">
                    @include('sales.components.create.cart-summary')

                    {{-- Payment section --}}
                    @include('sales.components.create.payment-section')

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Pembeli</label>
                        <input type="text" x-model="customer_name" placeholder="Masukkan nama pembeli"
                            class="w-full border border-yellow-300 rounded-xl py-2 px-3 focus:ring-2 focus:ring-yellow-400" />
                    </div>

                    {{-- Hidden inputs --}}
                    <input type="hidden" name="customer_name">
                    <input type="hidden" name="subtotal">
                    <input type="hidden" name="discount">
                    <input type="hidden" name="tax">
                    <input type="hidden" name="total">
                    <input type="hidden" name="items">

                    <button type="submit"
                        class="w-full mt-5 bg-yellow-600 hover:bg-yellow-700 text-white font-bold text-lg py-3 rounded-xl transition shadow-lg">
                        💰 Bayar & Simpan
                    </button>
                </div>

            </div>
        </form>
    </div>

    <script>
        function kasirApp() {
            return {
                // ==== State ====
                cart: {},
                subtotal: 0,
                discount: 0,           // <--- diskon manual
                tax: 0,
                total: 0,
                payment_method_id: '1',
                amount_received: 0,
                change: 0,
                note: '',
                datetime: '',
                search: '',
                products: @json($products),
                filteredProducts: [],
                customer_name: '',

                // ==== Inisialisasi ====
                init() {
                    this.filteredProducts = this.products;
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);

                    this.$watch('amount_received', () => this.updateChange());
                    this.$watch('search', value => this.filterProducts(value));
                    this.$watch('discount', () => this.updateTotal()); // <-- watch diskon
                },

                // ==== Helper ====
                updateTime() {
                    const now = new Date();
                    const options = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
                    this.datetime = now.toLocaleDateString('id-ID', options);
                },

                format(n) {
                    return new Intl.NumberFormat('id-ID').format(n);
                },

                filterProducts(term) {
                    term = term.toLowerCase();
                    this.filteredProducts = term === ''
                        ? this.products
                        : this.products.filter(p =>
                            p.name.toLowerCase().includes(term) ||
                            (p.sku && p.sku.toLowerCase().includes(term))
                        );
                },

                // ==== Keranjang ====
                add(id, name, price) {
                    if (!this.cart[id]) this.cart[id] = { id, name, price: parseFloat(price), quantity: 0, subtotal: 0 };
                    this.cart[id].quantity++;
                    this.updateCart();
                },

                decrease(id) {
                    if (this.cart[id]) {
                        this.cart[id].quantity--;
                        if (this.cart[id].quantity <= 0) delete this.cart[id];
                        this.updateCart();
                    }
                },

                updateCart() {
                    this.subtotal = Object.values(this.cart).reduce((t, i) => {
                        i.subtotal = i.price * i.quantity;
                        return t + i.subtotal;
                    }, 0);

                    this.tax = Math.round(this.subtotal * 0.1);
                    this.updateTotal(); // <-- total sekarang sudah include diskon
                },

                updateTotal() {
                    this.total = this.subtotal - (this.discount || 0) + this.tax;
                    this.updateChange();
                },

                updateChange() {
                    this.change = this.amount_received - this.total;
                },

                // ==== Submit ====
                submitForm() {
                    if (!this.payment_method_id) { alert('Pilih metode pembayaran!'); return; }
                    if (Object.keys(this.cart).length === 0) { alert('Belum ada item!'); return; }
                    if (this.amount_received < this.total) { alert('Uang diterima kurang dari total!'); return; }

                    const f = this.$refs.form;
                    f.querySelector('input[name="subtotal"]').value = this.subtotal;
                    f.querySelector('input[name="discount"]').value = this.discount;
                    f.querySelector('input[name="tax"]').value = this.tax;
                    f.querySelector('input[name="total"]').value = this.total;
                    f.querySelector('input[name="items"]').value = JSON.stringify(this.cart);
                    f.querySelector('input[name="customer_name"]').value = this.customer_name;

                    f.setAttribute('target', '_blank');
                    f.submit();
                    f.removeAttribute('target');

                    setTimeout(() => location.reload(), 500);
                }
            }
        }

    </script>

@endsection
