<x-filament-panels::page>
    <div class="space-y-6">

        <h2 class="text-xl font-semibold text-gray-800">Produksi Harian</h2>
        <p class="text-sm text-gray-600">
            Gunakan form ini untuk mencatat produksi harian. Stok produk jadi akan bertambah,
            dan stok bahan baku akan otomatis berkurang sesuai resep.
        </p>

        {{-- Form --}}
        <form wire:submit.prevent="submitProduction" class="space-y-4 max-w-md">
            {{ $this->form }}
            <br>
            <div class="flex items-center space-x-2">
                <x-filament::button type="submit">
                    Proses Produksi
                </x-filament::button>

                <x-filament::button type="button" color="secondary" wire:click="$set('quantity', 1)">
                    Reset Jumlah
                </x-filament::button>
            </div>
        </form>

        {{-- Catatan / helper --}}
        <div class="text-sm text-gray-500">
            <ul class="list-disc ml-5">
                <li>Pastikan stok bahan baku mencukupi sebelum melakukan produksi.</li>
                <li>Jumlah produk jadi akan ditambahkan ke stok cabang yang dipilih.</li>
                <li>Stok bahan baku akan otomatis dikurangi sesuai resep.</li>
            </ul>
        </div>
    </div>
</x-filament-panels::page>
