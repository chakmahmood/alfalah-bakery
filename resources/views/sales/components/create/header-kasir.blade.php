<div
    class="bg-white/90 rounded-3xl shadow-lg p-6 flex flex-col md:flex-row justify-between items-center backdrop-blur-sm border border-yellow-200 mb-6">
    <div>
        <h1 class="text-3xl font-extrabold text-yellow-800">
            <span style="font-family: 'Playfair Display', serif;">Al Falah</span>
            <span style="font-family: 'Pacifico', cursive; color:#D97706;">Bakery</span>
        </h1>



        <p class="text-gray-600 mt-1 text-sm">
            <span class="font-semibold text-yellow-700">Cabang:</span> {{ auth()->user()->branch->name ?? '-' }} |
            <span class="font-semibold text-yellow-700">Kasir:</span> {{ auth()->user()->name ?? '-' }}
        </p>
    </div>
    <div class="mt-3 md:mt-0 text-right">
        <p x-text="datetime" class="font-semibold text-yellow-800 text-lg"></p>
    </div>
</div>
