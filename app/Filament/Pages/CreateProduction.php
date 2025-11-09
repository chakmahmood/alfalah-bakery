<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use App\Models\Recipe;
use App\Models\Branch;
use App\Services\ProductionService;
use Illuminate\Support\Facades\Validator;
use BackedEnum;
use Exception;

class CreateProduction extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationLabel = 'Produksi Harian';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog';
    protected string $view = 'filament.pages.create-production';

    public ?int $branch_id = null;
    public array $productions = [];

    public function mount(): void
    {
        $this->form->fill([
            'branch_id' => null,
            'productions' => [
                ['recipe_id' => null, 'quantity' => 1],
            ],
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('branch_id')
                ->label('Cabang')
                ->options(Branch::pluck('name', 'id'))
                ->searchable()
                ->required()
                ->placeholder('Pilih cabang'),

            Forms\Components\Repeater::make('productions')
                ->label('Daftar Produksi')
                ->schema([
                    Forms\Components\Select::make('recipe_id')
                        ->label('Resep / Produk')
                        ->options(Recipe::pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->placeholder('Pilih resep'),

                    Forms\Components\TextInput::make('quantity')
                        ->label('Jumlah Produksi')
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->required(),
                ])
                ->columns(2)
                ->defaultItems(1)
                ->reorderable()
                ->createItemButtonLabel('Tambah Produk')
                ->required(),
        ];
    }

    public function submitProduction(): void
    {
        $data = $this->form->getState();

        // Validasi manual untuk duplikat recipe dan input kosong
        $validator = Validator::make($data, [
            'branch_id' => 'required|exists:branches,id',
            'productions' => 'required|array|min:1',
            'productions.*.recipe_id' => 'required|exists:recipes,id',
            'productions.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            Notification::make()
                ->title('Validasi gagal')
                ->body(collect($validator->errors()->all())->join("\n"))
                ->danger()
                ->send();
            return;
        }

        // Cek duplikat recipe
        $recipeIds = collect($data['productions'])->pluck('recipe_id');
        $duplicates = $recipeIds->duplicates();

        if ($duplicates->isNotEmpty()) {
            $dupeNames = Recipe::whereIn('id', $duplicates->unique())->pluck('name')->join(', ');
            Notification::make()
                ->title('Duplikat resep ditemukan')
                ->body("Resep berikut dimasukkan lebih dari sekali: {$dupeNames}. Harap hapus duplikat.")
                ->danger()
                ->send();
            return;
        }

        try {
            foreach ($data['productions'] as $item) {
                ProductionService::produce(
                    recipeId: $item['recipe_id'],
                    branchId: $data['branch_id'],
                    jumlahProduksi: $item['quantity'],
                );
            }

            $total = count($data['productions']);
            Notification::make()
                ->title('Produksi berhasil!')
                ->body("{$total} jenis produk berhasil diproses.")
                ->success()
                ->send();

            // Reset form
            $this->form->fill([
                'branch_id' => null,
                'productions' => [
                    ['recipe_id' => null, 'quantity' => 1],
                ],
            ]);
        } catch (Exception $e) {
            Notification::make()
                ->title('Gagal memproses produksi')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getFormModel(): string
    {
        return Recipe::class;
    }
}
