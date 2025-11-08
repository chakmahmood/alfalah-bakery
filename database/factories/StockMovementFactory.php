<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Branch;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockMovement>
 */
class StockMovementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'branch_id' => Branch::inRandomOrder()->value('id') ?? 1,
            'product_id' => Product::inRandomOrder()->value('id') ?? 1,
            'type' => $this->faker->randomElement(['in', 'out', 'transfer', 'adjustment', 'production', 'return']),
            'quantity' => $this->faker->randomFloat(2, 1, 100),
            'reference' => strtoupper($this->faker->bothify('TX-####')),
            'note' => $this->faker->sentence(),
        ];
    }
}
