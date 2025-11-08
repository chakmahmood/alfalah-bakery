<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use App\Models\Recipe;
use App\Models\Branch;
use App\Services\ProductionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use BackedEnum;
use Exception;

class CreateProduction extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationLabel = 'Produksi Harian';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog';
    protected string $view = 'filament.pages.create-production';

    // Form state
    public ?int $branch_id = null;
    public ?int $recipe_id = null;
    public int $quantity = 0;
    public array $formData = [];

    public function mount(): void
    {
        $this->form->fill([
            'branch_id' => null,
            'recipe_id' => null,
            'quantity' => 0,
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
                ->required()
                ->helperText('Masukkan jumlah produk jadi yang ingin diproduksi'),
        ];
    }

     public function submitProduction(): void
    {
        $data = $this->form->getState();

        try {
            ProductionService::produce(
                recipeId: $data['recipe_id'],
                branchId: $data['branch_id'],
                jumlahProduksi: $data['quantity'],
            );

            Notification::make()
                ->title('Produksi berhasil!')
                ->body("{$data['quantity']} unit produk berhasil diproduksi.")
                ->success()
                ->send();

            // Reset form
            $this->form->fill([
                'branch_id' => null,
                'recipe_id' => null,
                'quantity' => 0,
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
        return Recipe::class; // Hanya untuk Filament binding
    }
}
