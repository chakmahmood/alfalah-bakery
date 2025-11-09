<x-filament-panels::page>
    <div class="mb-4 flex items-center gap-3">
        <label for="date" class="text-sm font-medium">Pilih Tanggal:</label>
        <input
            type="date"
            id="date"
            wire:model.live="date"
            wire:change="$dispatch('refreshTable')"
            class="border rounded-md px-2 py-1"
        >
    </div>

    {{ $this->table }}
</x-filament-panels::page>
