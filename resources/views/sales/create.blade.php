@extends('layouts.app')

@section('content')
    <div x-data="kasirApp()" x-init="init()" class="max-w-7xl mx-auto p-6">

        {{-- Header --}}
        @include('sales.components.create.header-kasir')

        {{-- Form --}}
        <form x-ref="form" @submit.prevent="handleSubmit($event)" method="POST" action="{{ route('sales.store') }}">
            @csrf
            <div class="flex flex-col md:flex-row gap-6">

                {{-- Produk --}}
                <div class="w-full md:w-2/3 bg-white/90 rounded-3xl shadow-xl p-6 backdrop-blur-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-extrabold text-yellow-800">
                            🧁 Pilih Produk
                        </h2>

                        <div class="text-sm text-yellow-700 font-semibold">
                            Ditemukan <span x-text="filteredProducts.length"></span> produk
                        </div>
                    </div>

                    <div class="mb-4">
                        <input type="text" x-model="search" placeholder="Cari produk..."
                            class="w-full border border-yellow-300 rounded-xl py-2 px-3 focus:ring-2 focus:ring-yellow-400"
                            @keydown.enter.prevent />
                    </div>



                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        <template x-for="p in paginatedProducts()" :key="p.id">
                            @include('sales.components.create.product-card')
                        </template>
                    </div>

                    {{-- Pagination --}}
                    @include('sales.components.create.pagination-kasir')
                </div>

                {{-- Ringkasan + Pembayaran --}}
                <div class="w-full md:w-1/3">
                    @include('sales.components.create.cart-summary')
                    @include('sales.components.create.promo-kasir')
                    @include('sales.components.create.payment-section')
                    @include('sales.components.create.buyer-kasir')
                    {{-- Hidden inputs --}}
                    <input type="hidden" name="customer_name">
                    <input type="hidden" name="subtotal">
                    <input type="hidden" name="discount">
                    <input type="hidden" name="tax">
                    <input type="hidden" name="total">
                    <input type="hidden" name="items">
                    <input type="hidden" name="promotion_id">


                    <button type="button" @click="submitForm()"
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
                cart: {},
                subtotal: 0,
                discount: 0,
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
                currentPage: 1,
                perPage: 12,
                customer_name: '',
                promotion_id: '',
                promotion_type: '',
                promotion_value: 0,
                promotions: @json($promotions),


                init() {
                    this.filteredProducts = this.products;
                    this.products.forEach(p => {
                        p.item_discount_type = null;
                        p.item_discount_value = null;
                    });
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);

                    this.$watch('amount_received', () => this.updateChange());
                    this.$watch('search', value => {
                        this.currentPage = 1;
                        this.filterProducts(value);
                    });
                    this.$watch('discount', () => this.updateTotal());
                },

                // ==== Pagination ====
                paginatedProducts() {
                    const start = (this.currentPage - 1) * this.perPage;
                    const end = start + this.perPage;
                    return this.filteredProducts.slice(start, end);
                },

                totalPages() {
                    return Math.ceil(this.filteredProducts.length / this.perPage) || 1;
                },

                visiblePages() {
                    const total = this.totalPages();
                    let start = Math.max(this.currentPage - 2, 1);
                    let end = Math.min(start + 4, total);
                    if (end - start < 4) start = Math.max(end - 4, 1);

                    const pages = [];
                    for (let i = start; i <= end; i++) pages.push(i);
                    return pages;
                },

                nextPage() { if (this.currentPage < this.totalPages()) this.currentPage++; },
                prevPage() { if (this.currentPage > 1) this.currentPage--; },
                goToPage(page) { if (page >= 1 && page <= this.totalPages()) this.currentPage = page; },

                // ==== Helper ====
                updateTime() {
                    const now = new Date();
                    const options = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
                    this.datetime = now.toLocaleDateString('id-ID', options);
                },

                format(n) { return new Intl.NumberFormat('id-ID').format(n); },

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

                    const product = this.products.find(p => p.id === id);

                    // harga final = harga diskon jika ada
                    const finalPrice = this.getDiscountedPrice(product) ?? parseFloat(price);

                    if (!this.cart[id]) {
                        this.cart[id] = {
                            id,
                            name,
                            price: finalPrice,
                            quantity: 0,
                            subtotal: 0
                        };
                    }

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

                    // this.tax = Math.round(this.subtotal * 0.1);

                    // Recalculate promo based on new subtotal
                    if (this.promotion_id) this.applyPromo();

                    this.updateTotal();
                },


                updateTotal() {
                    this.total = this.subtotal - (this.discount || 0) + this.tax;
                    this.updateChange();
                },

                updateChange() {
                    this.change = this.amount_received - this.total;
                },

                applyPromo() {
                    // 1. Reset semua diskon per-produk
                    this.products.forEach(p => {
                        p.item_discount_type = null;
                        p.item_discount_value = null;
                    });

                    // 2. Jika tidak pilih promo → reset global & update
                    if (!this.promotion_id) {
                        this.discount = 0;
                        this.promotion_type = null;
                        this.promotion_value = null;
                        this.updateTotal();
                        return;
                    }

                    // 3. Tetap ambil data dari <option> (UNTUK UI)
                    const opt = document.querySelector(
                        `select[x-model='promotion_id'] option[value='${this.promotion_id}']`
                    );

                    if (opt) {
                        this.promotion_type = opt.dataset.type;
                        this.promotion_value = parseFloat(opt.dataset.value);
                    }

                    // 4. Ambil promo dari "this.promotions" (UNTUK DISKON PRODUK)
                    const promo = this.promotions.find(p => p.id == this.promotion_id);

                    if (promo) {
                        // Apply item-level discount
                        promo.products.forEach(pp => {
                            const target = this.products.find(prod => prod.id == pp.id);
                            if (target) {
                                target.item_discount_type = pp.pivot.discount_type;
                                target.item_discount_value = pp.pivot.discount_value;
                            }
                        });
                    }

                    // 5. Hitung diskon global (percentage / fixed)
                    let type = this.promotion_type;
                    let val = this.promotion_value;

                    if (type === "percentage") {
                        this.discount = Math.round(this.subtotal * (val / 100));
                    } else if (type === "fixed") {
                        this.discount = val;
                    } else {
                        this.discount = 0;
                    }

                    // 6. Update total tampilan
                    this.updateTotal();
                },

                // ---- Diskon Per Produk
                getDiscountedPrice(p) {
                    if (!p.item_discount_type || !p.item_discount_value) return null;

                    const price = parseFloat(p.sell_price);
                    const val = parseFloat(p.item_discount_value);

                    if (p.item_discount_type === 'percentage') {
                        return price - (price * val / 100);
                    }
                    if (p.item_discount_type === 'fixed') {
                        return price - val;
                    }
                    return price;
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
                    f.querySelector('input[name="promotion_id"]').value = this.promotion_id;





                    f.setAttribute('target', '_blank');
                    f.submit();
                    f.removeAttribute('target');

                    setTimeout(() => location.reload(), 500);
                },

                handleSubmit(event) {
                    // cegah submit via enter
                    event.preventDefault();
                }
            }
        }
    </script>
@endsection
