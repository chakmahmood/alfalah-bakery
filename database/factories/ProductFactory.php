<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $categories = Category::all();
        $units = Unit::all();

        $isSellable = $this->faker->boolean(70); // 70% produk jadi

        if ($isSellable) {
            $name = $this->faker->randomElement([
                'Roti Coklat', 'Roti Keju', 'Roti Butter', 'Roti Kismis',
                'Croissant', 'Donat', 'Brownies', 'Cake Vanilla', 'Cake Coklat'
            ]);
            $unit = $units->where('name', 'Pcs')->first() ?? $units->random();
            $sellPrice = $this->faker->numberBetween(5000, 50000);
            $costPrice = $this->faker->numberBetween(2000, 25000);
            $image = 'seeders/products/' . strtolower(Str::slug($name)) . '.jpg';
        } else {
            $name = $this->faker->randomElement([
                'Tepung Terigu', 'Gula Pasir', 'Ragi', 'Mentega', 'Coklat Bubuk', 'Keju'
            ]);
            $unit = $units->where('name', 'Gram')->first() ?? $units->random();
            $sellPrice = 0;
            $costPrice = $this->faker->numberBetween(1000, 30000);
            $image = null;
        }

        $category = $categories->random();

        return [
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'name' => $name,
            'slug' => Str::slug($name . '-' . Str::random(4)),
            'type' => $isSellable ? 'product' : 'material',
            'is_sellable' => $isSellable,
            'sell_price' => $sellPrice,
            'cost_price' => $costPrice,
            'description' => $this->faker->sentence(8),
            'image' => $image,
            'sku' => strtoupper(Str::random(6)),
            'is_active' => true,
        ];
    }
}
